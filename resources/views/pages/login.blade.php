@extends('layouts.app')

@section('title', 'Login | SMK Taruna Bangsa')

@section('content')
    <div class="flex-1 w-full min-h-screen flex bg-slate-50">
        <!-- Left Section: Image & Branding (Hidden on mobile) -->
        <div
            class="hidden lg:flex lg:w-1/2 relative bg-emerald-900 overflow-hidden items-center justify-center flex-col p-12">
            <!-- Background Overlay -->
            <div
                class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80')] bg-cover bg-center opacity-20">
            </div>
            <div
                class="absolute inset-0 bg-gradient-to-t from-emerald-950 via-emerald-900/80 to-emerald-900/40 mix-blend-multiply">
            </div>

            <!-- Floating decorative shapes -->
            <div class="absolute top-20 right-20 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 left-20 w-72 h-72 bg-teal-500/20 rounded-full blur-3xl animate-pulse"
                style="animation-delay: 2s;"></div>

            <div class="relative z-10 w-full max-w-lg animate-[fadeIn_1s_ease-out]">
                <div
                    class="glass px-6 py-4 rounded-2xl inline-flex items-center gap-4 mb-8 border-white/10 bg-white/5 backdrop-blur-md">
                    <div
                        class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/50">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-xl tracking-wide">SMK Taruna Bangsa</h2>
                        <p class="text-emerald-300 text-sm">Disiplin, Berprestasi, Berkarakter</p>
                    </div>
                </div>

                <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight mb-6 font-outfit">
                    Selamat Datang di <br />
                    Sistem Absensi Digital
                </h1>

                <p class="text-emerald-100 text-lg leading-relaxed mb-10 opacity-90">
                    Akses portal admin untuk memantau kehadiran, mengelola data siswa, dan mencetak laporan secara
                    real-time.
                </p>

                <div class="flex gap-4">
                    <div class="flex -space-x-4">
                        <x-avatar name="Admin 1" size="w-12 h-12" bg="bg-emerald-500" text="text-white"
                            class="border-2 border-emerald-900" />
                        <x-avatar name="Admin 2" size="w-12 h-12" bg="bg-emerald-600" text="text-white"
                            class="border-2 border-emerald-900" />
                        <x-avatar name="Admin 3" size="w-12 h-12" bg="bg-emerald-700" text="text-white"
                            class="border-2 border-emerald-900" />
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="flex items-center text-amber-400 text-sm">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-sm text-emerald-100 mt-1">Digunakan oleh seluruh staf</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Section: Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-12 relative animate-[slideUp_0.8s_ease-out]">
            <!-- Mobile background shapes -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-100 rounded-full blur-3xl opacity-50 lg:hidden"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-teal-100 rounded-full blur-3xl opacity-50 lg:hidden"></div>

            <div class="w-full max-w-md relative z-10">
                <!-- Mobile Logo -->
                <div class="lg:hidden mb-8 text-center flex flex-col items-center">
                    <div
                        class="w-16 h-16 bg-emerald-600 rounded-2xl flex items-center justify-center text-white font-bold shadow-lg shadow-emerald-600/30 mb-4">
                        <i data-lucide="graduation-cap" class="w-8 h-8"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">SMK Taruna Bangsa</h2>
                    <p class="text-slate-500 text-sm mt-1">Sistem Absensi Digital</p>
                </div>

                <!-- Login Card -->
                <div
                    class="bg-white/80 backdrop-blur-xl border border-white rounded-[2rem] p-8 lg:p-10 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] relative overflow-hidden">
                    <!-- Top glow line -->
                    <div
                        class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-400 via-emerald-600 to-teal-500">
                    </div>

                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-slate-800 mb-2 font-outfit">Login Admin</h2>
                        <p class="text-slate-500">Silakan masuk dengan akun yang terdaftar.</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-sm text-rose-600">
                            <div class="flex items-center gap-2 font-medium mb-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                Login Gagal
                            </div>
                            <p>{{ $errors->first() }}</p>
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                        @csrf
                        <!-- Email Field -->
                        <div class="space-y-2 group">
                            <label for="email"
                                class="text-sm font-medium text-slate-700 group-focus-within:text-emerald-600 transition-colors">Email
                                Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="mail"
                                        class="w-5 h-5 text-slate-400 group-focus-within:text-emerald-600 transition-colors"></i>
                                </div>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all"
                                    placeholder="admin@smktarunabangsa.sch.id" required>
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-2 group">
                            <label for="password"
                                class="text-sm font-medium text-slate-700 group-focus-within:text-emerald-600 transition-colors">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="lock"
                                        class="w-5 h-5 text-slate-400 group-focus-within:text-emerald-600 transition-colors"></i>
                                </div>
                                <input type="password" id="password" name="password"
                                    class="w-full pl-11 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all"
                                    placeholder="••••••••" required>
                                <button type="button"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="flex items-center justify-between text-sm">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" name="remember"
                                    class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500/20 cursor-pointer accent-emerald-600">
                                <span class="text-slate-600 group-hover:text-slate-800 transition-colors">Ingat saya</span>
                            </label>
                            <a href="#"
                                class="text-emerald-600 font-medium hover:text-emerald-700 hover:underline transition-all">Lupa
                                Password?</a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-600/20 hover:shadow-emerald-600/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 group">
                            <span>Masuk ke Dashboard</span>
                            <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>

                    <div class="mt-8 text-center">
                        <p class="text-sm text-slate-500">Bukan admin? <a href="/"
                                class="text-emerald-600 font-medium hover:underline">Kembali ke Beranda</a></p>
                    </div>
                </div>

                <div class="mt-8 text-center text-sm text-slate-400">
                    &copy; {{ date('Y') }} SMK Taruna Bangsa. All rights reserved.
                </div>
            </div>
        </div>
    </div>
@endsection