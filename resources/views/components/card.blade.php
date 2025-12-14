@props(['noPadding' => false])

<div {{ $attributes->merge(['class' => 'card shadow-sm rounded-3 mb-4' . ($noPadding ? ' p-0' : ' p-4')]) }}>
    {{ $slot }}
</div>