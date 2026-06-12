@extends('layouts.app')

@section('title', 'Dashboard | SMK Taruna Bangsa')

@section('content')
    @include('components.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 w-full transition-all duration-300">
        <!-- Top Navbar for Dashboard -->
        <header
            class="bg-white border-b border-slate-200 h-20 flex items-center justify-between px-6 shrink-0 z-10 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="md:hidden text-slate-500 hover:text-emerald-600 transition-colors">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <div class="hidden sm:block relative">
                    <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" placeholder="Cari data siswa, guru..."
                        class="pl-10 pr-4 py-2 w-64 lg:w-80 rounded-xl bg-slate-100 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all text-sm outline-none">
                </div>
            </div>

            <div class="flex items-center gap-6">
                <!-- Notifications -->
                <button class="relative text-slate-500 hover:text-emerald-600 transition-colors">
                    <i data-lucide="bell" class="w-6 h-6"></i>
                    <span
                        class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white"></span>
                </button>

                <!-- Profile Dropdown (Static for UI) -->
                <div class="flex items-center gap-3 cursor-pointer group">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-semibold text-slate-800 leading-tight">Admin Sekolah</p>
                        <p class="text-xs text-slate-500">Administrator</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-full bg-emerald-100 border-2 border-emerald-200 overflow-hidden group-hover:border-emerald-500 transition-colors">
                        <x-avatar name="Admin Sekolah" size="w-10 h-10" bg="bg-emerald-100" text="text-emerald-700"
                            class="border-2 border-emerald-200" />
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 group-hover:text-slate-600"></i>
                </div>
            </div>
        </header>

        <!-- Main Content Scrollable -->
        <main class="flex-1 overflow-y-auto p-6 custom-scrollbar">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 font-outfit">Dashboard Overview</h1>
                        <p class="text-sm text-slate-500 mt-1">Pantau statistik kehadiran hari ini, 26 Mei 2026</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button
                            class="flex items-center gap-2 bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Export
                        </button>
                        <button
                            class="flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm shadow-emerald-600/20">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Input Manual
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                    <x-cards title="Total Siswa" value="{{ number_format($totalStudents) }}" icon="users" color="blue"
                        trend="up" trendValue="+0" />
                    <x-cards title="Hadir" value="{{ number_format($hadir) }}" icon="check-circle" color="emerald"
                        trend="up" trendValue="{{ $totalStudents ? round(($hadir / $totalStudents) * 100) : 0 }}%" />
                    <x-cards title="Izin" value="{{ number_format($izin) }}" icon="mail-warning" color="amber" />
                    <x-cards title="Sakit" value="{{ number_format($sakit) }}" icon="thermometer" color="slate" />
                    <x-cards title="Alpha" value="{{ number_format($alpha) }}" icon="x-circle" color="rose" />
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm lg:col-span-2">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-slate-800">Tren Kehadiran Mingguan</h3>
                            <select
                                class="bg-slate-50 border border-slate-200 text-slate-600 text-sm rounded-lg px-3 py-1.5 outline-none">
                                <option>Minggu Ini</option>
                                <option>Bulan Ini</option>
                            </select>
                        </div>
                        <div class="h-72 w-full">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
                        <h3 class="font-bold text-slate-800 mb-6">Persentase Hari Ini</h3>
                        <div class="h-56 w-full flex items-center justify-center relative">
                            <canvas id="pieChart"></canvas>
                            <!-- Center text for doughnut -->
                            <div
                                class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-4">
                                <span
                                    class="text-3xl font-bold text-slate-800">{{ $totalStudents ? round(($hadir / $totalStudents) * 100) : 0 }}%</span>
                                <span class="text-xs text-slate-500">Kehadiran</span>
                            </div>
                        </div>
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div><span
                                        class="text-slate-600">Hadir</span>
                                </div>
                                <span class="font-medium text-slate-800">{{ number_format($hadir) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-amber-400"></div><span
                                        class="text-slate-600">Izin/Sakit</span>
                                </div>
                                <span class="font-medium text-slate-800">{{ number_format($izin + $sakit) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-rose-500"></div><span
                                        class="text-slate-600">Alpha</span>
                                </div>
                                <span class="font-medium text-slate-800">{{ number_format($alpha) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Attendance Table -->
                <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden mb-8">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="font-bold text-slate-800">Scan Kehadiran Terbaru</h3>
                        <a href="#" class="text-sm text-emerald-600 font-medium hover:text-emerald-700">Lihat Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-medium">Siswa</th>
                                    <th class="px-6 py-4 font-medium">Kelas</th>
                                    <th class="px-6 py-4 font-medium">Jam Masuk</th>
                                    <th class="px-6 py-4 font-medium">Jam Pulang</th>
                                    <th class="px-6 py-4 font-medium">Status</th>
                                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100">
                                @forelse ($recentAttendances as $attendance)
                                    <tr class="hover:bg-slate-50 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <x-avatar :name="$attendance->student->name" size="w-10 h-10" bg="bg-slate-100"
                                                    text="text-slate-600" />
                                                <div>
                                                    <p
                                                        class="font-semibold text-slate-800 group-hover:text-emerald-600 transition-colors">
                                                        {{ $attendance->student->name }}</p>
                                                    <p class="text-xs text-slate-500">NIS: {{ $attendance->student->nis }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-slate-600">{{ $attendance->student->class_name }}</td>
                                        <td class="px-6 py-4 text-slate-600 font-medium">
                                            {{ $attendance->masuk ? \Carbon\Carbon::parse($attendance->masuk)->format('h:i A') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-slate-600 font-medium">
                                            {{ $attendance->pulang ? \Carbon\Carbon::parse($attendance->pulang)->format('h:i A') : '-' }}
                                        </td>
                                        <td class="px-6 py-4">
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
                                                $color = $badgeClasses[$attendance->status] ?? $badgeClasses['hadir'];
                                                $dotColor = $dotClasses[$attendance->status] ?? $dotClasses['hadir'];
                                            @endphp
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $color }} capitalize">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                                                {{ $attendance->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-slate-400 hover:text-emerald-600 transition-colors"><i
                                                    data-lucide="more-vertical" class="w-5 h-5"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">Belum ada data scan absensi
                                            hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-sm text-slate-500">Menampilkan 1-4 dari 1,180 data</span>
                        <div class="flex gap-1">
                            <button
                                class="px-3 py-1 rounded border border-slate-200 text-slate-400 hover:bg-slate-50 disabled:opacity-50"
                                disabled><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
                            <button class="px-3 py-1 rounded bg-emerald-600 text-white font-medium">1</button>
                            <button
                                class="px-3 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium">2</button>
                            <button
                                class="px-3 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium">3</button>
                            <span class="px-2 py-1 text-slate-400">...</span>
                            <button class="px-3 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50"><i
                                    data-lucide="chevron-right" class="w-4 h-4"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Chart.js (deferred, non-blocking) -->
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                function initCharts() {
                    if (typeof Chart === 'undefined') {
                        setTimeout(initCharts, 100);
                        return;
                    }
                    // Line Chart
                    const ctx = document.getElementById('attendanceChart').getContext('2d');

                    // Gradient fill
                    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)'); // emerald-500
                    gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum'],
                            datasets: [{
                                label: 'Siswa Hadir',
                                data: [1150, 1175, 1160, 1180, 1140],
                                borderColor: '#10b981',
                                backgroundColor: gradient,
                                borderWidth: 3,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#10b981',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    min: 1100,
                                    max: 1250,
                                    grid: {
                                        color: '#f1f5f9',
                                        drawBorder: false
                                    },
                                    ticks: { color: '#64748b', font: { family: "'Inter', sans-serif" } }
                                },
                                x: {
                                    grid: { display: false, drawBorder: false },
                                    ticks: { color: '#64748b', font: { family: "'Inter', sans-serif" } }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index',
                            },
                        }
                    });

                    // Doughnut Chart
                    const pieCtx = document.getElementById('pieChart').getContext('2d');
                    new Chart(pieCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Hadir', 'Izin/Sakit', 'Alpha'],
                            datasets: [{
                                data: [{{ $hadir }}, {{ $izin + $sakit }}, {{ $alpha }}],
                                backgroundColor: ['#10b981', '#fbbf24', '#f43f5e'],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '75%',
                            plugins: {
                                legend: { display: false }
                            }
                        }
                    });
                }
                initCharts();
            });
        </script>
    @endpush
@endsection