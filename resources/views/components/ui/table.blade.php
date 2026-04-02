@props([
    'columns' => [],
    'data' => [],
    'sortField' => null,
    'sortDirection' => 'asc',
    'emptyMessage' => 'Tidak ada data',
    'emptySubMessage' => 'Silakan tambah data baru',
    'emptyIcon' => 'heroicon-o-inbox',
    'striped' => true,
    'wrapperClass' => '',
    'tableClass' => '',
])

@php
    $hasColumns = ! empty($columns);

    if ($data instanceof \Illuminate\Pagination\AbstractPaginator) {
        $rows = collect($data->items());
    } elseif ($data instanceof \Illuminate\Support\Collection) {
        $rows = $data;
    } elseif (is_array($data)) {
        $rows = collect($data);
    } elseif ($data === null) {
        $rows = collect();
    } else {
        $rows = collect($data);
    }

    $isEmpty = $rows->isEmpty();

    // Keep heroicon-like values safe when icon package is unavailable.
    $isHeroiconAlias = str_contains((string) $emptyIcon, 'heroicon');
    $resolvedEmptyIcon = $isHeroiconAlias ? 'inbox' : $emptyIcon;
@endphp

<div class="rounded-2xl border border-base-content/10 bg-base-100 shadow-sm {{ $wrapperClass }}" style="overflow-x: auto; overflow-y: visible !important;">
    <table class="table {{ $striped ? 'table-zebra' : '' }} {{ $tableClass }}" style="position: relative;">
        @if ($hasColumns)
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th class="{{ $column['class'] ?? '' }}">
                            @if (($column['sortable'] ?? false) && ! empty($column['field']))
                                <button wire:click="sortBy('{{ $column['field'] }}')" class="flex items-center justify-center gap-1 w-full hover:text-primary" type="button">
                                    {{ $column['label'] ?? '' }}

                                    @if ($sortField === $column['field'])
                                        @if ($sortDirection === 'asc')
                                            <i data-lucide="chevron-up" class="h-4 w-4"></i>
                                        @else
                                            <i data-lucide="chevron-down" class="h-4 w-4"></i>
                                        @endif
                                    @else
                                        <i data-lucide="chevrons-up-down" class="h-4 w-4 opacity-30"></i>
                                    @endif
                                </button>
                            @else
                                {{ $column['label'] ?? '' }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
        @elseif (isset($head))
            <thead>{{ $head }}</thead>
        @endif

        <tbody>
            @if ($hasColumns)
                @if ($isEmpty)
                    <tr>
                        <td colspan="{{ count($columns) }}" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2 text-base-content/60">
                                <i data-lucide="{{ $resolvedEmptyIcon }}" class="w-12 h-12 opacity-30"></i>
                                <p class="text-lg font-semibold">{{ $emptyMessage }}</p>
                                <p class="text-sm">{{ $emptySubMessage }}</p>
                            </div>
                        </td>
                    </tr>
                @else
                    {{ $slot }}
                @endif
            @else
                {{ $slot }}
            @endif
        </tbody>
    </table>
</div>
