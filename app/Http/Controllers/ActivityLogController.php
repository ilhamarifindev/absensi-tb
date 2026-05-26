<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Attendance::with('student')
            ->whereDate('scanned_at', $date)
            ->orderBy('scanned_at', 'desc');

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('nis', 'ilike', "%{$search}%")
                  ->orWhere('class_name', 'ilike', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $logs = $query->paginate(20)->appends($request->query());

        // Stats for the selected date
        $allToday = Attendance::whereDate('scanned_at', $date)->get();
        $stats = [
            'total' => $allToday->count(),
            'hadir' => $allToday->where('status', 'hadir')->count(),
            'terlambat' => $allToday->where('status', 'terlambat')->count(),
            'pulang' => $allToday->where('status', 'pulang')->count(),
            'izin' => $allToday->where('status', 'izin')->count(),
            'sakit' => $allToday->where('status', 'sakit')->count(),
        ];

        return view('pages.logs', compact('logs', 'date', 'search', 'status', 'stats'));
    }
}
