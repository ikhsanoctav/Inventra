<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DataRequest;
use App\Models\User;
use App\Notifications\GudangRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GudangRequestController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $status = $request->get('status', 'Menunggu');
            $requests = DataRequest::with('user')
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
                        'created_at' => $req->created_at->format('d M Y H:i'),
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
}
