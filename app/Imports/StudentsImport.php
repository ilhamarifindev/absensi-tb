<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\ClassRoom;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Exception;

class StudentsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected $targetClass;

    public function __construct($targetClass = null)
    {
        $this->targetClass = $targetClass;
    }

    public function collection(Collection $rows)
    {
        // 1. Get all valid classes
        $validClasses = ClassRoom::pluck('name')->toArray();
        $errors = [];

        // 2. Pre-validate all rows
        foreach ($rows as $index => $row) {
            // Check if required columns exist
            if (!isset($row['nis']) || !isset($row['nama']) || !isset($row['kelas'])) {
                continue; // Skip malformed rows entirely
            }

            $kelas = $row['kelas'];
            $nis = $row['nis'];

            // Validate against target class (if importing inside a specific class view)
            if ($this->targetClass && $kelas !== $this->targetClass) {
                $errors[] = "Baris " . ($index + 2) . ": Anda sedang mengimport untuk kelas {$this->targetClass}, tetapi ditemukan kelas '{$kelas}' untuk NIS {$nis}.";
                continue;
            }

            // Validate class name
            if (!in_array($kelas, $validClasses)) {
                $errors[] = "Baris " . ($index + 2) . ": Kelas '{$kelas}' salah tulis (typo) atau belum terdaftar untuk NIS {$nis}.";
            }
        }

        // 3. If there are any errors, abort the entire import and throw exception
        if (count($errors) > 0) {
            throw new Exception("Import digagalkan karena ada kesalahan penulisan kelas:\n" . implode("\n", $errors));
        }

        // 4. If all validations pass, insert the data
        foreach ($rows as $row) {
            if (!isset($row['nis']) || !isset($row['nama']) || !isset($row['kelas'])) continue;

            // Skip existing NIS
            if (Student::where('nis', $row['nis'])->exists()) {
                continue;
            }

            Student::create([
                'nis'          => $row['nis'],
                'name'         => $row['nama'],
                'class_name'   => $row['kelas'],
                'qr_code'      => 'QR-' . $row['nis'],
                'parent_phone' => $row['no_telp_orang_tua'] ?? null,
            ]);
        }
    }
}
