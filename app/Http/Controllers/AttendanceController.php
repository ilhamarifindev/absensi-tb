<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $selectedClass = $request->input('class');
        $search = $request->input('search');

        // Mode 1: Detailed View for a Specific Class
        if ($selectedClass) {
            $query = Student::where('class_name', $selectedClass)->with(['attendances' => function ($q) use ($date) {
                $q->whereDate('scanned_at', $date);
            }]);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                      ->orWhere('nis', 'ilike', "%{$search}%");
                });
            }

            $students = $query->orderBy('name')->get(); // Get all students in the class without pagination for easier overview
            
            return view('pages.attendances', compact('students', 'date', 'selectedClass'));
        }

        // Mode 2: Overview of All Classes
        $classRooms = \App\Models\ClassRoom::orderBy('name')->get();
        
        $classStats = [];
        
        foreach ($classRooms as $room) {
            $totalStudents = Student::where('class_name', $room->name)->count();
            
            if ($totalStudents === 0) continue; // Skip classes with no students

            // Get attendances for this class on this date
            $attendances = Attendance::whereHas('student', function($q) use ($room) {
                $q->where('class_name', $room->name);
            })->whereDate('scanned_at', $date)->get();
            
            $hadir = $attendances->where('status', 'hadir')->count();
            $terlambat = $attendances->where('status', 'terlambat')->count();
            $izin = $attendances->where('status', 'izin')->count();
            $sakit = $attendances->where('status', 'sakit')->count();
            $alphaRecorded = $attendances->where('status', 'alpha')->count();
            
            // Alpha is explicitly recorded alpha + students who haven't scanned
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
