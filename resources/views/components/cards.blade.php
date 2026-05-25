@props([
    'title' => 'Statistic',
    'value' => '0',
    'icon' => 'activity',
    'color' => 'emerald',
    'trend' => null,
    'trendValue' => null
])

@php
    $colorClasses = [
        'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        'blue' => 'bg-blue-50 text-blue-600 border-blue-100',
        'amber' => 'bg-amber-50 text-amber-600 border-amber-100',
        'rose' => 'bg-rose-50 text-rose-600 border-rose-100',
        'slate' => 'bg-slate-50 text-slate-600 border-slate-100',
    ];
    $selectedColor = $colorClasses[$color] ?? $colorClasses['emerald'];
@endphp

<div class="bg-white rounded-xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">{{ $title }}</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ $value }}</h3>
            
            @if($trend)
                <div class="mt-4 flex items-center text-sm">
                    @if($trend === 'up')
                        <i data-lucide="trending-up" class="w-4 h-4 text-emerald-500 mr-1"></i>
                        <span class="text-emerald-500 font-medium">{{ $trendValue }}</span>
                    @elseif($trend === 'down')
                        <i data-lucide="trending-down" class="w-4 h-4 text-rose-500 mr-1"></i>
                        <span class="text-rose-500 font-medium">{{ $trendValue }}</span>
                    @endif
                    <span class="text-slate-400 ml-2">vs last month</span>
                </div>
            @endif
        </div>
        <div class="p-4 rounded-xl border {{ $selectedColor }} group-hover:scale-110 transition-transform duration-300">
            <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
        </div>
    </div>
</div>
