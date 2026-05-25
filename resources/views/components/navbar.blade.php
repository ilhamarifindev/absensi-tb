<nav class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-slate-100 shadow-sm transition-all duration-300" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-emerald-600/30 group-hover:scale-105 transition-transform">
                        TB
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-xl text-slate-800 leading-tight">SMK Taruna Bangsa</span>
                        <span class="text-xs text-emerald-600 font-medium">Sistem Absensi Digital</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="#" class="text-emerald-600 font-semibold relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-full after:h-0.5 after:bg-emerald-600 after:transition-all">Home</a>
                <a href="#" class="text-slate-600 hover:text-emerald-600 font-medium relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-0.5 after:bg-emerald-600 hover:after:w-full after:transition-all duration-300">Profile</a>
                <a href="#" class="text-slate-600 hover:text-emerald-600 font-medium relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-0.5 after:bg-emerald-600 hover:after:w-full after:transition-all duration-300">Program Keahlian</a>
                <a href="#" class="text-slate-600 hover:text-emerald-600 font-medium relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-0.5 after:bg-emerald-600 hover:after:w-full after:transition-all duration-300">Fasilitas</a>
                <a href="#" class="text-slate-600 hover:text-emerald-600 font-medium relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-0.5 after:bg-emerald-600 hover:after:w-full after:transition-all duration-300">Extracurricular</a>
                <a href="#" class="text-slate-600 hover:text-emerald-600 font-medium relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-0.5 after:bg-emerald-600 hover:after:w-full after:transition-all duration-300">Pengumuman</a>
                <a href="#" class="text-slate-600 hover:text-emerald-600 font-medium relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-0.5 after:bg-emerald-600 hover:after:w-full after:transition-all duration-300">Contact</a>
            </div>

            <!-- Action Buttons -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="/login" class="text-slate-600 hover:text-emerald-600 font-medium px-4 py-2 rounded-lg hover:bg-emerald-50 transition-colors">Login</a>
                <a href="/dashboard" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-2.5 rounded-xl shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/50 transition-all hover:-translate-y-0.5">Dashboard</a>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button class="text-slate-600 hover:text-emerald-600 focus:outline-none p-2 rounded-lg hover:bg-emerald-50 transition-colors">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
    </div>
</nav>
