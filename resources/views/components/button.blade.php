@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'submit',
])

@php
    $variants = [
        'primary' => 'border-transparent bg-green-600 text-white hover:bg-green-700 focus:ring-green-500 active:bg-green-800',
        'secondary' => 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-green-500',
        'danger' => 'border-transparent bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 active:bg-red-800',
        'ghost' => 'border-transparent bg-transparent text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:ring-green-500',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 rounded-lg border font-semibold transition ease-in-out duration-150 focus:outline-hidden focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 '
        .($variants[$variant] ?? $variants['primary']).' '
        .($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
