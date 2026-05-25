@extends('layouts.app')

@section('title', 'Rekap Absensi | SMK Taruna Bangsa')

@section('content')
    @include('components.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 w-full transition-all duration-300">
        <!-- Top Navbar -->
        <header class="bg-white border-b border-slate-200 h-20 flex items-center justify-between px-6 shrink-0 z-10 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-500 hover:text-emerald-600 transition-colors">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h1 class="text-xl font-bold text-slate-800 font-outfit">Rekap Absensi Harian</h1>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-semibold text-slate-800 leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500">Administrator</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-emerald-100 border-2 border-emerald-200 overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=10b981&color=fff" alt="Admin" class="w-full h-full object-cover">
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6 custom-scrollbar" x-data="{ showStatusModal: false, editRecord: {} }">
            <div class="max-w-7xl mx-auto">

                <!-- Toast Notification -->
                @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                     class="fixed top-6 right-6 z-50 flex items-center gap-3 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-lg shadow-emerald-600/30 font-medium text-sm">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    {{ session('success') }}
                    <button @click="show = false" class="ml-2 hover:text-emerald-200"><i data-lucide="x" class="w-4 h-4"></i></button>
                </div>
                @endif

                <!-- Header Actions & Filters -->
                <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm mb-6">
                    <form method="GET" action="{{ route('attendances.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
                        @if($selectedClass)
                            <input type="hidden" name="class" value="{{ $selectedClass }}">
                        @endif
                        <div class="w-full md:w-auto">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal</label>
                            <input type="date" name="date" value="{{ $date }}" 
                                   class="w-full px-4 py-2 rounded-lg bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all text-sm outline-none">
                        </div>
                        <div class="w-full md:w-auto flex-1">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Cari Siswa</label>
                            <div class="relative">
                                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, kelas..." 
                                       class="w-full pl-9 pr-4 py-2 rounded-lg bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all text-sm outline-none">
                            </div>
                        </div>
                        <div class="w-full md:w-auto flex gap-2">
                            <button type="submit" class="flex-1 md:flex-none px-5 py-2 bg-slate-800 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition-colors">
                                Terapkan
                            </button>
                            <a href="{{ route('attendances.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-200 transition-colors flex items-center justify-center tooltip-trigger" title="Reset Filter">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </form>
                </div>

                @if(!$selectedClass)
                <!-- Mode Overview: Grid Kelas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($classStats as $stat)
                    <a href="{{ route('attendances.index', ['date' => $date, 'class' => $stat['name']]) }}" class="block bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all group">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800 font-outfit group-hover:text-emerald-600 transition-colors">Kelas {{ $stat['name'] }}</h3>
                                @if($stat['homeroom_teacher'])
                                <p class="text-xs text-slate-500 mt-1">Wali Kelas: {{ $stat['homeroom_teacher'] }}</p>
                                @endif
                            </div>
                            <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </div>
                        </div>
                        
                        <!-- Progress Bar (Hadir vs Total) -->
                        <div class="mb-4">
                            <div class="flex justify-between text-xs mb-1.5">
                                <span class="font-medium text-slate-700">Tingkat Kehadiran</span>
                                <span class="font-bold text-emerald-600">{{ $stat['total_students'] ? round(($stat['hadir'] / $stat['total_students']) * 100) : 0 }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden flex">
                                @if($stat['total_students'] > 0)
                                    <div class="bg-emerald-500 h-full transition-all" style="width: {{ ($stat['hadir'] / $stat['total_students']) * 100 }}%"></div>
                                    <div class="bg-amber-400 h-full transition-all" style="width: {{ ($stat['izin'] / $stat['total_students']) * 100 }}%"></div>
                                    <div class="bg-blue-500 h-full transition-all" style="width: {{ ($stat['sakit'] / $stat['total_students']) * 100 }}%"></div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-sm mt-5 pt-5 border-t border-slate-50">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-emerald-500"></div> Hadir</span>
                                <span class="font-semibold text-slate-800">{{ $stat['hadir'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-amber-400"></div> Izin</span>
                                <span class="font-semibold text-slate-800">{{ $stat['izin'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-blue-500"></div> Sakit</span>
                                <span class="font-semibold text-slate-800">{{ $stat['sakit'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-rose-500"></div> Terlambat</span>
                                <span class="font-semibold text-slate-800">{{ $stat['terlambat'] }}</span>
                            </div>
                            <div class="flex items-center justify-between col-span-2 mt-1 bg-slate-50 p-2 rounded-lg">
                                <span class="text-slate-600 font-medium text-xs uppercase tracking-wider">Total Siswa</span>
                                <span class="font-bold text-slate-800">{{ $stat['total_students'] }}</span>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="col-span-full py-12 text-center text-slate-500 bg-white rounded-xl border border-slate-100">
                        <i data-lucide="school" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                        <p class="font-medium">Belum ada data kelas.</p>
                    </div>
                    @endforelse
                </div>
                @else
                <!-- Mode Detail: Tabel Siswa Per Kelas -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <a href="{{ route('attendances.index', ['date' => $date]) }}" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg transition-colors mb-3">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Kelas
                        </a>
                        <h2 class="text-xl font-bold text-slate-800 font-outfit">Kehadiran Kelas {{ $selectedClass }}</h2>
                        <p class="text-sm text-slate-500">Menampilkan detail siswa pada tanggal {{ Carbon\Carbon::parse($date)->format('d F Y') }}</p>
                    </div>
                </div>

                <!-- Summary Cards for the Selected Class -->
                @php
                    $hadir = 0; $izin = 0; $sakit = 0; $alpha = 0; $terlambat = 0;
                    foreach ($students as $student) {
                        $att = $student->attendances->first();
                        if (!$att) { $alpha++; }
                        elseif ($att->status == 'hadir') { $hadir++; }
                        elseif ($att->status == 'izin') { $izin++; }
                        elseif ($att->status == 'sakit') { $sakit++; }
                        elseif ($att->status == 'terlambat') { $terlambat++; }
                        elseif ($att->status == 'alpha') { $alpha++; }
                    }
                @endphp
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex flex-col justify-center">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Hadir</span>
                        <span class="text-2xl font-bold text-emerald-600">{{ $hadir }}</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex flex-col justify-center">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Terlambat</span>
                        <span class="text-2xl font-bold text-rose-500">{{ $terlambat }}</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex flex-col justify-center">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Izin</span>
                        <span class="text-2xl font-bold text-amber-500">{{ $izin }}</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex flex-col justify-center">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Sakit</span>
                        <span class="text-2xl font-bold text-blue-500">{{ $sakit }}</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex flex-col justify-center">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Alpha</span>
                        <span class="text-2xl font-bold text-slate-700">{{ $alpha }}</span>
                    </div>
                </div>

                <!-- Attendances Table -->
                <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-medium">Siswa</th>
                                    <th class="px-6 py-4 font-medium">Kelas</th>
                                    <th class="px-6 py-4 font-medium">Waktu Scan</th>
                                    <th class="px-6 py-4 font-medium">Status</th>
                                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100">
                                @forelse ($students as $student)
                                    @php
                                        $attendance = $student->attendances->first();
                                        $status = $attendance ? $attendance->status : 'alpha';
                                        
                                        $badgeClasses = [
                                            'hadir' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                            'terlambat' => 'bg-rose-50 text-rose-600 border-rose-200',
                                            'izin' => 'bg-amber-50 text-amber-600 border-amber-200',
                                            'sakit' => 'bg-blue-50 text-blue-600 border-blue-200',
                                            'alpha' => 'bg-slate-50 text-slate-600 border-slate-200',
                                        ];
                                        $dotClasses = [
                                            'hadir' => 'bg-emerald-500',
                                            'terlambat' => 'bg-rose-500',
                                            'izin' => 'bg-amber-500',
                                            'sakit' => 'bg-blue-500',
                                            'alpha' => 'bg-slate-500',
                                        ];
                                        $color = $badgeClasses[$status];
                                        $dotColor = $dotClasses[$status];
                                    @endphp
                                <tr class="hover:bg-slate-50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=f1f5f9" class="w-10 h-10 rounded-full" alt="{{ $student->name }}">
                                            <div>
                                                <p class="font-semibold text-slate-800 group-hover:text-emerald-600 transition-colors">{{ $student->name }}</p>
                                                <p class="text-xs text-slate-500">NIS: {{ $student->nis }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">{{ $student->class_name }}</td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">
                                        {{ $attendance ? $attendance->scanned_at->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $color }} capitalize">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span> {{ $status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="editRecord = { student_id: {{ $student->id }}, name: '{{ addslashes($student->name) }}', status: '{{ $status }}' }; showStatusModal = true" 
                                                class="text-slate-400 hover:text-emerald-600 transition-colors p-1.5 rounded-lg hover:bg-emerald-50 border border-transparent hover:border-emerald-100 flex items-center justify-center gap-2 ml-auto text-xs font-medium">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i> Edit Status
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        <i data-lucide="search-x" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                                        <p class="font-medium">Data tidak ditemukan.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <!-- Edit Status Modal -->
            <div x-show="showStatusModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
                <div @click.outside="showStatusModal = false" x-transition.scale.duration.200ms class="bg-white rounded-2xl w-full max-w-sm mx-4 shadow-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <h3 class="text-lg font-bold text-slate-800 font-outfit">Edit Status Manual</h3>
                        <button @click="showStatusModal = false" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                    </div>
                    <form action="{{ route('attendances.update_status') }}" method="POST" class="p-6 space-y-5">
                        @csrf
                        <input type="hidden" name="student_id" x-model="editRecord.student_id">
                        <input type="hidden" name="date" value="{{ $date }}">

                        <div class="text-center mb-4">
                            <p class="text-sm text-slate-500">Siswa</p>
                            <p class="font-bold text-slate-800 text-lg" x-text="editRecord.name"></p>
                            <p class="text-xs text-slate-400 mt-1">Tanggal: {{ Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-slate-700">Pilih Status Kehadiran</label>
                            <select name="status" x-model="editRecord.status" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm appearance-none">
                                <option value="hadir">Hadir (Tepat Waktu)</option>
                                <option value="terlambat">Terlambat</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpha">Alpha (Tanpa Keterangan)</option>
                            </select>
                        </div>
                        
                        <div class="flex gap-3 pt-4 border-t border-slate-100">
                            <button type="button" @click="showStatusModal = false" class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-medium text-sm transition-colors">Batal</button>
                            <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-medium text-sm shadow-sm shadow-emerald-600/20 transition-colors">Simpan Status</button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>
@endsection
