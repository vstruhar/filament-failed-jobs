<ul class="text-sm space-y-2 break-all">
    @foreach ($getRecord()->exceptionItems() as $line)
    <li @class(['text-lg' => $loop->first, 'opacity-50' => str($line)->contains('/vendor/')])>
        @if ($loop->index >= 2)
            {{
                str($line)->contains('/vendor/')
                    ? '#' . ($loop->index - 2) . ' ' . str($line)->after('/vendor/')->prepend('/vendor/')
                    : $line
            }}
        @else
            {{ $line }}
        @endif
    </li>
    @endforeach
</ul>
