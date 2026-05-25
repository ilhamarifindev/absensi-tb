@extends('layouts.app')

@section('title', 'Scanner | SMK Taruna Bangsa')

@section('content')
<div x-data="scannerApp()" x-init="initScanner()" class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-900 relative w-full font-outfit">
    <!-- Scanner Header -->
    <header class="glass-dark border-b-0 h-20 flex items-center justify-between px-6 shrink-0 z-20 absolute top-0 left-0 right-0">
        <a href="/dashboard" class="flex items-center gap-3 text-white hover:text-emerald-400 transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
            <span class="font-medium">Kembali ke Dashboard</span>
        </a>
        <div class="flex flex-col items-end">
            <span class="font-bold text-lg text-white tracking-wide">QR SCANNER</span>
            <span class="text-xs flex items-center gap-1.5" :class="isScanning ? 'text-emerald-400' : 'text-slate-400'">
                <span class="w-1.5 h-1.5 rounded-full" :class="isScanning ? 'bg-emerald-500 animate-pulse' : 'bg-slate-500'"></span>
                <span x-text="isScanning ? 'SISTEM AKTIF' : 'DIJEDA'"></span>
            </span>
        </div>
    </header>

    <!-- Main Scanner Area -->
    <main class="flex-1 relative flex flex-col lg:flex-row">
        
        <!-- Camera Viewport -->
        <div class="flex-1 relative bg-black flex items-center justify-center overflow-hidden">
            <!-- Simulated Camera Background Pattern -->
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
            
            <!-- Real Camera Div -->
            <div id="qr-reader" class="w-full h-full max-w-2xl max-h-[80vh] rounded-2xl overflow-hidden [&>video]:object-cover border-none bg-black"></div>

            <!-- Frame Overlay (Only visual) -->
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center z-10" x-show="isScanning">
                <div class="relative w-72 h-72 sm:w-96 sm:h-96">
                    <!-- Frame Corners -->
                    <div class="absolute top-0 left-0 w-12 h-12 border-t-4 border-l-4 border-emerald-500 rounded-tl-xl"></div>
                    <div class="absolute top-0 right-0 w-12 h-12 border-t-4 border-r-4 border-emerald-500 rounded-tr-xl"></div>
                    <div class="absolute bottom-0 left-0 w-12 h-12 border-b-4 border-l-4 border-emerald-500 rounded-bl-xl"></div>
                    <div class="absolute bottom-0 right-0 w-12 h-12 border-b-4 border-r-4 border-emerald-500 rounded-br-xl"></div>
                    
                    <!-- Neon Scan Line -->
                    <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 shadow-[0_0_15px_3px_rgba(52,211,153,0.8)] rounded-full animate-[scan_2s_ease-in-out_infinite_alternate]"></div>

                    <!-- Overlay center clear -->
                    <div class="absolute inset-0 bg-emerald-500/5 shadow-[inset_0_0_50px_rgba(16,185,129,0.1)] rounded-xl backdrop-blur-[1px]"></div>
                </div>
            </div>

            <!-- Loading overlay when fetching API -->
            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm z-20 flex flex-col items-center justify-center text-emerald-400" x-show="isLoading" x-transition>
                <i data-lucide="loader-2" class="w-12 h-12 animate-spin mb-4"></i>
                <p class="font-medium tracking-widest font-mono text-sm">MEMPROSES DATA...</p>
            </div>

            <!-- Controls (Mobile Bottom, Desktop Center Bottom) -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex gap-4 z-30">
                <button @click="toggleScanner()" class="w-14 h-14 rounded-full text-white flex items-center justify-center transition-all hover:scale-110 shadow-lg" :class="isScanning ? 'bg-rose-600 hover:bg-rose-500 shadow-rose-500/50' : 'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-500/50'">
                    <i data-lucide="power" class="w-6 h-6"></i>
                </button>
                <button @click="switchCamera()" class="w-14 h-14 rounded-full glass-dark text-white flex items-center justify-center hover:bg-white/10 transition-all hover:scale-110" x-show="cameras.length > 1">
                    <i data-lucide="camera-reverse" class="w-6 h-6"></i>
                </button>
            </div>
        </div>

        <!-- Result Sidebar (Right) -->
        <div class="w-full lg:w-[400px] glass-dark border-l border-slate-700/50 z-10 flex flex-col relative h-[45vh] lg:h-full lg:rounded-tl-3xl shadow-[-10px_0_30px_rgba(0,0,0,0.5)]">
            <div class="p-6 border-b border-slate-700/50">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-6 h-6 text-emerald-400"></i>
                    Hasil Scan
                </h2>
            </div>
            
            <div class="flex-1 p-6 overflow-y-auto custom-scrollbar flex flex-col items-center justify-center relative">
                
                <!-- Waiting State -->
                <div class="text-center opacity-50" x-show="!result">
                    <i data-lucide="qr-code" class="w-20 h-20 text-slate-500 mx-auto mb-4 opacity-50"></i>
                    <p class="text-sm text-slate-400 font-mono tracking-widest">MENUNGGU SCAN...</p>
                </div>

                <!-- Success State -->
                <div class="w-full bg-slate-800/80 rounded-2xl border border-emerald-500/30 p-6 relative overflow-hidden group shadow-lg shadow-emerald-900/20" x-show="result && result.success" x-transition.scale.duration.300ms style="display: none;">
                    <div class="absolute -top-20 -right-20 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl"></div>
                    
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-24 h-24 rounded-full bg-slate-700 border-4 border-emerald-500 p-1 mb-4 relative">
                            <img :src="result?.student_photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(result?.student_name || 'X')}&background=334155&color=fff&size=100`" class="w-full h-full rounded-full object-cover" alt="Student">
                            <div class="absolute bottom-0 right-0 w-6 h-6 bg-emerald-500 rounded-full border-2 border-slate-800 flex items-center justify-center">
                                <i data-lucide="check" class="w-3 h-3 text-white"></i>
                            </div>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-white mb-1" x-text="result?.student_name">Nama</h3>
                        <p class="text-emerald-400 font-medium mb-4 tracking-wide">NIS: <span x-text="result?.student_nis"></span></p>
                        
                        <div class="w-full bg-slate-900/50 rounded-xl p-4 grid grid-cols-2 gap-4 text-left">
                            <div>
                                <p class="text-xs text-slate-400 mb-1 uppercase tracking-wider">Kelas</p>
                                <p class="text-sm text-white font-medium" x-text="result?.student_class"></p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 mb-1 uppercase tracking-wider">Waktu</p>
                                <p class="text-sm text-emerald-400 font-bold" x-text="result?.time"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error State -->
                <div class="w-full bg-slate-800/80 rounded-2xl border border-rose-500/30 p-6 relative overflow-hidden group shadow-lg shadow-rose-900/20" x-show="result && !result.success" x-transition.scale.duration.300ms style="display: none;">
                    <div class="absolute -top-20 -right-20 w-40 h-40 bg-rose-500/20 rounded-full blur-3xl"></div>
                    
                    <div class="relative z-10 flex flex-col items-center text-center py-6">
                        <div class="w-16 h-16 rounded-full bg-rose-500/20 border border-rose-500 flex items-center justify-center mb-4">
                            <i data-lucide="x-circle" class="w-8 h-8 text-rose-500"></i>
                        </div>
                        
                        <h3 class="text-xl font-bold text-white mb-2">Gagal</h3>
                        <p class="text-rose-400 text-sm" x-text="result?.message">Error message</p>
                    </div>
                </div>

                <!-- Small History List -->
                <div class="w-full mt-auto pt-8">
                    <p class="text-sm text-slate-400 font-medium mb-4">Riwayat Sesi Ini</p>
                    <div class="space-y-3">
                        <template x-for="history in histories" :key="history.id">
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-800/50 border border-slate-700/50">
                                <div class="flex items-center gap-3">
                                    <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(history.name)}&background=334155&color=fff`" class="w-8 h-8 rounded-full" alt="avatar">
                                    <div>
                                        <p class="text-sm text-white font-medium" x-text="history.name"></p>
                                        <p class="text-xs" :class="history.success ? 'text-emerald-400' : 'text-rose-400'" x-text="history.message"></p>
                                    </div>
                                </div>
                                <span class="text-xs text-slate-400 font-mono" x-text="history.time"></span>
                            </div>
                        </template>
                        <div x-show="histories.length === 0" class="text-xs text-slate-500 text-center py-2">Belum ada scan.</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Beep Sound Audio Element -->
    <audio id="beep-sound" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
    </audio>
</div>

<style>
    @keyframes scan {
        0% { top: 0; }
        100% { top: calc(100% - 4px); }
    }
    /* Hide some default styling from html5-qrcode */
    #qr-reader img { display: none !important; }
    #qr-reader span { color: white; }
    #qr-reader__dashboard_section_csr button { 
        background: #059669; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 500;
        cursor: pointer;
    }
</style>

<!-- Load html5-qrcode -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('scannerApp', () => ({
            html5QrcodeScanner: null,
            isScanning: false,
            isLoading: false,
            result: null,
            cameras: [],
            currentCameraId: null,
            histories: [], // list of {id, name, success, message, time}
            
            initScanner() {
                // Get cameras
                Html5Qrcode.getCameras().then(devices => {
                    if (devices && devices.length) {
                        this.cameras = devices;
                        this.currentCameraId = devices[0].id; // default to first camera
                        this.startScanner();
                    }
                }).catch(err => {
                    console.error("Error getting cameras", err);
                    alert("Akses kamera ditolak atau tidak ditemukan.");
                });
            },

            startScanner() {
                this.html5QrcodeScanner = new Html5Qrcode("qr-reader");
                
                this.html5QrcodeScanner.start(
                    { facingMode: "environment" }, // prefer back camera
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0,
                    },
                    (decodedText, decodedResult) => {
                        this.handleScan(decodedText);
                    },
                    (errorMessage) => {
                        // console.log(errorMessage); // silent failure for continuous scan
                    }
                ).then(() => {
                    this.isScanning = true;
                }).catch(err => {
                    console.error(err);
                });
            },

            stopScanner() {
                if (this.html5QrcodeScanner) {
                    this.html5QrcodeScanner.stop().then(() => {
                        this.isScanning = false;
                        this.html5QrcodeScanner.clear();
                    }).catch(err => {
                        console.error(err);
                    });
                }
            },

            toggleScanner() {
                if (this.isScanning) {
                    this.stopScanner();
                } else {
                    this.startScanner();
                }
            },

            switchCamera() {
                // Feature if multiple cameras available
                this.stopScanner();
                setTimeout(() => {
                    this.startScanner(); // Simplification: restarts with default environment mode. 
                    // To do properly: cycle through this.cameras[].id
                }, 500);
            },

            handleScan(qrCode) {
                // Prevent duplicate processing
                if (this.isLoading || !this.isScanning) return;
                
                this.isLoading = true;
                this.stopScanner(); // Pause scanner while processing
                this.playBeep();

                // Call API
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch('/api/scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ qr_code: qrCode })
                })
                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                .then(res => {
                    const data = res.body;
                    if (res.status === 200 && data.success) {
                        this.result = {
                            success: true,
                            student_name: data.data.student.name,
                            student_nis: data.data.student.nis,
                            student_class: data.data.student.class_name,
                            time: data.data.time,
                        };
                        this.addHistory(data.data.student.name, true, 'Berhasil Scan', data.data.time);
                    } else {
                        this.result = {
                            success: false,
                            message: data.message || 'Terjadi kesalahan sistem.'
                        };
                        this.addHistory('Unknown', false, data.message || 'Gagal', new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute:'2-digit' }));
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.result = {
                        success: false,
                        message: 'Koneksi ke server terputus.'
                    };
                })
                .finally(() => {
                    this.isLoading = false;
                    
                    // Resume scanning after 3 seconds
                    setTimeout(() => {
                        this.result = null; // hide result
                        this.startScanner(); // resume
                    }, 3000);
                });
            },

            playBeep() {
                const audio = document.getElementById('beep-sound');
                if (audio) {
                    audio.currentTime = 0;
                    audio.play().catch(e => console.log('Audio blocked by browser', e));
                }
            },

            addHistory(name, success, message, time) {
                this.histories.unshift({
                    id: Date.now(),
                    name: name,
                    success: success,
                    message: message,
                    time: time
                });
                
                // Keep only last 4
                if (this.histories.length > 4) {
                    this.histories.pop();
                }
            }
        }));
    });
</script>
@endsection
