@extends('layouts.app')

@section('title', 'Welcome | SMK Taruna Bangsa')

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-y-auto w-full custom-scrollbar">
    @include('components.topbar')
    @include('components.navbar')

    <!-- Hero Section -->
    <main class="flex-1 relative overflow-hidden bg-slate-50 flex items-center min-h-[calc(100vh-8rem)]">
        <!-- Abstract Background Shapes -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-emerald-400/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-teal-400/20 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white/40 via-transparent to-transparent"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-12 lg:py-0">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                <!-- Left Content -->
                <div class="max-w-2xl animate-[fadeIn_1s_ease-out]">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-100/50 border border-emerald-200 text-emerald-700 font-medium text-sm mb-6 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Sistem Presensi Digital Terintegrasi
                    </div>
                    
                    <h1 class="text-5xl lg:text-6xl font-bold text-slate-800 leading-[1.1] mb-6 tracking-tight font-outfit">
                        Sistem Absensi <br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">SMK Taruna Bangsa</span>
                    </h1>
                    
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        Platform absensi digital modern yang dirancang khusus untuk mempermudah pemantauan kehadiran siswa dan guru. Dilengkapi dengan teknologi QR Code untuk akurasi dan kecepatan.
                    </p>
                    
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="/login" class="px-8 py-4 bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2">
                            Mulai Absensi
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </a>
                        <a href="#" class="px-8 py-4 bg-white text-slate-700 font-semibold rounded-xl border border-slate-200 shadow-sm hover:border-emerald-200 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-300">
                            Tentang Sekolah
                        </a>
                    </div>
                    
                    <div class="mt-10 flex items-center gap-6 text-sm text-slate-500">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
                            Cepat & Akurat
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
                            Real-time Report
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
                            Mobile Friendly
                        </div>
                    </div>
                </div>

                <!-- Right Content (Image & Elements) -->
                <div class="relative lg:h-[600px] flex items-center justify-center animate-[slideUp_1s_ease-out]">
                    <!-- Main Image Frame -->
                    <div class="relative w-full max-w-md mx-auto">
                        <!-- Decorative background frame -->
                        <div class="absolute inset-0 bg-gradient-to-tr from-emerald-400 to-teal-300 rounded-[2.5rem] rotate-3 scale-105 opacity-50 blur-lg"></div>
                        <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500 to-teal-400 rounded-[2.5rem] -rotate-3 transition-transform duration-500 hover:rotate-0"></div>
                        
                        <!-- Image Container -->
                        <div class="relative bg-slate-100 rounded-[2rem] overflow-hidden border-4 border-white shadow-2xl aspect-[4/5]">
                            <!-- Placeholder for student image -->
                            <div class="absolute inset-0 flex items-center justify-center bg-slate-200">
                                <i data-lucide="image" class="w-20 h-20 text-slate-400"></i>
                                <span class="absolute mt-24 text-slate-500 font-medium">Foto Siswa Berseragam</span>
                            </div>
                        </div>

                        <!-- Floating Badge 1 -->
                        <div class="absolute -left-10 top-1/4 glass px-4 py-3 rounded-2xl flex items-center gap-3 animate-[bounce_3s_ease-in-out_infinite]">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                <i data-lucide="qr-code" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-medium">Metode</p>
                                <p class="text-sm font-bold text-slate-800">Scan QR Code</p>
                            </div>
                        </div>

                        <!-- Floating Badge 2 -->
                        <div class="absolute -right-8 bottom-1/4 glass px-4 py-3 rounded-2xl flex items-center gap-3 animate-[bounce_3.5s_ease-in-out_infinite] delay-150">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-medium">Total Siswa</p>
                                <p class="text-sm font-bold text-slate-800">1,250+</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
