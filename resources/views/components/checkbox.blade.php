@props(['disabled' => false])

<input type="checkbox" @disabled($disabled) {{ $attributes->merge(['class' => 'rounded border-slate-300 text-green-600 shadow-xs focus:ring-green-500']) }}>
