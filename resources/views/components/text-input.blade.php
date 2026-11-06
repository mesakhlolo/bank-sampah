@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-slate-300 text-slate-900 shadow-xs placeholder:text-slate-400 focus:border-green-500 focus:ring-green-500']) }}>
