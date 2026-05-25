<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Statistik Total
        $totalStudents = Student::count();
        
        $attendancesToday = Attendance::whereDate('scanned_at', $today)->get();
        
        $hadir = $attendancesToday->where('status', 'hadir')->count();
        $izin = $attendancesToday->where('status', 'izin')->count();
        $sakit = $attendancesToday->where('status', 'sakit')->count();
        $alpha = $totalStudents - ($hadir + $izin + $sakit);

        // Rekaman terbaru (5 terakhir)
        $recentAttendances = Attendance::with('student')
            ->whereDate('scanned_at', $today)
            ->orderBy('scanned_at', 'desc')
            ->take(5)
            ->get();

        // Untuk chart line seminggu terakhir (dummy/simplified untuk UI)
        // Idealnya query group by date, tapi sementara passing data statis atau agregasi sederhana
        $weeklyData = [
            'hadir' => [1150, 1175, 1160, 1180, $hadir],
            'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum']
        ];

        return view('pages.dashboard', compact(
            'totalStudents', 'hadir', 'izin', 'sakit', 'alpha', 'recentAttendances', 'weeklyData', 'today'
        ));
    }
}
