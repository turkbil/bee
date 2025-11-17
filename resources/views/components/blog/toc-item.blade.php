@props(['item', 'level' => 0])

@php
    $level = max(0, (int) $level);
    $hasChildren = !empty($item['children']);
@endphp

<li class="toc-entry">
    <a
        href="#{{ $item['id'] }}"
        data-target="{{ $item['id'] }}"
        class="toc-link toc-level-{{ $level }}"
    >
        <span class="toc-dot"></span>
        <span class="toc-text">{{ $item['text'] }}</span>
    </a>

    @if($hasChildren)
        <ul class="toc-children">
            @foreach($item['children'] as $child)
                @include('components.blog.toc-item', ['item' => $child, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>
