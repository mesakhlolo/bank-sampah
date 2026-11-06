@props(['headers' => []])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200']) }}>
        <thead class="bg-gray-50">
            <tr>
                @foreach ($headers as $header)
                    @php
                        $label = is_array($header) ? ($header['label'] ?? '') : $header;
                        $align = is_array($header) ? ($header['align'] ?? 'left') : 'left';
                        $alignClass = match ($align) {
                            'right' => 'text-right',
                            'center' => 'text-center',
                            default => 'text-left',
                        };
                    @endphp
                    <th scope="col" class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 {{ $alignClass }}">
                        {{ $label }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            {{ $slot }}
        </tbody>
        @isset($footer)
            <tfoot class="bg-gray-50 font-semibold text-gray-900">
                {{ $footer }}
            </tfoot>
        @endisset
    </table>
</div>
