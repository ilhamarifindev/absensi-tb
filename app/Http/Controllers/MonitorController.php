<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function index()
    {
        return view('pages.monitor');
    }

    public function fetchLatest()
    {
        $today = Carbon::today();
        
        $attendances = Attendance::with('student')
            ->whereDate('scanned_at', $today)
            ->orderBy('scanned_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'name' => $attendance->student->name,
                    'nis' => $attendance->student->nis,
                    'class_name' => $attendance->student->class_name,
                    'status' => $attendance->status,
                    'time' => Carbon::parse($attendance->scanned_at)->format('H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $attendances
        ]);
    }
}
