@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-gray-300 shadow-xs focus:border-green-500 focus:ring-green-500']) }}>
