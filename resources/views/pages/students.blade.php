@extends('layouts.app')

@section('title', 'Data Siswa | SMK Taruna Bangsa')

@section('content')
    @include('components.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 w-full transition-all duration-300">
        <!-- Top Navbar -->
        <header
            class="bg-white border-b border-slate-200 h-20 flex items-center justify-between px-6 shrink-0 z-10 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="md:hidden text-slate-500 hover:text-emerald-600 transition-colors">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h1 class="text-xl font-bold text-slate-800 font-outfit">Manajemen Data Siswa</h1>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-semibold text-slate-800 leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500">Administrator</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-emerald-100 border-2 border-emerald-200 overflow-hidden">
                    <x-avatar :name="Auth::user()->name" size="w-10 h-10" bg="bg-emerald-500" text="text-white" />
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6 custom-scrollbar"
            x-data="{ showAddModal: false, showEditModal: false, showQrModal: false, showImportModal: false, importTargetClass: null, showAddClassModal: false, editStudent: {}, qrStudent: {} }">
            <div class="max-w-7xl mx-auto">

                <!-- Toast Notification -->
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                        class="fixed top-6 right-6 z-50 flex items-center gap-3 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-lg shadow-emerald-600/30 font-medium text-sm">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        {{ session('success') }}
                        <button @click="show = false" class="ml-2 hover:text-emerald-200"><i data-lucide="x"
                                class="w-4 h-4"></i></button>
                    </div>
                @endif
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 8000)"
                        class="fixed top-6 right-6 z-50 flex items-start gap-3 bg-rose-600 text-white px-5 py-3 rounded-xl shadow-lg shadow-rose-600/30 font-medium text-sm max-w-md">
                        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                        <div class="max-h-64 overflow-y-auto custom-scrollbar text-rose-50 pr-2">
                            {!! nl2br(e(session('error'))) !!}
                        </div>
                        <button @click="show = false" class="ml-auto hover:text-rose-200 shrink-0"><i data-lucide="x"
                                class="w-4 h-4"></i></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 6000)"
                        class="fixed top-6 right-6 z-50 flex items-start gap-3 bg-rose-600 text-white px-5 py-3 rounded-xl shadow-lg shadow-rose-600/30 font-medium text-sm">
                        <i data-lucide="alert-circle" class="w-5 h-5 mt-0.5"></i>
                        <ul class="list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button @click="show = false" class="ml-2 hover:text-rose-200"><i data-lucide="x"
                                class="w-4 h-4"></i></button>
                    </div>
                @endif

                <!-- Class Filter Bar -->
                <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm mb-6">
                    <div class="flex flex-col md:flex-row md:items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pilih
                                Kelas</label>
                            <div class="flex flex-wrap gap-2 items-center">
                                @foreach ($classes as $class)
                                    <a href="{{ route('students.index', ['class' => $class]) }}"
                                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border {{ $selectedClass === $class ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm shadow-emerald-600/20' : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-300 hover:text-emerald-600 hover:bg-emerald-50' }}">
                                        {{ $class }}
                                    </a>
                                @endforeach

                                <button @click="showAddClassModal = true"
                                    class="px-3 py-2 rounded-lg text-sm font-medium border border-dashed border-slate-300 text-slate-500 hover:border-emerald-500 hover:text-emerald-600 hover:bg-emerald-50 transition-all flex items-center gap-1 tooltip-trigger"
                                    title="Tambah Kelas Baru">
                                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kelas
                                </button>

                                <button @click="importTargetClass = null; showImportModal = true"
                                    class="px-3 py-2 rounded-lg text-sm font-medium border border-slate-200 bg-white text-slate-600 hover:border-emerald-500 hover:text-emerald-600 hover:bg-emerald-50 shadow-sm transition-all flex items-center gap-1 tooltip-trigger"
                                    title="Import Banyak Kelas Sekaligus">
                                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i> Import Global
                                </button>
                            </div>
                        </div>
                        @if($selectedClass)
                            <a href="{{ route('students.index') }}"
                                class="text-xs font-medium text-slate-500 hover:text-rose-500 flex items-center gap-1 transition-colors shrink-0 pb-1">
                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Reset Filter
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Header Actions (only when class is selected) -->
                @if($selectedClass)
                    @php $currentClassRoom = $classRooms->firstWhere('name', $selectedClass); @endphp
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800 font-outfit">Kelas {{ $selectedClass }}</h2>
                            <p class="text-sm text-slate-500">{{ $students->total() }} siswa terdaftar
                                @if($currentClassRoom && $currentClassRoom->homeroom_teacher)
                                    · Wali Kelas: <span
                                        class="font-medium text-emerald-600">{{ $currentClassRoom->homeroom_teacher }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Search Bar -->
                            <form method="GET" action="{{ route('students.index') }}" class="relative">
                                <input type="hidden" name="class" value="{{ $selectedClass }}">
                                <i data-lucide="search"
                                    class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari nama atau NIS..."
                                    class="pl-10 pr-4 py-2 w-56 lg:w-72 rounded-xl bg-white border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all text-sm outline-none">
                            </form>
                            <!-- Import Button -->
                            <button @click="importTargetClass = '{{ $selectedClass }}'; showImportModal = true"
                                class="flex items-center gap-2 bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
                                Import ke Kelas Ini
                            </button>
                            <!-- Add Student Button -->
                            <button @click="showAddModal = true"
                                class="flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm shadow-emerald-600/20">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Tambah Siswa
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Students Table (only when class selected) -->
                @if(!$selectedClass)
                    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center mb-4">
                                <i data-lucide="filter" class="w-10 h-10 text-emerald-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-700 mb-1 font-outfit">Pilih Kelas Terlebih Dahulu</h3>
                            <p class="text-sm text-slate-500 max-w-sm">Klik salah satu tombol kelas di atas untuk menampilkan
                                daftar siswa di kelas tersebut.</p>
                        </div>
                    </div>
                @else

                        <!-- Students Table -->
                        <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-wider">
                                            <th class="px-6 py-4 font-medium w-16 text-slate-400">#</th>
                                            <th class="px-6 py-4 font-medium">Siswa</th>
                                            <th class="px-6 py-4 font-medium">Kelas</th>
                                            <th class="px-6 py-4 font-medium">No Telp Ortu</th>
                                            <th class="px-6 py-4 font-medium">QR Code</th>
                                            <th class="px-6 py-4 font-medium text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm divide-y divide-slate-100">
                                        @forelse ($students as $index => $student)
                                            <tr class="hover:bg-slate-50 transition-colors group">
                                                <td class="px-6 py-4 text-slate-400">{{ $students->firstItem() + $index }}</td>
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <x-avatar :name="$student->name" size="w-10 h-10" bg="bg-slate-100"
                                                            text="text-slate-600" />
                                                        <div>
                                                            <p
                                                                class="font-semibold text-slate-800 group-hover:text-emerald-600 transition-colors">
                                                                {{ $student->name }}</p>
                                                            <p class="text-xs text-slate-500">NIS: {{ $student->nis }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-slate-600">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-100">{{ $student->class_name }}</span>
                                                </td>
                                                <td class="px-6 py-4 text-slate-600">
                                                    {{ $student->parent_phone ?: '-' }}
                                                </td>
                                                <td class="px-6 py-4 text-slate-600">
                                                    <div class="flex items-center gap-2">
                                                        <code
                                                            class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-md font-mono">{{ $student->qr_code }}</code>
                                                        <button
                                                            @click="qrStudent = { id: {{ $student->id }}, name: '{{ addslashes($student->name) }}', nis: '{{ $student->nis }}', qr: '{{ $student->qr_code }}' }; showQrModal = true"
                                                            class="text-emerald-600 hover:text-emerald-700 bg-emerald-50 p-1.5 rounded-lg transition-colors border border-emerald-100 tooltip-trigger"
                                                            title="Lihat QR Code">
                                                            <i data-lucide="qr-code" class="w-4 h-4"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <button
                                                            @click="editStudent = { id: {{ $student->id }}, nis: '{{ $student->nis }}', name: '{{ addslashes($student->name) }}', class_name: '{{ addslashes($student->class_name) }}', parent_phone: '{{ addslashes($student->parent_phone) }}' }; showEditModal = true"
                                                            class="text-slate-400 hover:text-emerald-600 transition-colors p-1.5 rounded-lg hover:bg-emerald-50">
                                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                                        </button>
                                                        <form action="{{ route('students.destroy', $student) }}" method="POST"
                                                            onsubmit="return confirm('Yakin ingin menghapus siswa {{ addslashes($student->name) }}?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="text-slate-400 hover:text-rose-600 transition-colors p-1.5 rounded-lg hover:bg-rose-50">
                                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                                    <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                                                    <p class="font-medium">Belum ada data siswa di kelas ini.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if ($students->hasPages())
                                <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-sm text-slate-500">Menampilkan
                                        {{ $students->firstItem() }}-{{ $students->lastItem() }} dari {{ $students->total() }}
                                        data</span>
                                    <div class="flex gap-1">
                                        @if ($students->onFirstPage())
                                            <span class="px-3 py-1 rounded border border-slate-200 text-slate-300 cursor-not-allowed"><i
                                                    data-lucide="chevron-left" class="w-4 h-4"></i></span>
                                        @else
                                            <a href="{{ $students->previousPageUrl() }}"
                                                class="px-3 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50"><i
                                                    data-lucide="chevron-left" class="w-4 h-4"></i></a>
                                        @endif

                                        @foreach ($students->links()->elements[0] as $page => $url)
                                            @if ($page == $students->currentPage())
                                                <span class="px-3 py-1 rounded bg-emerald-600 text-white font-medium text-sm">{{ $page }}</span>
                                            @else
                                                <a href="{{ $url }}"
                                                    class="px-3 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-sm">{{ $page }}</a>
                                            @endif
                                        @endforeach

                                        @if ($students->hasMorePages())
                                            <a href="{{ $students->nextPageUrl() }}"
                                                class="px-3 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50"><i
                                                    data-lucide="chevron-right" class="w-4 h-4"></i></a>
                                        @else
                                            <span class="px-3 py-1 rounded border border-slate-200 text-slate-300 cursor-not-allowed"><i
                                                    data-lucide="chevron-right" class="w-4 h-4"></i></span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            <!-- Add Class Modal -->
            <div x-show="showAddClassModal" x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                style="display: none;">
                <div @click.outside="showAddClassModal = false" x-transition.scale.duration.200ms
                    class="bg-white rounded-2xl w-full max-w-sm mx-4 shadow-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <h3 class="text-lg font-bold text-slate-800 font-outfit">Tambah Kelas Baru</h3>
                        <button @click="showAddClassModal = false" class="text-slate-400 hover:text-slate-600"><i
                                data-lucide="x" class="w-5 h-5"></i></button>
                    </div>
                    <form action="{{ route('classes.store') }}" method="POST" class="p-6 space-y-5">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-slate-700">Nama Kelas</label>
                            <input type="text" name="name" required placeholder="Contoh: XII IPA 1"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-slate-700">Wali Kelas (Opsional)</label>
                            <input type="text" name="homeroom_teacher" placeholder="Contoh: Bp. Budi Santoso"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="showAddClassModal = false"
                                class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-medium text-sm transition-colors">Batal</button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-medium text-sm shadow-sm shadow-emerald-600/20 transition-colors">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Add Student Modal -->
            <div x-show="showAddModal" x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                style="display: none;">
                <div @click.outside="showAddModal = false" x-transition.scale.duration.200ms
                    class="bg-white rounded-2xl w-full max-w-md mx-4 shadow-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <h3 class="text-lg font-bold text-slate-800 font-outfit">Tambah Siswa Baru</h3>
                        <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600"><i data-lucide="x"
                                class="w-5 h-5"></i></button>
                    </div>
                    <form action="{{ route('students.store') }}" method="POST" class="p-6 space-y-5">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-slate-700">NIS</label>
                            <input type="text" name="nis" required placeholder="Contoh: 22231001"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-slate-700">Nama Lengkap</label>
                            <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-slate-700">Kelas</label>
                            <select name="class_name" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm appearance-none">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class }}">{{ $class }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-slate-700">No Telp Orang Tua (Opsional)</label>
                            <input type="text" name="parent_phone" placeholder="Contoh: 08123456789"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="showAddModal = false"
                                class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-medium text-sm transition-colors">Batal</button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-medium text-sm shadow-sm shadow-emerald-600/20 transition-colors">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Student Modal -->
            <div x-show="showEditModal" x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                style="display: none;">
                <div @click.outside="showEditModal = false" x-transition.scale.duration.200ms
                    class="bg-white rounded-2xl w-full max-w-md mx-4 shadow-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <h3 class="text-lg font-bold text-slate-800 font-outfit">Edit Data Siswa</h3>
                        <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600"><i
                                data-lucide="x" class="w-5 h-5"></i></button>
                    </div>
                    <form :action="`/students/${editStudent.id}`" method="POST" class="p-6 space-y-5">
                        @csrf
                        @method('PUT')
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-slate-700">NIS</label>
                            <input type="text" name="nis" x-model="editStudent.nis" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-slate-700">Nama Lengkap</label>
                            <input type="text" name="name" x-model="editStudent.name" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-slate-700">Kelas</label>
                            <select name="class_name" x-model="editStudent.class_name" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm appearance-none">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class }}">{{ $class }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-slate-700">No Telp Orang Tua (Opsional)</label>
                            <input type="text" name="parent_phone" x-model="editStudent.parent_phone"
                                placeholder="Contoh: 08123456789"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="showEditModal = false"
                                class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-medium text-sm transition-colors">Batal</button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-medium text-sm shadow-sm shadow-emerald-600/20 transition-colors">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- QR Code Modal -->
            <div x-show="showQrModal" x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
                style="display: none;">
                <div @click.outside="showQrModal = false" x-transition.scale.duration.200ms
                    class="bg-white rounded-2xl w-full max-w-sm mx-4 shadow-2xl overflow-hidden relative">
                    <button @click="showQrModal = false"
                        class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 bg-slate-100 rounded-full p-1"><i
                            data-lucide="x" class="w-5 h-5"></i></button>

                    <div class="p-8 text-center flex flex-col items-center">
                        <h3 class="text-xl font-bold text-slate-800 mb-1" x-text="qrStudent.name">Nama Siswa</h3>
                        <p class="text-slate-500 text-sm mb-6 font-medium">NIS: <span x-text="qrStudent.nis"></span></p>

                        <!-- The QR Image is fetched from the route -->
                        <div class="bg-white p-4 rounded-xl border-2 border-slate-100 shadow-sm inline-block mb-6 relative">
                            <img :src="showQrModal ? `/students/${qrStudent.id}/qr` : ''" class="w-48 h-48" alt="QR Code">
                        </div>

                        <code
                            class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg font-mono text-sm tracking-widest mb-6"
                            x-text="qrStudent.qr"></code>

                        <div class="flex w-full gap-3">
                            <a :href="`/students/${qrStudent.id}/qr`" download="QR_Code.svg" target="_blank"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-medium text-sm shadow-sm shadow-emerald-600/20 transition-all">
                                <i data-lucide="download" class="w-4 h-4"></i> Download
                            </a>
                            <button @click="window.open(`/students/${qrStudent.id}/qr`, '_blank')"
                                class="flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 font-medium text-sm transition-all">
                                <i data-lucide="printer" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Excel Modal -->
            <div x-show="showImportModal" x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                style="display: none;">
                <div @click.outside="showImportModal = false" x-transition.scale.duration.200ms
                    class="bg-white rounded-2xl w-full max-w-lg mx-4 shadow-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <h3 class="text-lg font-bold text-slate-800 font-outfit">Import Data dari Excel</h3>
                        <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600"><i
                                data-lucide="x" class="w-5 h-5"></i></button>
                    </div>

                    <div class="p-6">
                        <!-- Format Preview -->
                        <div class="mb-5 bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-sm font-medium text-slate-700">Contoh Format Excel (Baris 1 harus Header):
                                </p>
                                <a href="{{ route('students.template') }}"
                                    class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">
                                    <i data-lucide="download-cloud" class="w-3 h-3"></i> Download Template
                                </a>
                            </div>
                            <!-- Visual Table mimicking Excel -->
                            <div class="overflow-hidden rounded border border-slate-300 bg-white shadow-sm">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="bg-slate-100 text-slate-600 border-b border-slate-300">
                                            <th
                                                class="px-3 py-1.5 border-r border-slate-300 w-10 text-center font-normal bg-slate-200">
                                            </th>
                                            <th class="px-3 py-1.5 border-r border-slate-300 font-semibold w-1/4">A</th>
                                            <th class="px-3 py-1.5 border-r border-slate-300 font-semibold w-1/4">B</th>
                                            <th class="px-3 py-1.5 border-r border-slate-300 font-semibold w-1/4">C</th>
                                            <th class="px-3 py-1.5 font-semibold w-1/4">D</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-slate-600 font-mono">
                                        <tr class="border-b border-slate-200">
                                            <td class="px-3 py-1.5 border-r border-slate-300 text-center bg-slate-100">1
                                            </td>
                                            <td class="px-3 py-1.5 border-r border-slate-200 font-bold bg-yellow-50">NIS
                                            </td>
                                            <td class="px-3 py-1.5 border-r border-slate-200 font-bold bg-yellow-50">NAMA
                                            </td>
                                            <td class="px-3 py-1.5 border-r border-slate-200 font-bold bg-yellow-50">KELAS
                                            </td>
                                            <td class="px-3 py-1.5 font-bold bg-yellow-50">NO TELP ORANG TUA</td>
                                        </tr>
                                        <tr class="border-b border-slate-200">
                                            <td class="px-3 py-1.5 border-r border-slate-300 text-center bg-slate-100">2
                                            </td>
                                            <td class="px-3 py-1.5 border-r border-slate-200">22231001</td>
                                            <td class="px-3 py-1.5 border-r border-slate-200">Budi Santoso</td>
                                            <td class="px-3 py-1.5 border-r border-slate-200">XI RPL 1</td>
                                            <td class="px-3 py-1.5">08123456789</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-1.5 border-r border-slate-300 text-center bg-slate-100">3
                                            </td>
                                            <td class="px-3 py-1.5 border-r border-slate-200">22231002</td>
                                            <td class="px-3 py-1.5 border-r border-slate-200">Siti Aminah</td>
                                            <td class="px-3 py-1.5 border-r border-slate-200">XI TKJ 2</td>
                                            <td class="px-3 py-1.5">08987654321</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-xs text-slate-500 mt-2"><i data-lucide="info" class="w-3 h-3 inline"></i> Data
                                dengan NIS yang sudah ada di sistem akan otomatis dilewati (skip).</p>
                        </div>

                        <!-- Upload Form -->
                        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="target_class" :value="importTargetClass">

                            <div x-show="importTargetClass"
                                class="mb-4 bg-emerald-50 text-emerald-800 p-3 rounded-lg border border-emerald-200 text-sm flex items-start gap-2">
                                <i data-lucide="info" class="w-4 h-4 mt-0.5 shrink-0 text-emerald-600"></i>
                                <p>Anda sedang mengimport khusus untuk kelas <strong x-text="importTargetClass"></strong>.
                                    Jika ada siswa dari kelas lain di dalam file Excel, proses import akan digagalkan.</p>
                            </div>

                            <div x-show="!importTargetClass"
                                class="mb-4 bg-blue-50 text-blue-800 p-3 rounded-lg border border-blue-200 text-sm flex items-start gap-2">
                                <i data-lucide="globe" class="w-4 h-4 mt-0.5 shrink-0 text-blue-600"></i>
                                <p>Anda sedang melakukan <strong>Import Global</strong>. Anda bisa memasukkan banyak siswa
                                    dari berbagai kelas sekaligus selama nama kelas tersebut sudah terdaftar di sistem.</p>
                            </div>

                            <div class="mb-5">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Upload File (.xlsx)</label>
                                <input type="file" name="file" accept=".xlsx, .xls, .csv" required
                                    class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-200 rounded-xl cursor-pointer">
                            </div>

                            <div class="flex gap-3">
                                <button type="button" @click="showImportModal = false"
                                    class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-medium text-sm transition-colors">Batal</button>
                                <button type="submit"
                                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-medium text-sm shadow-sm shadow-emerald-600/20 transition-colors">
                                    <i data-lucide="upload" class="w-4 h-4"></i> Upload & Proses
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>
@endsection