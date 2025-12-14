@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'placeholder' => '',
    'helper' => '',
    'disabled' => false,
    'class' => ''
])

@php
    $inputId = $name . '_' . uniqid();
@endphp

<div class="mb-3 {{ $class }}">
    @if($label)
        <label for="{{ $inputId }}" class="form-label fw-medium">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="input-group">
        <input 
            type="{{ $type }}" 
            class="form-control @error($name) is-invalid @enderror" 
            id="{{ $inputId }}" 
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge(['class' => 'form-control']) }}
        >
        
        @error($name)
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    @if($helper)
        <div class="form-text text-muted small mt-1">
            {{ $helper }}
        </div>
    @endif
</div>
