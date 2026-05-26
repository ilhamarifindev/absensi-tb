@extends('layouts.app')

@section('title', 'Live Monitor Scanner | SMK Taruna Bangsa')

@section('content')
<div x-data="monitorApp()" x-init="initMonitor()" class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-900 w-full font-outfit relative">
    
    <!-- Header -->
    <header class="glass-dark border-b-0 h-20 flex items-center justify-between px-6 shrink-0 z-20">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-400 hover:text-emerald-400 transition-colors">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <div>
                <h1 class="font-bold text-xl text-white tracking-wide">LIVE MONITOR SCANNER</h1>
                <p class="text-xs text-slate-400">Pantau hasil scan Python secara real-time</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs font-mono" :class="isOnline ? 'text-emerald-400' : 'text-rose-400'">
                <i data-lucide="activity" class="w-4 h-4 inline-block mr-1"></i>
                <span x-text="isOnline ? 'TERKONEKSI' : 'TERPUTUS'"></span>
            </span>
            <div class="w-2.5 h-2.5 rounded-full" :class="isOnline ? 'bg-emerald-500 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.8)]' : 'bg-rose-500'"></div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-6 custom-scrollbar relative">
        <div class="max-w-4xl mx-auto space-y-6">
            
            <!-- Latest Scan Highlight -->
            <div class="glass-dark rounded-3xl p-8 border border-emerald-500/30 relative overflow-hidden shadow-2xl transition-all" x-show="attendances.length > 0">
                <div class="absolute -top-32 -right-32 w-64 h-64 rounded-full blur-3xl opacity-20 pointer-events-none"
                     :class="{
                         'bg-emerald-500': attendances[0]?.status === 'hadir',
                         'bg-rose-500': attendances[0]?.status === 'terlambat',
                         'bg-indigo-500': attendances[0]?.status === 'pulang'
                     }"></div>
                
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-6 text-center">Scan Terakhir</h2>
                
                <div class="flex flex-col sm:flex-row items-center gap-8 relative z-10">
                    <div class="w-32 h-32 rounded-full border-4 border-slate-700 overflow-hidden shrink-0 shadow-lg">
                        <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(attendances[0]?.name || 'X')}&background=334155&color=fff&size=200`" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <h3 class="text-4xl font-bold text-white mb-2" x-text="attendances[0]?.name">Nama Siswa</h3>
                        <p class="text-xl text-slate-300 font-medium mb-4">NIS: <span x-text="attendances[0]?.nis"></span> &bull; Kelas: <span x-text="attendances[0]?.class_name"></span></p>
                        
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4">
                            <span class="px-6 py-2 rounded-full text-sm font-bold uppercase tracking-wider"
                                  :class="{
                                      'bg-emerald-500/20 text-emerald-400 border border-emerald-500/50': attendances[0]?.status === 'hadir',
                                      'bg-rose-500/20 text-rose-400 border border-rose-500/50': attendances[0]?.status === 'terlambat',
                                      'bg-indigo-500/20 text-indigo-400 border border-indigo-500/50': attendances[0]?.status === 'pulang'
                                  }"
                                  x-text="attendances[0]?.status">Status</span>
                            <span class="text-lg font-mono text-white bg-slate-800/80 px-4 py-2 rounded-xl" x-text="attendances[0]?.time">00:00:00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div x-show="attendances.length === 0" class="glass-dark rounded-3xl p-12 text-center border border-slate-700/50">
                <i data-lucide="qr-code" class="w-16 h-16 text-slate-600 mx-auto mb-4 opacity-50"></i>
                <h3 class="text-xl font-bold text-white mb-2">Menunggu Scan...</h3>
                <p class="text-slate-400">Jalankan script Python dan scan QR Code siswa.</p>
            </div>

            <!-- Recent History List -->
            <div class="glass-dark rounded-2xl border border-slate-700/50 overflow-hidden" x-show="attendances.length > 1">
                <div class="p-4 border-b border-slate-700/50 bg-slate-800/30">
                    <h3 class="font-medium text-slate-300">Riwayat Sebelumnya (Hari Ini)</h3>
                </div>
                <div class="divide-y divide-slate-700/50">
                    <template x-for="(att, index) in attendances.slice(1)" :key="att.id">
                        <div class="flex items-center justify-between p-4 hover:bg-slate-800/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(att.name)}&background=334155&color=fff`" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="text-white font-medium text-sm" x-text="att.name"></p>
                                    <p class="text-xs text-slate-400"><span x-text="att.class_name"></span> &bull; <span x-text="att.nis"></span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 text-right">
                                <span class="text-xs px-2 py-1 rounded-md uppercase font-bold"
                                      :class="{
                                          'text-emerald-400 bg-emerald-400/10': att.status === 'hadir',
                                          'text-rose-400 bg-rose-400/10': att.status === 'terlambat',
                                          'text-indigo-400 bg-indigo-400/10': att.status === 'pulang'
                                      }" x-text="att.status"></span>
                                <span class="text-sm font-mono text-slate-300 w-16" x-text="att.time"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('monitorApp', () => ({
            attendances: [],
            isOnline: false,
            pollInterval: null,

            initMonitor() {
                this.fetchData();
                // Poll every 2 seconds
                this.pollInterval = setInterval(() => {
                    this.fetchData();
                }, 2000);
            },

            fetchData() {
                fetch('/api/monitor/latest')
                    .then(res => {
                        if(!res.ok) throw new Error('Network response was not ok');
                        return res.json();
                    })
                    .then(data => {
                        this.isOnline = true;
                        if(data.success && JSON.stringify(this.attendances) !== JSON.stringify(data.data)) {
                            this.attendances = data.data;
                            if (window.lucide) {
                                setTimeout(() => window.lucide.createIcons(), 100);
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        this.isOnline = false;
                    });
            },
            
            destroy() {
                if(this.pollInterval) clearInterval(this.pollInterval);
            }
        }));
    });
</script>
@endsection
