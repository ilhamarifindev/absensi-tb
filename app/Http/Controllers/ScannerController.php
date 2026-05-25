<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index()
    {
        return view('pages.scanner');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $student = Student::where('qr_code', $request->qr_code)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak dikenali atau siswa tidak ditemukan.'
            ], 404);
        }

        $today = Carbon::today();
        
        // Cek apakah sudah absen hari ini
        $alreadyScanned = Attendance::where('student_id', $student->id)
                                    ->whereDate('scanned_at', $today)
                                    ->exists();

        if ($alreadyScanned) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah melakukan absensi hari ini.'
            ], 400);
        }

        // Tentukan status kehadiran (contoh batas waktu 07:15)
        $now = Carbon::now();
        $status = 'hadir';
        if ($now->format('H:i:s') > '07:15:00') {
            $status = 'terlambat';
        }

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'status' => $status,
            'scanned_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat.',
            'data' => [
                'student' => $student,
                'attendance' => $attendance,
                'time' => $now->format('h:i A')
            ]
        ]);
    }
}
