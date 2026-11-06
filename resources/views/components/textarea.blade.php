@props(['disabled' => false, 'rows' => 3])

<textarea rows="{{ $rows }}" @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-lg border-slate-300 text-slate-900 shadow-xs placeholder:text-slate-400 focus:border-green-500 focus:ring-green-500']) }}>{{ $slot }}</textarea>
