<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSchedule;
use App\Models\WorkShift;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $users = User::where('is_active', true)->orderBy('name')->get();
        $shifts = WorkShift::all();

        // Get schedules for this month
        $schedules = UserSchedule::with('workShift')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy('user_id');

        return view('system.schedules.index', compact('users', 'shifts', 'schedules', 'month', 'startOfMonth', 'endOfMonth'));
    }

    public function shifts()
    {
        $shifts = WorkShift::all();

        return view('system.schedules.shifts', compact('shifts'));
    }

    public function storeShift(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'color_hex' => 'required|string|max:7',
        ]);

        WorkShift::create($validated);

        return redirect()->back()->with('success', 'Master Shift berhasil ditambahkan.');
    }

    public function updateShift(Request $request, $id)
    {
        $shift = WorkShift::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'color_hex' => 'required|string|max:7',
        ]);

        $shift->update($validated);

        return redirect()->back()->with('success', 'Master Shift berhasil diperbarui.');
    }

    public function destroyShift($id)
    {
        $shift = WorkShift::findOrFail($id);
        $shift->delete();

        return redirect()->back()->with('success', 'Master Shift berhasil dihapus.');
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'work_shift_id' => 'required|exists:work_shifts,id',
            'date' => 'required|date',
        ]);

        UserSchedule::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'date' => $request->date,
            ],
            [
                'work_shift_id' => $request->work_shift_id,
                'status' => 'scheduled',
            ]
        );

        return redirect()->back()->with('success', 'Jadwal berhasil ditugaskan.');
    }

    public function destroySchedule($id)
    {
        $schedule = UserSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
