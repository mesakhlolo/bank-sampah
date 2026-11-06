@props(['variant' => 'slate'])

@php
    $variants = [
        'green' => 'bg-green-50 text-green-700 ring-green-600/20',
        'red' => 'bg-red-50 text-red-700 ring-red-600/20',
        'yellow' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'gray' => 'bg-slate-100 text-slate-600 ring-slate-500/20',
        'slate' => 'bg-slate-100 text-slate-600 ring-slate-500/20',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset '.($variants[$variant] ?? $variants['slate'])]) }}>
    {{ $slot }}
</span>
