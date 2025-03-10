@php
    $tables = $this->tables;
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <div>
            {{ $tables['pending'] }}
        </div>

        <div>
            {{ $tables['scoring'] }}
        </div>
    </div>
</x-filament-panels::page>
