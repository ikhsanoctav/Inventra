<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DataRequest;
use App\Models\User;
use App\Notifications\GudangRequestNotification;
use App\Notifications\GudangRequestReviewedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GudangRequestController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $status = $request->get('status', 'Menunggu');
            $requests = DataRequest::with(['user', 'reviewer'])
                ->where('status', $status)
                ->latest()
                ->get()
                ->map(function ($req) {
                    return [
                        'id' => $req->id,
                        'request_number' => $req->request_number,
                        'type' => $req->type,
                        'title' => $req->title,
                        'description' => $req->description,
                        'status' => $req->status,
                        'review_note' => $req->review_note,
                        'reviewed_at' => $req->reviewed_at ? $req->reviewed_at->format('d M Y H:i') : null,
                        'reviewer_name' => $req->reviewer->name ?? 'Manajer',
                        'created_at' => $req->created_at->format('d M Y H:i'),
                        'time_ago' => $req->created_at->diffForHumans(),
                        'user_name' => $req->user->name ?? 'Admin Gudang',
                        'user' => [
                            'name' => $req->user->name ?? 'Unknown',
                        ],
                    ];
                });

            $counts = [
                'Menunggu' => DataRequest::where('status', 'Menunggu')->count(),
                'Disetujui' => DataRequest::where('status', 'Disetujui')->count(),
                'Ditolak' => DataRequest::where('status', 'Ditolak')->count(),
            ];

            return response()->json([
                'requests' => $requests,
                'counts' => $counts,
            ]);
        }

        return view('gudang.requests');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $nextId = (DataRequest::max('id') ?? 0) + 1;
        $number = 'REQ-'.date('Ym').'-'.str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $dataRequest = DataRequest::create([
            'user_id' => Auth::id(),
            'request_number' => $number,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'Menunggu',
        ]);

        AuditLog::record('GUDANG_REQUEST_CREATED', 'DataRequest', $dataRequest->id, null, [
            'request_number' => $number,
            'title' => $validated['title'],
            'type' => $validated['type'],
        ]);

        // Notify roles about new request
        $usersToNotify = User::whereIn('role', ['admin_gudang', 'manager', 'super_admin'])->get();
        foreach ($usersToNotify as $user) {
            $user->notify(new GudangRequestNotification($dataRequest));
        }

        return response()->json(['message' => 'Request created successfully']);
    }

    /**
     * Approve a warehouse data request (Manager / Super Admin).
     */
    public function approve(Request $request, $id)
    {
        $dataRequest = DataRequest::findOrFail($id);

        if ($dataRequest->status === 'Disetujui') {
            $msg = "Permintaan {$dataRequest->request_number} sudah disetujui sebelumnya.";
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg]);
            }

            return redirect()->back()->with('success', $msg);
        }

        $note = $request->input('review_note');
        $affected = DataRequest::where('id', $dataRequest->id)
            ->where('status', '!=', 'Disetujui')
            ->update([
                'status' => 'Disetujui',
                'reviewed_by' => Auth::id(),
                'review_note' => $note,
                'reviewed_at' => now(),
            ]);

        if ($affected === 0) {
            $msg = "Permintaan {$dataRequest->request_number} sudah disetujui sebelumnya.";
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg]);
            }

            return redirect()->back()->with('success', $msg);
        }

        $dataRequest->refresh();

        AuditLog::record('GUDANG_REQUEST_APPROVED', 'DataRequest', $dataRequest->id, null, [
            'request_number' => $dataRequest->request_number,
            'approved_by' => Auth::user()->name,
            'note' => $note,
        ]);

        if ($dataRequest->user) {
            $dataRequest->user->notify(new GudangRequestReviewedNotification($dataRequest, 'Disetujui', $note));
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => "Permintaan {$dataRequest->request_number} berhasil disetujui."]);
        }

        return redirect()->back()->with('success', "Permintaan {$dataRequest->request_number} berhasil disetujui.");
    }

    /**
     * Reject a warehouse data request (Manager / Super Admin).
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $dataRequest = DataRequest::findOrFail($id);

        $reason = $request->input('reason', 'Tidak memenuhi kriteria / ditolak manajer.');
        $dataRequest->update([
            'status' => 'Ditolak',
            'reviewed_by' => Auth::id(),
            'review_note' => $reason,
            'reviewed_at' => now(),
        ]);

        AuditLog::record('GUDANG_REQUEST_REJECTED', 'DataRequest', $dataRequest->id, null, [
            'request_number' => $dataRequest->request_number,
            'rejected_by' => Auth::user()->name,
            'reason' => $reason,
        ]);

        if ($dataRequest->user) {
            $dataRequest->user->notify(new GudangRequestReviewedNotification($dataRequest, 'Ditolak', $reason));
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => "Permintaan {$dataRequest->request_number} ditolak."]);
        }

        return redirect()->back()->with('success', "Permintaan {$dataRequest->request_number} berhasil ditolak.");
    }
}
