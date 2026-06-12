<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $selectedClass = $request->input('class');
        $search = $request->input('search');

        // Mode 1: Detailed View for a Specific Class
        if ($selectedClass) {
            $query = Student::where('class_name', $selectedClass)->with([
                'attendances' => function ($q) use ($date) {
                    $q->whereDate('scanned_at', $date);
                }
            ]);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            }

            $students = $query->orderBy('name')->get();

            return view('pages.attendances', compact('students', 'date', 'selectedClass'));
        }

        // Mode 2: Overview of All Classes (OPTIMIZED - minimal queries)
        $classRooms = \App\Models\ClassRoom::orderBy('name')->get();
        $classNames = $classRooms->pluck('name')->toArray();

        // Query 1: Get student counts per class in ONE query (cached 30s)
        $studentCounts = Cache::remember('student_counts_by_class', 30, function () use ($classNames) {
            return Student::selectRaw('class_name, COUNT(*) as total')
                ->whereIn('class_name', $classNames)
                ->groupBy('class_name')
                ->pluck('total', 'class_name')
                ->toArray();
        });

        // Query 2: Get attendance stats per class in ONE query (cached 30s)
        $cacheKey = 'attendance_class_stats_' . $date;
        $attendanceStats = Cache::remember($cacheKey, 30, function () use ($classNames, $date) {
            return Attendance::selectRaw("
                    students.class_name,
                    COUNT(CASE WHEN attendances.status = 'hadir' THEN 1 END) as hadir,
                    COUNT(CASE WHEN attendances.status = 'terlambat' THEN 1 END) as terlambat,
                    COUNT(CASE WHEN attendances.status = 'izin' THEN 1 END) as izin,
                    COUNT(CASE WHEN attendances.status = 'sakit' THEN 1 END) as sakit
                ")
                ->join('students', 'attendances.student_id', '=', 'students.id')
                ->whereIn('students.class_name', $classNames)
                ->whereDate('attendances.scanned_at', $date)
                ->groupBy('students.class_name')
                ->get()
                ->keyBy('class_name')
                ->toArray();
        });

        // Build stats from in-memory data (no more queries)
        $classStats = [];
        foreach ($classRooms as $room) {
            $totalStudents = $studentCounts[$room->name] ?? 0;
            if ($totalStudents === 0)
                continue;

            $stats = $attendanceStats[$room->name] ?? [
                'hadir' => 0,
                'terlambat' => 0,
                'izin' => 0,
                'sakit' => 0
            ];

            $hadir = (int) ($stats['hadir'] ?? 0);
            $terlambat = (int) ($stats['terlambat'] ?? 0);
            $izin = (int) ($stats['izin'] ?? 0);
            $sakit = (int) ($stats['sakit'] ?? 0);
            $alpha = $totalStudents - ($hadir + $terlambat + $izin + $sakit);

            $classStats[] = [
                'name' => $room->name,
                'homeroom_teacher' => $room->homeroom_teacher,
                'total_students' => $totalStudents,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
            ];
        }

        return view('pages.attendances', compact('date', 'selectedClass', 'classStats'));
    }

    /**
     * Manually update a student's attendance status.
     */
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpha,terlambat',
        ]);

        $studentId = $validated['student_id'];
        $date = Carbon::parse($validated['date']);
        $status = $validated['status'];

        $attendance = Attendance::where('student_id', $studentId)
            ->whereDate('scanned_at', $date)
            ->first();

        if ($status === 'alpha') {
            // If marked alpha, we could just delete the record, or explicitly set status to 'alpha'
            if ($attendance) {
                $attendance->update(['status' => 'alpha']);
            } else {
                Attendance::create([
                    'student_id' => $studentId,
                    'status' => 'alpha',
                    'scanned_at' => $date->copy()->setHour(7), // dummy time
                ]);
            }
        } else {
            if ($attendance) {
                $attendance->update(['status' => $status]);
            } else {
                // Determine a realistic time based on status if we are creating it
                $time = $date->copy();
                if ($status === 'hadir') {
                    $time->setHour(6)->setMinute(45);
                } elseif ($status === 'terlambat') {
                    $time->setHour(7)->setMinute(30);
                } else {
                    $time->setHour(7); // Izin / Sakit
                }

                Attendance::create([
                    'student_id' => $studentId,
                    'status' => $status,
                    'scanned_at' => $time,
                ]);
            }
        }

        return back()->with('success', 'Status absensi berhasil diperbarui.');
    }
}
