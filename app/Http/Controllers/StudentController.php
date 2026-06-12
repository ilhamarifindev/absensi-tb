<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get all class rooms as objects so we have homeroom_teacher too
        $classRooms = \App\Models\ClassRoom::orderBy('name')->get();
        $classes = $classRooms->pluck('name'); // still need plain names for filter buttons
        $selectedClass = $request->input('class');

        $query = Student::query();

        // Filter by class (required to show students)
        if ($selectedClass) {
            $query->where('class_name', $selectedClass);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Only paginate if a class is selected, otherwise return empty
        if ($selectedClass) {
            $students = $query->orderBy('name')->paginate(15)->withQueryString();
        } else {
            $students = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        }

        return view('pages.students', compact('students', 'classes', 'classRooms', 'selectedClass'));
    }

    /**
     * Store a new class room.
     */
    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:class_rooms,name|max:100',
            'homeroom_teacher' => 'nullable|string|max:255',
        ]);

        \App\Models\ClassRoom::create($validated);

        return redirect()->route('students.index')->with('success', 'Kelas baru berhasil ditambahkan.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|unique:students,nis',
            'name' => 'required|string|max:255',
            'class_name' => 'required|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
        ]);

        // Auto generate QR code value
        $validated['qr_code'] = 'QR-' . $validated['nis'];

        Student::create($validated);

        $selectedClass = $request->input('class_name');
        return redirect()->route('students.index', ['class' => $selectedClass])->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nis' => 'required|string|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'class_name' => 'required|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
        ]);

        $validated['qr_code'] = 'QR-' . $validated['nis'];

        $student->update($validated);

        $selectedClass = $request->input('class_name');
        return redirect()->route('students.index', ['class' => $selectedClass])->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Download/View QR Code for a student.
     */
    public function downloadQr(Student $student)
    {
        // We use simple-qrcode to generate a PNG or SVG.
        // Let's generate an SVG string
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)
            ->margin(2)
            ->generate($student->qr_code);

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Import students from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // max 10MB
            'target_class' => 'nullable|string',
        ]);

        try {
            $targetClass = $request->input('target_class');
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\StudentsImport($targetClass), $request->file('file'));

            $msg = 'Data siswa berhasil diimport. NIS yang duplikat dilewati.';
            if ($targetClass) {
                $msg = "Data siswa untuk kelas {$targetClass} berhasil diimport.";
                return redirect()->route('students.index', ['class' => $targetClass])->with('success', $msg);
            }

            return redirect()->route('students.index')->with('success', $msg);
        } catch (\Exception $e) {
            $redirect = redirect()->route('students.index');
            if ($request->input('target_class')) {
                $redirect = redirect()->route('students.index', ['class' => $request->input('target_class')]);
            }
            return $redirect->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for import.
     */
    public function downloadTemplate()
    {
        $export = new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function array(): array
            {
                return [
                    ['22231001', 'Budi Santoso', 'XI RPL 1', '08123456789'],
                    ['22231002', 'Siti Aminah', 'XI TKJ 2', '08987654321'],
                ];
            }

            public function headings(): array
            {
                return ['NIS', 'NAMA', 'KELAS', 'NO TELP ORANG TUA'];
            }
        };

        return \Maatwebsite\Excel\Facades\Excel::download($export, 'template-siswa.xlsx');
    }
}
