@php
    $types = [
        'success' => [
            'classes' => 'border-green-200 bg-green-50 text-green-800',
            'path' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'error' => [
            'classes' => 'border-red-200 bg-red-50 text-red-800',
            'path' => 'M12 9v3.75m0 3.75h.008v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'warning' => [
            'classes' => 'border-amber-200 bg-amber-50 text-amber-800',
            'path' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
        ],
    ];
@endphp

@foreach ($types as $type => $config)
    @if (session($type))
        <div {{ $attributes->merge(['class' => 'mb-4 flex items-start gap-3 rounded-xl border px-4 py-3 text-sm shadow-xs '.$config['classes']]) }} role="alert">
            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $config['path'] }}" />
            </svg>
            <p>{{ session($type) }}</p>
        </div>
    @endif
@endforeach
