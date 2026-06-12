<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Statistik Total (OPTIMIZED - aggregate query + cache 30 detik)
        $totalStudents = Cache::remember('total_students', 30, fn() => Student::count());

        // Satu query untuk hitung semua status sekaligus (cached 30 detik)
        $todayKey = 'attendance_stats_' . $today->toDateString();
        $statusCounts = Cache::remember($todayKey, 30, function () use ($today) {
            return Attendance::selectRaw("
                    COUNT(CASE WHEN status = 'hadir' THEN 1 END) as hadir,
                    COUNT(CASE WHEN status = 'izin' THEN 1 END) as izin,
                    COUNT(CASE WHEN status = 'sakit' THEN 1 END) as sakit,
                    COUNT(CASE WHEN status = 'terlambat' THEN 1 END) as terlambat
                ")
                ->whereDate('scanned_at', $today)
                ->first();
        });

        $hadir = (int) ($statusCounts->hadir ?? 0);
        $izin = (int) ($statusCounts->izin ?? 0);
        $sakit = (int) ($statusCounts->sakit ?? 0);
        $terlambat = (int) ($statusCounts->terlambat ?? 0);
        $alpha = max(0, $totalStudents - ($hadir + $izin + $sakit + $terlambat));

        // Rekaman terbaru (OPTIMIZED - limit di DB, bukan load semua lalu groupBy di PHP)
        $recentAttendances = Attendance::with('student')
            ->whereDate('scanned_at', $today)
            ->orderBy('scanned_at', 'desc')
            ->limit(20)
            ->get()
            ->groupBy('student_id')
            ->map(function ($records) {
                $masuk = $records->whereIn('status', ['hadir', 'terlambat'])->first();
                $pulang = $records->where('status', 'pulang')->first();
                $other = $records->whereIn('status', ['izin', 'sakit', 'alpha'])->first();

                $status = $pulang ? 'pulang' : ($other ? $other->status : ($masuk ? $masuk->status : 'alpha'));

                return (object) [
                    'student' => $records->first()->student,
                    'masuk' => $masuk ? $masuk->scanned_at : ($other ? $other->scanned_at : null),
                    'pulang' => $pulang ? $pulang->scanned_at : null,
                    'status' => $status,
                    'last_scan' => $records->last()->scanned_at
                ];
            })
            ->sortByDesc('last_scan')
            ->take(5);

        // Untuk chart line seminggu terakhir
        $weeklyData = [
            'hadir' => [1150, 1175, 1160, 1180, $hadir],
            'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum']
        ];

        return view('pages.dashboard', compact(
            'totalStudents',
            'hadir',
            'izin',
            'sakit',
            'alpha',
            'recentAttendances',
            'weeklyData',
            'today'
        ));
    }
}
