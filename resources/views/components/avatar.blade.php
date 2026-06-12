@props(['name' => '?', 'size' => 'w-10 h-10', 'bg' => 'bg-slate-200', 'text' => 'text-slate-600', 'rounded' => 'rounded-full'])

@php
    $initials = collect(explode(' ', trim($name)))
        ->filter()
        ->map(fn($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->take(2)
        ->join('');
    if (empty($initials)) $initials = '?';
@endphp

<div {{ $attributes->merge(['class' => "$size $bg $rounded flex items-center justify-center shrink-0 overflow-hidden"]) }}>
    <span class="{{ $text }} font-semibold text-xs select-none">{{ $initials }}</span>
</div>
