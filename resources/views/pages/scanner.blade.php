@extends('layouts.app')

@section('title', 'Scanner | SMK Taruna Bangsa')

@section('content')
<div x-data="scannerApp()" x-init="initApp()" class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-900 relative w-full font-outfit">
    <!-- Scanner Header -->
    <header class="glass-dark border-b-0 h-20 flex items-center justify-between px-6 shrink-0 z-20 absolute top-0 left-0 right-0">
        <a href="/" class="flex items-center gap-3 text-white hover:text-emerald-400 transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
            <span class="font-medium">Kembali ke Beranda</span>
        </a>
        <div class="flex flex-col items-end">
            <span class="font-bold text-lg text-white tracking-wide">QR SCANNER</span>
            <span class="text-xs flex items-center gap-1.5" :class="isOnline ? 'text-emerald-400' : 'text-slate-400'">
                <span class="w-1.5 h-1.5 rounded-full" :class="isOnline ? 'bg-emerald-500 animate-pulse' : 'bg-slate-500'"></span>
                <span x-text="isOnline ? 'SISTEM AKTIF' : 'DIJEDA'"></span>
            </span>
        </div>
    </header>

    <!-- Main Scanner Area -->
    <main class="flex-1 relative flex flex-col lg:flex-row mt-20">
        
        <!-- Camera Viewport -->
        <div class="flex-1 relative bg-black flex flex-col items-center justify-center overflow-hidden">
            <!-- Simulated Camera Background Pattern -->
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
            
            <!-- Scan Mode Toggle -->
            <div class="absolute top-6 left-1/2 -translate-x-1/2 z-30 bg-slate-900/80 p-1 rounded-full flex backdrop-blur-md border border-slate-700 shadow-xl" x-show="isCameraOn">
                <button @click="setMode('masuk')" class="px-6 py-2 rounded-full text-sm font-bold transition-all tracking-wide" :class="scanMode === 'masuk' ? 'bg-emerald-500 text-white shadow-[0_0_15px_rgba(16,185,129,0.5)]' : 'text-slate-400 hover:text-white'">MASUK</button>
                <button @click="setMode('pulang')" class="px-6 py-2 rounded-full text-sm font-bold transition-all tracking-wide" :class="scanMode === 'pulang' ? 'bg-indigo-500 text-white shadow-[0_0_15px_rgba(99,102,241,0.5)]' : 'text-slate-400 hover:text-white'">PULANG</button>
            </div>

            <!-- Video Stream Container -->
            <div class="relative w-full h-full max-w-3xl max-h-[75vh] flex items-center justify-center bg-slate-800/50 rounded-2xl border border-slate-700 overflow-hidden shadow-2xl z-10">
                <!-- If Camera is OFF show this -->
                <div x-show="!isCameraOn && !isLaunching" class="text-center p-8">
                    <div class="w-24 h-24 bg-slate-900 rounded-full border-4 border-slate-700 flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i data-lucide="camera-off" class="w-10 h-10 text-slate-500"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">Kamera Belum Aktif</h2>
                    <p class="text-slate-400 text-sm max-w-md mx-auto mb-8">Klik tombol di bawah untuk meluncurkan server Python OpenCV dan mengaktifkan streaming kamera secara langsung.</p>
                    
                    <button @click="launchScanner()" 
                            class="py-4 px-8 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold rounded-xl shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] transition-all duration-300 flex items-center gap-3 mx-auto transform hover:-translate-y-1">
                        <i data-lucide="power" class="w-6 h-6"></i>
                        AKTIFKAN KAMERA (PYTHON)
                    </button>
                </div>

                <!-- Launching State -->
                <div x-show="isLaunching" class="text-center text-emerald-400">
                    <i data-lucide="loader-2" class="w-16 h-16 animate-spin mx-auto mb-4"></i>
                    <p class="font-bold tracking-widest font-mono">MENYIAPKAN KAMERA...</p>
                </div>

                <!-- Python MJPEG Stream -->
                <img x-show="isCameraOn" :src="videoSrc" class="w-full h-full object-cover" x-on:error="handleStreamError" alt="Video Stream">
                
                <!-- Frame Overlay (Only visual) -->
                <div class="absolute inset-0 pointer-events-none flex items-center justify-center z-20" x-show="isCameraOn">
                    <div class="relative w-72 h-72 sm:w-96 sm:h-96" :class="scanMode === 'masuk' ? 'text-emerald-500' : 'text-indigo-500'">
                        <div class="absolute top-0 left-0 w-12 h-12 border-t-4 border-l-4 rounded-tl-xl" :class="scanMode === 'masuk' ? 'border-emerald-500' : 'border-indigo-500'"></div>
                        <div class="absolute top-0 right-0 w-12 h-12 border-t-4 border-r-4 rounded-tr-xl" :class="scanMode === 'masuk' ? 'border-emerald-500' : 'border-indigo-500'"></div>
                        <div class="absolute bottom-0 left-0 w-12 h-12 border-b-4 border-l-4 rounded-bl-xl" :class="scanMode === 'masuk' ? 'border-emerald-500' : 'border-indigo-500'"></div>
                        <div class="absolute bottom-0 right-0 w-12 h-12 border-b-4 border-r-4 rounded-br-xl" :class="scanMode === 'masuk' ? 'border-emerald-500' : 'border-indigo-500'"></div>
                        
                        <div class="absolute top-0 left-0 w-full h-1 rounded-full animate-[scan_2s_ease-in-out_infinite_alternate]" :class="scanMode === 'masuk' ? 'bg-emerald-400 shadow-[0_0_15px_3px_rgba(52,211,153,0.8)]' : 'bg-indigo-400 shadow-[0_0_15px_3px_rgba(99,102,241,0.8)]'"></div>
                    </div>
                </div>
            </div>

            <!-- Turn off button -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30" x-show="isCameraOn">
                <button @click="stopScanner()" class="w-14 h-14 rounded-full bg-rose-600 text-white flex items-center justify-center hover:bg-rose-500 transition-all hover:scale-110 shadow-lg shadow-rose-500/50">
                    <i data-lucide="power-off" class="w-6 h-6"></i>
                </button>
            </div>
        </div>

        <!-- Result Sidebar (Right) -->
        <div class="w-full lg:w-[400px] glass-dark border-l border-slate-700/50 z-10 flex flex-col relative h-[45vh] lg:h-full lg:rounded-tl-3xl shadow-[-10px_0_30px_rgba(0,0,0,0.5)]">
            <div class="p-6 border-b border-slate-700/50 bg-slate-800/30">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-6 h-6 text-emerald-400"></i>
                    Hasil Scan (Real-time)
                </h2>
            </div>
            
            <div class="flex-1 p-6 overflow-y-auto custom-scrollbar flex flex-col relative">
                
                <!-- Waiting State -->
                <div class="text-center opacity-50 my-auto" x-show="attendances.length === 0">
                    <i data-lucide="qr-code" class="w-20 h-20 text-slate-500 mx-auto mb-4 opacity-50"></i>
                    <p class="text-sm text-slate-400 font-mono tracking-widest">MENUNGGU SCAN...</p>
                </div>

                <!-- Latest Result Card -->
                <div class="w-full bg-slate-800/80 rounded-2xl border p-6 relative overflow-hidden mb-6 shadow-lg transition-colors duration-500" 
                     x-show="attendances.length > 0" 
                     :class="{
                         'border-emerald-500/50 shadow-emerald-900/20': attendances[0]?.status === 'hadir',
                         'border-rose-500/50 shadow-rose-900/20': attendances[0]?.status === 'terlambat',
                         'border-indigo-500/50 shadow-indigo-900/20': attendances[0]?.status === 'pulang'
                     }" style="display: none;">
                    
                    <div class="absolute -top-20 -right-20 w-40 h-40 rounded-full blur-3xl opacity-30"
                         :class="{
                             'bg-emerald-500': attendances[0]?.status === 'hadir',
                             'bg-rose-500': attendances[0]?.status === 'terlambat',
                             'bg-indigo-500': attendances[0]?.status === 'pulang'
                         }"></div>
                    
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-20 h-20 rounded-full bg-slate-700 border-2 p-1 mb-3 relative"
                             :class="{
                                 'border-emerald-500': attendances[0]?.status === 'hadir',
                                 'border-rose-500': attendances[0]?.status === 'terlambat',
                                 'border-indigo-500': attendances[0]?.status === 'pulang'
                             }">
                            <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(attendances[0]?.name || 'X')}&background=334155&color=fff&size=100`" class="w-full h-full rounded-full object-cover">
                        </div>
                        
                        <h3 class="text-xl font-bold text-white mb-1" x-text="attendances[0]?.name">Nama</h3>
                        <p class="text-slate-300 text-sm mb-4"><span x-text="attendances[0]?.nis"></span> &bull; <span x-text="attendances[0]?.class_name"></span></p>
                        
                        <div class="w-full bg-slate-900/50 rounded-xl p-3 flex justify-between items-center">
                            <span class="px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider"
                                  :class="{
                                      'bg-emerald-500/20 text-emerald-400': attendances[0]?.status === 'hadir',
                                      'bg-rose-500/20 text-rose-400': attendances[0]?.status === 'terlambat',
                                      'bg-indigo-500/20 text-indigo-400': attendances[0]?.status === 'pulang'
                                  }"
                                  x-text="attendances[0]?.status">Status</span>
                            <span class="text-sm text-emerald-400 font-mono font-bold" x-text="attendances[0]?.time">00:00:00</span>
                        </div>
                    </div>
                </div>

                <!-- History List -->
                <div class="space-y-3" x-show="attendances.length > 1">
                    <p class="text-xs text-slate-500 font-medium mb-2 uppercase tracking-wider">Riwayat Sesi Ini</p>
                    <template x-for="(att, index) in attendances.slice(1)" :key="att.id">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-800/30 border border-slate-700/50 hover:bg-slate-800/80 transition-colors">
                            <div class="flex items-center gap-3">
                                <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(att.name)}&background=334155&color=fff`" class="w-8 h-8 rounded-full">
                                <div>
                                    <p class="text-sm text-white font-medium truncate w-32" x-text="att.name"></p>
                                    <p class="text-[10px] text-slate-400" x-text="att.class_name"></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] uppercase font-bold"
                                   :class="{
                                      'text-emerald-400': att.status === 'hadir',
                                      'text-rose-400': att.status === 'terlambat',
                                      'text-indigo-400': att.status === 'pulang'
                                   }" x-text="att.status"></p>
                                <p class="text-xs text-slate-400 font-mono" x-text="att.time"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    @keyframes scan {
        0% { top: 0; }
        100% { top: calc(100% - 4px); }
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('scannerApp', () => ({
            attendances: [],
            isOnline: false,
            pollInterval: null,
            isLaunching: false,
            isCameraOn: false,
            scanMode: 'masuk',
            videoSrc: '',

            initApp() {
                // Check if Python Flask server is already running
                this.checkPythonStatus();
                
                this.pollInterval = setInterval(() => {
                    this.fetchData();
                }, 2000);
            },

            checkPythonStatus() {
                fetch('http://127.0.0.1:5000/status')
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'running') {
                            this.isCameraOn = true;
                            this.scanMode = data.mode;
                            this.videoSrc = 'http://127.0.0.1:5000/video_feed?' + new Date().getTime();
                        }
                    })
                    .catch(err => {
                        this.isCameraOn = false;
                    });
            },

            launchScanner() {
                this.isLaunching = true;

                // Panggil Laravel API untuk menjalankan script Python
                fetch('/api/launch-scanner', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    // Poll /health sampai Flask benar-benar siap (max 10 detik)
                    let attempts = 0;
                    const maxAttempts = 20; // 20 x 500ms = 10 detik
                    const poll = setInterval(() => {
                        attempts++;
                        fetch('http://127.0.0.1:5000/health')
                            .then(r => r.json())
                            .then(h => {
                                if (h.ok) {
                                    clearInterval(poll);
                                    this.isLaunching = false;
                                    this.isCameraOn = true;
                                    this.videoSrc = 'http://127.0.0.1:5000/video_feed?' + new Date().getTime();
                                    this.setMode(this.scanMode);
                                }
                            })
                            .catch(() => {
                                // Server belum ready, lanjut polling
                                if (attempts >= maxAttempts) {
                                    clearInterval(poll);
                                    this.isLaunching = false;
                                    alert("Server Python tidak merespons. Pastikan Python & dependensi terinstall dengan benar.");
                                }
                            });
                    }, 500);
                })
                .catch(err => {
                    console.error(err);
                    this.isLaunching = false;
                    alert("Gagal meluncurkan server Python.");
                });
            },

            stopScanner() {
                // Matikan server python
                fetch('http://127.0.0.1:5000/shutdown', { method: 'POST' })
                    .then(() => {
                        this.isCameraOn = false;
                        this.videoSrc = '';
                    })
                    .catch(e => {
                        // Kalo catch biasanya emang connection refused karena server mati
                        this.isCameraOn = false;
                        this.videoSrc = '';
                    });
            },

            setMode(mode) {
                this.scanMode = mode;
                // Update mode di Python Server
                if (this.isCameraOn) {
                    fetch('http://127.0.0.1:5000/set_mode', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ mode: mode })
                    }).catch(e => console.error("Gagal set mode", e));
                }
            },

            handleStreamError() {
                if (this.isCameraOn) {
                    // Jika gambar error tapi diset on, berarti server mati/belum siap
                    console.log("Stream error, retrying...");
                    setTimeout(() => {
                        if (this.isCameraOn) {
                            this.videoSrc = 'http://127.0.0.1:5000/video_feed?' + new Date().getTime();
                        }
                    }, 2000);
                }
            },

            fetchData() {
                fetch('/api/monitor/latest')
                    .then(res => res.json())
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
