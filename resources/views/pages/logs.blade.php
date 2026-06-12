@extends('layouts.app')

@section('title', 'Log Aktivitas | SMK Taruna Bangsa')

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
                <div>
                    <h1 class="text-xl font-bold text-slate-800 font-outfit">Log Aktivitas</h1>
                    <p class="text-xs text-slate-500">Riwayat semua aktivitas scan masuk & pulang</p>
                </div>
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
        <main class="flex-1 overflow-y-auto p-6 custom-scrollbar">
            <div class="max-w-7xl mx-auto">

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                                <i data-lucide="activity" class="w-4 h-4 text-slate-600"></i>
                            </div>
                            <span class="text-xs font-medium text-slate-500 uppercase">Total Scan</span>
                        </div>
                        <span class="text-2xl font-bold text-slate-800">{{ $stats['total'] }}</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                            <span class="text-xs font-medium text-slate-500 uppercase">Hadir</span>
                        </div>
                        <span class="text-2xl font-bold text-emerald-600">{{ $stats['hadir'] }}</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center">
                                <i data-lucide="clock" class="w-4 h-4 text-rose-600"></i>
                            </div>
                            <span class="text-xs font-medium text-slate-500 uppercase">Terlambat</span>
                        </div>
                        <span class="text-2xl font-bold text-rose-600">{{ $stats['terlambat'] }}</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                                <i data-lucide="log-out" class="w-4 h-4 text-indigo-600"></i>
                            </div>
                            <span class="text-xs font-medium text-slate-500 uppercase">Pulang</span>
                        </div>
                        <span class="text-2xl font-bold text-indigo-600">{{ $stats['pulang'] }}</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                                <i data-lucide="mail-warning" class="w-4 h-4 text-amber-600"></i>
                            </div>
                            <span class="text-xs font-medium text-slate-500 uppercase">Izin</span>
                        </div>
                        <span class="text-2xl font-bold text-amber-600">{{ $stats['izin'] }}</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                                <i data-lucide="thermometer" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <span class="text-xs font-medium text-slate-500 uppercase">Sakit</span>
                        </div>
                        <span class="text-2xl font-bold text-blue-600">{{ $stats['sakit'] }}</span>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm mb-6">
                    <form method="GET" action="{{ route('logs.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="w-full md:w-auto">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal</label>
                            <input type="date" name="date" value="{{ $date }}"
                                class="w-full px-4 py-2 rounded-lg bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all text-sm outline-none">
                        </div>
                        <div class="w-full md:w-auto flex-1">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Cari Siswa</label>
                            <div class="relative">
                                <i data-lucide="search"
                                    class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" name="search" value="{{ $search }}"
                                    placeholder="Cari nama, NIS, kelas..."
                                    class="w-full pl-9 pr-4 py-2 rounded-lg bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all text-sm outline-none">
                            </div>
                        </div>
                        <div class="w-full md:w-auto">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                            <select name="status"
                                class="w-full px-4 py-2 rounded-lg bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all text-sm outline-none appearance-none">
                                <option value="">Semua Status</option>
                                <option value="hadir" {{ $status === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                <option value="terlambat" {{ $status === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                <option value="pulang" {{ $status === 'pulang' ? 'selected' : '' }}>Pulang</option>
                                <option value="izin" {{ $status === 'izin' ? 'selected' : '' }}>Izin</option>
                                <option value="sakit" {{ $status === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="alpha" {{ $status === 'alpha' ? 'selected' : '' }}>Alpha</option>
                            </select>
                        </div>
                        <div class="w-full md:w-auto flex gap-2">
                            <button type="submit"
                                class="flex-1 md:flex-none px-5 py-2 bg-slate-800 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition-colors">
                                Terapkan
                            </button>
                            <a href="{{ route('logs.index') }}"
                                class="px-3 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-200 transition-colors flex items-center justify-center"
                                title="Reset Filter">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Log Table -->
                <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <i data-lucide="scroll-text" class="w-5 h-5 text-emerald-500"></i>
                            Riwayat Scan — {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                        </h3>
                        <span class="text-sm text-slate-500">{{ $logs->total() }} data</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-medium w-10">#</th>
                                    <th class="px-6 py-4 font-medium">Siswa</th>
                                    <th class="px-6 py-4 font-medium">Kelas</th>
                                    <th class="px-6 py-4 font-medium">Waktu Scan</th>
                                    <th class="px-6 py-4 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100">
                                @forelse ($logs as $index => $log)
                                    @php
                                        $badgeClasses = [
                                            'hadir' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                            'terlambat' => 'bg-rose-50 text-rose-600 border-rose-200',
                                            'izin' => 'bg-amber-50 text-amber-600 border-amber-200',
                                            'sakit' => 'bg-blue-50 text-blue-600 border-blue-200',
                                            'alpha' => 'bg-slate-50 text-slate-600 border-slate-200',
                                            'pulang' => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                                        ];
                                        $dotClasses = [
                                            'hadir' => 'bg-emerald-500',
                                            'terlambat' => 'bg-rose-500',
                                            'izin' => 'bg-amber-500',
                                            'sakit' => 'bg-blue-500',
                                            'alpha' => 'bg-slate-500',
                                            'pulang' => 'bg-indigo-500',
                                        ];
                                        $iconMap = [
                                            'hadir' => 'log-in',
                                            'terlambat' => 'clock',
                                            'izin' => 'mail',
                                            'sakit' => 'thermometer',
                                            'alpha' => 'x-circle',
                                            'pulang' => 'log-out',
                                        ];
                                        $color = $badgeClasses[$log->status] ?? $badgeClasses['hadir'];
                                        $dotColor = $dotClasses[$log->status] ?? $dotClasses['hadir'];
                                        $icon = $iconMap[$log->status] ?? 'activity';
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors group">
                                        <td class="px-6 py-4 text-slate-400 text-xs">{{ $logs->firstItem() + $index }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <x-avatar :name="$log->student->name ?? 'X'" size="w-10 h-10" bg="bg-slate-100"
                                                    text="text-slate-600" />
                                                <div>
                                                    <p
                                                        class="font-semibold text-slate-800 group-hover:text-emerald-600 transition-colors">
                                                        {{ $log->student->name ?? '-' }}</p>
                                                    <p class="text-xs text-slate-500">NIS: {{ $log->student->nis ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-slate-600">{{ $log->student->class_name ?? '-' }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="{{ $icon }}"
                                                    class="w-4 h-4 {{ str_replace('bg-', 'text-', $dotColor) }}"></i>
                                                <span
                                                    class="text-slate-800 font-medium">{{ $log->scanned_at->format('H:i:s') }}</span>
                                                <span
                                                    class="text-xs text-slate-400">{{ $log->scanned_at->diffForHumans() }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $color }} capitalize">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                                                {{ $log->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                            <i data-lucide="scroll-text" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                                            <p class="font-medium">Belum ada aktivitas scan pada tanggal ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($logs->hasPages())
                        <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-sm text-slate-500">
                                Menampilkan {{ $logs->firstItem() }}-{{ $logs->lastItem() }} dari {{ $logs->total() }} data
                            </span>
                            <div class="flex gap-1">
                                @if($logs->onFirstPage())
                                    <span class="px-3 py-1 rounded border border-slate-200 text-slate-400 disabled:opacity-50"><i
                                            data-lucide="chevron-left" class="w-4 h-4"></i></span>
                                @else
                                    <a href="{{ $logs->previousPageUrl() }}"
                                        class="px-3 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50"><i
                                            data-lucide="chevron-left" class="w-4 h-4"></i></a>
                                @endif

                                @foreach($logs->getUrlRange(max(1, $logs->currentPage() - 2), min($logs->lastPage(), $logs->currentPage() + 2)) as $page => $url)
                                    @if($page == $logs->currentPage())
                                        <span class="px-3 py-1 rounded bg-emerald-600 text-white font-medium">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}"
                                            class="px-3 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if($logs->hasMorePages())
                                    <a href="{{ $logs->nextPageUrl() }}"
                                        class="px-3 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50"><i
                                            data-lucide="chevron-right" class="w-4 h-4"></i></a>
                                @else
                                    <span class="px-3 py-1 rounded border border-slate-200 text-slate-400"><i
                                            data-lucide="chevron-right" class="w-4 h-4"></i></span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </main>
    </div>
@endsection