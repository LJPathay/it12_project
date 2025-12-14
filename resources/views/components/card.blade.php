@props(['noPadding' => false])

<div {{ $attributes->merge(['class' => 'bg-white shadow-sm rounded-3 mb-4' . ($noPadding ? ' p-0' : ' p-4')]) }}>
    {{ $slot }}
</div>