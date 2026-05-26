@php
    $currentRoute = request()->route()?->getName();
@endphp

<aside class="bg-emerald-900 text-emerald-50 w-64 flex-shrink-0 hidden md:flex flex-col transition-all duration-300" :class="{ 'w-20': !sidebarOpen }">
    <!-- Sidebar Header -->
    <div class="h-20 flex items-center justify-center border-b border-emerald-800/50 px-4">
        <a href="/dashboard" class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg backdrop-blur-sm">
                TB
            </div>
            <div class="flex flex-col overflow-hidden whitespace-nowrap transition-all duration-300" x-show="sidebarOpen" x-transition.opacity>
                <span class="font-bold text-lg text-white leading-tight">Admin Panel</span>
                <span class="text-xs text-emerald-300">SMK Taruna Bangsa</span>
            </div>
        </a>
    </div>

    <!-- Sidebar Menu -->
    <div class="flex-1 overflow-y-auto py-6 px-3 custom-scrollbar">
        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl transition-colors group {{ $currentRoute === 'dashboard' ? 'bg-emerald-800/80 text-white border border-emerald-700/50 shadow-inner' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 {{ $currentRoute === 'dashboard' ? 'text-emerald-300' : 'text-emerald-400' }} group-hover:text-emerald-300 transition-colors"></i>
                <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-transition.opacity>Dashboard</span>
            </a>
            
            <a href="{{ route('attendances.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl transition-colors group {{ str_starts_with($currentRoute ?? '', 'attendances') ? 'bg-emerald-800/80 text-white border border-emerald-700/50 shadow-inner' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                <i data-lucide="calendar-check" class="w-5 h-5 {{ str_starts_with($currentRoute ?? '', 'attendances') ? 'text-emerald-300' : 'text-emerald-400' }} group-hover:text-emerald-300 transition-colors"></i>
                <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-transition.opacity>Absensi</span>
            </a>

            <a href="{{ route('students.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl transition-colors group {{ str_starts_with($currentRoute ?? '', 'students') ? 'bg-emerald-800/80 text-white border border-emerald-700/50 shadow-inner' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                <i data-lucide="users" class="w-5 h-5 {{ str_starts_with($currentRoute ?? '', 'students') ? 'text-emerald-300' : 'text-emerald-400' }} group-hover:text-emerald-300 transition-colors"></i>
                <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-transition.opacity>Data Siswa</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl text-emerald-100 hover:bg-emerald-800/50 hover:text-white transition-colors group">
                <i data-lucide="graduation-cap" class="w-5 h-5 text-emerald-400 group-hover:text-emerald-300 transition-colors"></i>
                <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-transition.opacity>Data Guru</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl text-emerald-100 hover:bg-emerald-800/50 hover:text-white transition-colors group">
                <i data-lucide="calendar-days" class="w-5 h-5 text-emerald-400 group-hover:text-emerald-300 transition-colors"></i>
                <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-transition.opacity>Jadwal</span>
            </a>
            
            <a href="{{ route('monitor') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl transition-colors group {{ str_starts_with($currentRoute ?? '', 'monitor') ? 'bg-emerald-800/80 text-white border border-emerald-700/50 shadow-inner' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                <i data-lucide="monitor" class="w-5 h-5 {{ str_starts_with($currentRoute ?? '', 'monitor') ? 'text-emerald-300' : 'text-emerald-400' }} group-hover:text-emerald-300 transition-colors"></i>
                <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-transition.opacity>Live Monitor Scanner</span>
            </a>
            <a href="{{ route('logs.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl transition-colors group {{ str_starts_with($currentRoute ?? '', 'logs') ? 'bg-emerald-800/80 text-white border border-emerald-700/50 shadow-inner' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                <i data-lucide="scroll-text" class="w-5 h-5 {{ str_starts_with($currentRoute ?? '', 'logs') ? 'text-emerald-300' : 'text-emerald-400' }} group-hover:text-emerald-300 transition-colors"></i>
                <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-transition.opacity>Log Aktivitas</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl text-emerald-100 hover:bg-emerald-800/50 hover:text-white transition-colors group">
                <i data-lucide="settings" class="w-5 h-5 text-emerald-400 group-hover:text-emerald-300 transition-colors"></i>
                <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-transition.opacity>Pengaturan</span>
            </a>
        </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-emerald-800/50 space-y-3">
        <!-- Logout -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-emerald-200 hover:bg-rose-600/20 hover:text-rose-300 transition-colors text-sm">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                <span class="whitespace-nowrap" x-show="sidebarOpen" x-transition.opacity>Logout</span>
            </button>
        </form>

        <!-- Collapse Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" class="flex items-center justify-center w-full p-2 rounded-lg bg-emerald-800 hover:bg-emerald-700 text-white transition-colors">
            <i data-lucide="chevron-left" class="w-5 h-5 transition-transform" :class="{ 'rotate-180': !sidebarOpen }"></i>
        </button>
    </div>
</aside>
