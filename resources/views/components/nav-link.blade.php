@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-900 transition duration-150 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400'
            : 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition duration-150 ease-in-out hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
