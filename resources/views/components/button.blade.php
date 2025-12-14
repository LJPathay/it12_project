@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'disabled' => false,
    'block' => false,
    'href' => null
])

@php
    $classes = 'btn btn-' . $variant . ($size ? ' btn-' . $size : '') . ($block ? ' w-100' : '');
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i class="{{ $icon }} me-2"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} {{ $disabled ? 'disabled' : '' }}>
        @if($icon)
            <i class="{{ $icon }} me-2"></i>
        @endif
        {{ $slot }}
    </button>
@endif
