@props([
    'style' => 'dropdown', // dropdown, buttons, links, minimal
    'showFlags' => true,
    'showText' => true,
    'size' => 'normal' // small, normal, large
])

<div class="language-switcher-wrapper">
    @livewire('language-management::language-switcher', [
        'style' => $style,
        'showFlags' => $showFlags,
        'showText' => $showText
    ])
</div>