@extends('layouts.app')

@section('content')

<style>
    .service-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        border-radius: 12px;
    }
    .service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }
    .service-card:active {
        transform: scale(0.98);
    }
    .service-image {
        transition: transform 0.4s ease;
        border-radius: 10px;
    }
    .service-card:hover .service-image {
        transform: scale(1.1);
    }
</style>

<div class="position-relative w-100" style="min-height: 80vh;">
    <div style="
        background: linear-gradient(rgba(255,255,255,0.77), rgba(255,255,255,0.73)), 
                    url('{{ asset('images/clinic-bg.jpg') }}') center/cover no-repeat;
        position:absolute; left:0; top:0; width:100%; height:100%; z-index:0;">
    </div>

    <div class="container position-relative py-5" style="z-index:1;">
        <h2 class="fw-bold mb-5 text-center text-primary">Choose a Service to Get Started</h2>

        <div class="row justify-content-center g-4">

            @php
                $services = [
                    ['name' => 'General Checkup', 'img' => 'gen-checkup.jpg', 'desc' => 'Comprehensive health checkups and consultation for all ages.'],
                    ['name' => 'Prenatal Care', 'img' => 'pre-natal.jpg', 'desc' => 'Regular checkups and guidance for a healthy pregnancy journey.'],
                    ['name' => 'Medical Check-up', 'img' => 'gen-checkup.jpg', 'desc' => 'Routine health assessments to keep you in top shape.'],
                    ['name' => 'Immunization', 'img' => 'immunization.jpg', 'desc' => 'Vaccines for children and adults to prevent diseases.'],
                    ['name' => 'Family Planning', 'img' => 'family-planning.jpg', 'desc' => 'Counseling and services for reproductive health & planning.'],
                ];
            @endphp

            @foreach($services as $service)
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 service-card" 
                         data-bs-toggle="modal" 
                         data-bs-target="#serviceModal"
                         data-service-name="{{ $service['name'] }}"
                         data-service-desc="{{ $service['desc'] }}"
                         data-service-img="{{ asset('images/' . $service['img']) }}">
                        
                        <div class="card-body text-center p-4">
                            <img src="{{ asset('images/' . $service['img']) }}" 
                                 alt="{{ $service['name'] }}" 
                                 class="img-fluid mb-3 service-image" 
                                 style="max-height:140px; object-fit: cover;">
                            <h5 class="card-title fw-bold text-primary">{{ $service['name'] }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($service['desc'], 70) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content overflow-hidden border-0 shadow-lg" style="border-radius: 16px;">
            <div class="row g-0">
                <!-- Left side - Image with gradient overlay -->
                <div class="col-md-5 position-relative" style="background: linear-gradient(135deg, #009fb1 0%, #138496 100%);">
                    <div class="position-absolute top-0 end-0 p-3">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="d-flex align-items-center justify-content-center h-100 p-4">
                        <div class="text-center">
                            <div class="mb-3">
                                <img id="modal-image" src="" alt="" class="img-fluid rounded-3 shadow" style="max-height: 200px; object-fit: cover;">
                            </div>
                            <div class="text-white">
                                <i class="fas fa-check-circle fa-2x mb-2 opacity-75"></i>
                                <p class="mb-0 small opacity-75">Trusted Healthcare Service</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right side - Content -->
                <div class="col-md-7">
                    <div class="p-4">
                        <!-- Header -->
                        <div class="mb-4">
                            <h4 class="fw-bold text-primary mb-2" id="serviceModalLabel"></h4>
                            <p id="modal-description" class="text-muted mb-0"></p>
                        </div>

                        <!-- Features/Benefits -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-dark">
                                <i class="fas fa-star text-warning me-2"></i>What You'll Get
                            </h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span class="text-muted small">Professional healthcare service</span>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span class="text-muted small">Experienced medical staff</span>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span class="text-muted small">Quick and easy booking process</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Call to Action Alert -->
                        <div class="alert alert-primary border-0 mb-4" style="background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, rgba(19, 132, 150, 0.1) 100%);">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle text-primary me-3 fa-lg"></i>
                                <div>
                                    <p class="mb-0 small fw-semibold text-primary">Ready to get started?</p>
                                    <p class="mb-0 small text-muted">Register now to book this service!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-user-plus me-2"></i>Register Now
                            </a>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Maybe Later
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('serviceModal');
    modal.addEventListener('show.bs.modal', function (event) {
        const card = event.relatedTarget; // Card that triggered the modal

        const name = card.getAttribute('data-service-name');
        const desc = card.getAttribute('data-service-desc');
        const img = card.getAttribute('data-service-img');

        // Update modal content
        modal.querySelector('#serviceModalLabel').textContent = name;
        modal.querySelector('#modal-description').textContent = desc;
        modal.querySelector('#modal-image').src = img;
    });
});
</script>

@endsection