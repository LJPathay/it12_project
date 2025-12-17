<form method="POST" action="{{ route('patient.store-appointment') }}">
    @csrf

    <!-- Patient Information -->
    <div class="form-section">
        <h6><i class="fas fa-user me-2"></i>Patient Information</h6>
        <p class="text-muted small mb-3">
            <i class="fas fa-info-circle me-1"></i>
            Your information has been pre-filled from your account. You can modify any details as
            needed.
        </p>
        <div class="row">
            <div class="col-md-6">
                <label for="patient_name" class="form-label">Full Name *</label>
                <input type="text" class="form-control @error('patient_name') is-invalid @enderror"
                    id="patient_name" name="patient_name"
                    value="{{ old('patient_name', $user->name ?? '') }}" required>
                @error('patient_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="patient_phone" class="form-label">Phone Number *</label>
                <input type="tel" class="form-control @error('patient_phone') is-invalid @enderror"
                    id="patient_phone" name="patient_phone"
                    value="{{ old('patient_phone', $user->phone ?? '') }}" required>
                @error('patient_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Appointment Details -->
    <div class="form-section">
        <h6><i class="fas fa-calendar me-2"></i>Appointment Details</h6>

        <!-- Calendar Legend -->
        <div class="calendar-legend">
            <div class="legend-item">
                <div class="legend-color available"></div>
                <span>Available</span>
            </div>
            <div class="legend-item">
                <div class="legend-color occupied"></div>
                <span>Fully Booked</span>
            </div>
            <div class="legend-item">
                <div class="legend-color partially-occupied"></div>
                <span>Limited Slots</span>
            </div>
            <div class="legend-item">
                <div class="legend-color selected"></div>
                <span>Selected</span>
            </div>
            <div class="legend-item">
                <div class="legend-color unavailable"></div>
                <span>Unavailable</span>
            </div>
        </div>

        <!-- Calendar View -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Select Appointment Date & Time
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @include('patient.partials.book-appointment-calendar')
                    
                    @include('patient.partials.book-appointment-time-slots')
                </div>
            </div>
        </div>

        <div class="mt-3">
            <label for="service_id" class="form-label">Service Needed *</label>
            <select class="form-control @error('service_id') is-invalid @enderror" id="service_id"
                name="service_id" required>
                <option value="" disabled selected>Select Service</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                        {{ $service->name }}
                    </option>
                @endforeach
            </select>

            @error('service_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Additional Information -->
    <div class="form-section">
        <h6><i class="fas fa-notes-medical me-2"></i>Additional Information</h6>
        <div class="mb-3">
            <label for="medical_history" class="form-label">Medical History</label>
            <textarea class="form-control @error('medical_history') is-invalid @enderror"
                id="medical_history" name="medical_history" rows="3"
                placeholder="Please provide any relevant medical history...">{{ old('medical_history') }}</textarea>
            @error('medical_history')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Additional Notes</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                rows="2"
                placeholder="Any additional information or special requests...">{{ old('notes') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>


    @if($user->age >= 6)

        <!-- Consent Section -->
        <div class="form-section">
            <h6><i class="fas fa-file-signature me-2"></i>Patient Consent (Pagtugot sa Pasyente)</h6>
            <div class="alert alert-info">
                <p class="mb-2"><strong>IN ENGLISH:</strong></p>
                <p class="small mb-2">I have read and understand the ITR (Individual Treatment Record) after
                    I have been made aware of its contents. During informational conversation, I was
                    informed in a comprehensive way about the need and importance of the Primary Care
                    Benefit Package (PCB), Konsulta Program, eKonsulta System, iClinicSys (Integrated Clinic
                    Information System) by the CHO DHO/UHC representative. All my questions during the said
                    conversation were addressed accordingly and I have also been given enough time to decide
                    on this matter.</p>
                <p class="small mb-2">Furthermore, I permit CHO DHO/UHC to encode the information concerning
                    my person and the collected data regarding my health status and consultations conducted
                    by the same on the information system as mentioned above and provide the same to the
                    Philippine Health Information Exchange - Lite (PHIE Lite), the Department of Health
                    (DOH) National Health Data Reporting and PhilhealthKonsulta Program.</p>

                <p class="mb-2 mt-3"><strong>SA BISAYA:</strong></p>
                <p class="small mb-0">Ako nakabasa ug nakasabot sa ITR (Individual Treatment Record)
                    paghuman naa ko gipahibalo sa sulod niini ug gipasabot sa importansya sa Primary Care
                    Benefits Package (PCB), Konsulta Program, eKonsulta System ug iClinicsys (Integrated
                    Clinic Information System) sa taga- CHO DHO/UHC. Tanan nakong pangutana kay natubag ug
                    ako na hatagan ug saktong panahon para mahatag saakoa ang pagtugot.
                    Ako pud gihatagan ug permission na isulod ang impormasyon sa akong pagkatao, sa estado
                    sa akong panlawas ug sa nahimo ug mahimong konsultasyon na mga information systems na
                    nahisgot ug ang maong impormasyon ihatag sa Philippine Health Information Exchange - Lte
                    (PHIE Lite), sa Department of Health (DOH) National Health Data Reporting ug Phil Health
                    Konsulta Program.
                    Ang resulta saakong konsultasyon ug estado saakong panglawas kay pwede nako mapangayo o
                    saakong tag tungod. Pwede ra pud nako ikansel akining gihatag nako pagtugot sa CHO
                    DHO/UHC na walay ihatag na rason ug walay maski unsa na desbintaha saakong medical
                    napagtambal..</p>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="consent_signed" name="consent_signed"
                    value="1" {{ old('consent_signed', $user->consent_signed) ? 'checked' : '' }} required>
                <label class="form-check-label fw-bold" for="consent_signed">
                    I have read and agree to the terms above / Nakabasa ug miuyon sa mga termino sa ibabaw
                </label>
            </div>
        </div>
    @endif

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-times me-2"></i>Cancel
        </a>
        <button type="button" id="bookAppointmentBtn" class="btn btn-primary">
            <i class="fas fa-calendar-check me-2"></i>Book Appointment
        </button>
    </div>
</form>
