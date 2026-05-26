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

        // Rekaman terbaru (5 terakhir digroup by siswa)
        $recentAttendancesRaw = Attendance::with('student')
            ->whereDate('scanned_at', $today)
            ->orderBy('scanned_at', 'asc')
            ->get();

        $recentAttendances = $recentAttendancesRaw->groupBy('student_id')->map(function ($records) {
            $masuk = $records->whereIn('status', ['hadir', 'terlambat'])->first();
            $pulang = $records->where('status', 'pulang')->last();
            $other = $records->whereIn('status', ['izin', 'sakit', 'alpha'])->first();

            $status = $pulang ? 'pulang' : ($other ? $other->status : ($masuk ? $masuk->status : 'alpha'));

            return (object) [
                'student' => $records->first()->student,
                'masuk' => $masuk ? $masuk->scanned_at : ($other ? $other->scanned_at : null),
                'pulang' => $pulang ? $pulang->scanned_at : null,
                'status' => $status,
                'last_scan' => $records->last()->scanned_at
            ];
        })->sortByDesc('last_scan')->take(5);

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
