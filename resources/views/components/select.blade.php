@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-lg border-slate-300 text-slate-900 shadow-xs focus:border-green-500 focus:ring-green-500']) }}>
    {{ $slot }}
</select>
