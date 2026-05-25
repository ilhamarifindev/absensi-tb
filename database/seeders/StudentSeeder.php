<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data Dummy Siswa
        $students = [
            ['nis' => '22231001', 'name' => 'Budi Santoso', 'class_name' => 'XI RPL 1', 'qr_code' => 'QR-22231001'],
            ['nis' => '22231045', 'name' => 'Siti Aminah', 'class_name' => 'XI TKJ 2', 'qr_code' => 'QR-22231045'],
            ['nis' => '22231012', 'name' => 'Andi Pratama', 'class_name' => 'XI MM 1', 'qr_code' => 'QR-22231012'],
            ['nis' => '22231088', 'name' => 'Dewi Sartika', 'class_name' => 'XI AKL 2', 'qr_code' => 'QR-22231088'],
            ['nis' => '22231090', 'name' => 'Joko Anwar', 'class_name' => 'XI RPL 1', 'qr_code' => 'QR-22231090'],
        ];

        foreach ($students as $data) {
            $student = Student::create($data);
            
            // Buat record absen untuk hari ini sebagai contoh
            $statusOptions = ['hadir', 'hadir', 'hadir', 'terlambat', 'izin', 'sakit'];
            $status = $statusOptions[array_rand($statusOptions)];
            
            Attendance::create([
                'student_id' => $student->id,
                'status' => $status,
                'scanned_at' => Carbon::today()->addHours(6)->addMinutes(rand(10, 50)),
            ]);
        }
    }
}
