@props(['item', 'level' => 0])

@php
    $level = max(0, (int) $level);
    $hasChildren = !empty($item['children']);
@endphp

<li class="toc-entry">
    <a
        href="#{{ $item['id'] }}"
        data-target="{{ $item['id'] }}"
        class="toc-level-{{ $level }}"
    >
        <span class="text-xs font-mono text-slate-400 dark:text-slate-500 min-w-[2rem] uppercase">
            {{ strtoupper($item['tag'] ?? 'H') }}
        </span>
        <span class="flex-1">{{ $item['text'] }}</span>
    </a>

    @if($hasChildren)
        <ul class="mt-1 space-y-1">
            @foreach($item['children'] as $child)
                @include('components.blog.toc-item', ['item' => $child, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>
