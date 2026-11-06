@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-lg bg-green-50 px-3 py-2 text-start text-sm font-medium text-green-700 focus:outline-none'
            : 'block w-full rounded-lg px-3 py-2 text-start text-sm font-medium text-slate-600 transition duration-150 ease-in-out hover:bg-slate-50 hover:text-slate-900 focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
