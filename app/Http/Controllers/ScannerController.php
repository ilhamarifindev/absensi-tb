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

    public function launchScanner()
    {
        // Gunakan VBScript untuk menjalankan Python secara tersembunyi (tanpa memunculkan console)
        $scriptPath = base_path('scanner.py');
        $vbsPath = base_path('launch_hidden.vbs');
        
        $vbsCode = "Set WshShell = CreateObject(\"WScript.Shell\")\n" .
                   "WshShell.Run \"cmd /c python \"\"\" & WScript.Arguments(0) & \"\"\" > NUL 2>&1\", 0, False";
        file_put_contents($vbsPath, $vbsCode);
        
        pclose(popen('wscript "' . $vbsPath . '" "' . $scriptPath . '"', 'r'));
        
        return response()->json(['success' => true, 'message' => 'Scanner Python diluncurkan!']);
    }

    public function scan(Request $request)
    {
        if ($request->header('X-API-Key') !== env('SCANNER_API_KEY', 'default_secret_key')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized API Key'
            ], 401);
        }

        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $student = Student::where('qr_code', $request->qr_code)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak dikenali atau siswa tidak ditemukan.'
            ], 200);
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
            ], 200);
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

    public function scanOut(Request $request)
    {
        if ($request->header('X-API-Key') !== env('SCANNER_API_KEY', 'default_secret_key')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized API Key'
            ], 401);
        }

        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $student = Student::where('qr_code', $request->qr_code)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak dikenali atau siswa tidak ditemukan.'
            ], 200);
        }

        $today = Carbon::today();

        // Pastikan sudah absen masuk (hadir / terlambat) hari ini
        $attendanceIn = Attendance::where('student_id', $student->id)
                                    ->whereDate('scanned_at', $today)
                                    ->whereIn('status', ['hadir', 'terlambat'])
                                    ->first();

        if (!$attendanceIn) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa belum melakukan absensi masuk hari ini.'
            ], 200);
        }

        // Cek apakah sudah absen pulang
        $alreadyOut = Attendance::where('student_id', $student->id)
                                    ->whereDate('scanned_at', $today)
                                    ->where('status', 'pulang')
                                    ->exists();

        if ($alreadyOut) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah melakukan absensi pulang hari ini.'
            ], 200);
        }

        $now = Carbon::now();
        $attendanceOut = Attendance::create([
            'student_id' => $student->id,
            'status' => 'pulang',
            'scanned_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi pulang berhasil dicatat.',
            'data' => [
                'student' => $student,
                'attendance' => $attendanceOut,
                'time' => $now->format('h:i A')
            ]
        ]);
    }
}
