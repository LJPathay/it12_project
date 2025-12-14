@props(['icon' => 'fa-box-open', 'title', 'description'])

<div class="text-center py-5">
    <div class="text-primary opacity-50 mb-3">
        <i class="fas {{ $icon }} fa-3x"></i>
    </div>
    <h4 class="fw-bold text-secondary mb-2">{{ $title }}</h4>
    <p class="text-muted mb-4">{{ $description }}</p>

    @if(isset($action))
        <div>
            {{ $action }}
        </div>
    @endif
</div>