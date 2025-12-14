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
        <!-- Treatment Record Section -->
        <div class="form-section">
            <h6><i class="fas fa-file-medical me-2"></i>Individual Treatment Record</h6>
            <p class="text-muted small mb-3">
                <i class="fas fa-info-circle me-1"></i>
                This information helps us provide better healthcare. Fields are optional and auto-filled
                from your previous records.
            </p>

            <!-- Demographics -->
            <div class="mb-4">
                <h6 class="text-muted small fw-bold">Family Information</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="mother_name" class="form-label">Mother's Name</label>
                        <input type="text" class="form-control" id="mother_name" name="mother_name"
                            value="{{ old('mother_name', $user->mother_name) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="father_name" class="form-label">Father's Name</label>
                        <input type="text" class="form-control" id="father_name" name="father_name"
                            value="{{ old('father_name', $user->father_name) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="religion" class="form-label">Religion</label>
                        <input type="text" class="form-control" id="religion" name="religion"
                            value="{{ old('religion', $user->religion) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="marital_status" class="form-label">Marital Status</label>
                        <select class="form-control" id="marital_status" name="marital_status">
                            <option value="">Select Status</option>
                            <option value="single" {{ old('marital_status', $user->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married" {{ old('marital_status', $user->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                            <option value="widowed" {{ old('marital_status', $user->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="separated" {{ old('marital_status', $user->marital_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                            <option value="co-habitation" {{ old('marital_status', $user->marital_status) == 'co-habitation' ? 'selected' : '' }}>Co-habitation
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="educational_attainment" class="form-label">Educational
                            Attainment</label>
                        <input type="text" class="form-control" id="educational_attainment"
                            name="educational_attainment"
                            value="{{ old('educational_attainment', $user->educational_attainment) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="occupation" class="form-label">Occupation</label>
                        <input type="text" class="form-control" id="occupation" name="occupation"
                            value="{{ old('occupation', $user->occupation) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="accompanying_person" class="form-label">Accompanying Person</label>
                        <input type="text" class="form-control" id="accompanying_person"
                            name="accompanying_person"
                            value="{{ old('accompanying_person', $user->accompanying_person) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="accompanying_relationship" class="form-label">Relationship</label>
                        <input type="text" class="form-control" id="accompanying_relationship"
                            name="accompanying_relationship"
                            value="{{ old('accompanying_relationship', $user->accompanying_relationship) }}">
                    </div>
                </div>

                <!-- Spouse Information (shown if married) -->
                <div id="spouseInfo" class="mt-3"
                    style="display: {{ old('marital_status', $user->marital_status) == 'married' ? 'block' : 'none' }}">
                    <h6 class="text-muted small fw-bold mt-3">Spouse Information</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="spouse_name" class="form-label">Spouse Name</label>
                            <input type="text" class="form-control" id="spouse_name" name="spouse_name"
                                value="{{ old('spouse_name', $user->spouse_name) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="spouse_age" class="form-label">Spouse Age</label>
                            <input type="number" class="form-control" id="spouse_age" name="spouse_age"
                                value="{{ old('spouse_age', $user->spouse_age) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="spouse_occupation" class="form-label">Spouse Occupation</label>
                            <input type="text" class="form-control" id="spouse_occupation"
                                name="spouse_occupation"
                                value="{{ old('spouse_occupation', $user->spouse_occupation) }}">
                        </div>
                        @if($user->gender == 'female')
                            <div class="col-md-12">
                                <label for="maiden_name" class="form-label">Maiden Name</label>
                                <input type="text" class="form-control" id="maiden_name" name="maiden_name"
                                    value="{{ old('maiden_name', $user->maiden_name) }}">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Personal/Social History -->
            <div class="mb-4">
                <h6 class="text-muted small fw-bold">Personal/Social History</h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="smoker" name="smoker"
                                value="1" {{ old('smoker', $user->smoker) ? 'checked' : '' }}>
                            <label class="form-check-label" for="smoker">Smoker</label>
                        </div>
                        <input type="text" class="form-control form-control-sm mt-1"
                            id="smoker_packs_per_year" name="smoker_packs_per_year" placeholder="Packs/year"
                            value="{{ old('smoker_packs_per_year', $user->smoker_packs_per_year) }}">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="drinks_alcohol"
                                name="drinks_alcohol" value="1" {{ old('drinks_alcohol', $user->drinks_alcohol) ? 'checked' : '' }}>
                            <label class="form-check-label" for="drinks_alcohol">Drinks Alcohol</label>
                        </div>
                        <input type="text" class="form-control form-control-sm mt-1" id="alcohol_specify"
                            name="alcohol_specify" placeholder="Specify"
                            value="{{ old('alcohol_specify', $user->alcohol_specify) }}">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="illicit_drug_use"
                                name="illicit_drug_use" value="1" {{ old('illicit_drug_use', $user->illicit_drug_use) ? 'checked' : '' }}>
                            <label class="form-check-label" for="illicit_drug_use">Illicit Drug Use</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="multiple_sexual_partners"
                                name="multiple_sexual_partners" value="1" {{ old('multiple_sexual_partners', $user->multiple_sexual_partners) ? 'checked' : '' }}>
                            <label class="form-check-label" for="multiple_sexual_partners">Multiple Sexual
                                Partners</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_pwd" name="is_pwd"
                                value="1" {{ old('is_pwd', $user->is_pwd) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_pwd">PWD</label>
                        </div>
                        <input type="text" class="form-control form-control-sm mt-1" id="pwd_specify"
                            name="pwd_specify" placeholder="Specify"
                            value="{{ old('pwd_specify', $user->pwd_specify) }}">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="has_sti" name="has_sti"
                                value="1" {{ old('has_sti', $user->has_sti) ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_sti">STI</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="has_allergies"
                                name="has_allergies" value="1" {{ old('has_allergies', $user->has_allergies) ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_allergies">Allergies</label>
                        </div>
                        <input type="text" class="form-control form-control-sm mt-1" id="allergies_specify"
                            name="allergies_specify" placeholder="Specify allergies"
                            value="{{ old('allergies_specify', $user->allergies_specify) }}">
                    </div>
                    <div class="col-md-12">
                        <label for="social_history_others" class="form-label">Others</label>
                        <textarea class="form-control" id="social_history_others"
                            name="social_history_others"
                            rows="2">{{ old('social_history_others', $user->social_history_others) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Family History -->
            <div class="mb-4">
                <h6 class="text-muted small fw-bold">Family History</h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="family_hypertension"
                                name="family_hypertension" value="1" {{ old('family_hypertension', $user->family_hypertension) ? 'checked' : '' }}>
                            <label class="form-check-label" for="family_hypertension">Hypertension</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="family_diabetes"
                                name="family_diabetes" value="1" {{ old('family_diabetes', $user->family_diabetes) ? 'checked' : '' }}>
                            <label class="form-check-label" for="family_diabetes">Diabetes</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="family_goiter"
                                name="family_goiter" value="1" {{ old('family_goiter', $user->family_goiter) ? 'checked' : '' }}>
                            <label class="form-check-label" for="family_goiter">Goiter</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="family_cancer"
                                name="family_cancer" value="1" {{ old('family_cancer', $user->family_cancer) ? 'checked' : '' }}>
                            <label class="form-check-label" for="family_cancer">Cancer</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="family_history_others" class="form-label">Others</label>
                        <textarea class="form-control" id="family_history_others"
                            name="family_history_others"
                            rows="2">{{ old('family_history_others', $user->family_history_others) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Patient Medical History -->
            <div class="mb-4">
                <h6 class="text-muted small fw-bold">Patient Medical History</h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="history_uti"
                                name="history_uti" value="1" {{ old('history_uti', $user->history_uti) ? 'checked' : '' }}>
                            <label class="form-check-label" for="history_uti">UTI</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="history_hypertension"
                                name="history_hypertension" value="1" {{ old('history_hypertension', $user->history_hypertension) ? 'checked' : '' }}>
                            <label class="form-check-label" for="history_hypertension">Hypertension</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="history_diabetes"
                                name="history_diabetes" value="1" {{ old('history_diabetes', $user->history_diabetes) ? 'checked' : '' }}>
                            <label class="form-check-label" for="history_diabetes">Diabetes</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="history_goiter"
                                name="history_goiter" value="1" {{ old('history_goiter', $user->history_goiter) ? 'checked' : '' }}>
                            <label class="form-check-label" for="history_goiter">Goiter</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="history_cancer"
                                name="history_cancer" value="1" {{ old('history_cancer', $user->history_cancer) ? 'checked' : '' }}>
                            <label class="form-check-label" for="history_cancer">Cancer</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="history_tuberculosis"
                                name="history_tuberculosis" value="1" {{ old('history_tuberculosis', $user->history_tuberculosis) ? 'checked' : '' }}>
                            <label class="form-check-label" for="history_tuberculosis">Tuberculosis</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="medical_history_others" class="form-label">Others</label>
                        <textarea class="form-control" id="medical_history_others"
                            name="medical_history_others"
                            rows="2">{{ old('medical_history_others', $user->medical_history_others) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Previous Surgeries & Maintenance Medicine -->
            <div class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="previous_surgeries" class="form-label">Previous Surgeries</label>
                        <textarea class="form-control" id="previous_surgeries" name="previous_surgeries"
                            rows="3"
                            placeholder="List any previous surgeries...">{{ old('previous_surgeries', $user->previous_surgeries) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="maintenance_medicine" class="form-label">Maintenance Medicine</label>
                        <textarea class="form-control" id="maintenance_medicine" name="maintenance_medicine"
                            rows="3"
                            placeholder="List current medications...">{{ old('maintenance_medicine', $user->maintenance_medicine) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Immunization Section -->
        <div class="form-section">
            <h6><i class="fas fa-syringe me-2"></i>Immunization Records</h6>

            <div class="row g-3">
                <div class="col-md-6">
                    <h6 class="text-muted small fw-bold">For Children</h6>
                    <div class="row g-2">
                        @php
                            $childVaccines = ['BCG', 'DPT1', 'DPT2', 'DPT3', 'OPV1', 'OPV2', 'OPV3', 'Measles', 'Hepatitis B1', 'Hepatitis B2', 'Hepatitis B3', 'Hepatitis A'];
                        @endphp
                        @foreach($childVaccines as $vaccine)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        id="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}"
                                        name="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}" value="1"
                                        {{ old('imm_' . strtolower(str_replace(' ', '_', $vaccine)), $user->immunization->{strtolower(str_replace(' ', '_', $vaccine))} ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}">{{ $vaccine }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted small fw-bold">For Elderly and Immunocompromised</h6>
                    <div class="row g-2">
                        @php
                            $elderlyVaccines = ['Varicella', 'HPV', 'Pneumococcal', 'MMR', 'Flu Vaccine', 'None'];
                        @endphp
                        @foreach($elderlyVaccines as $vaccine)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        id="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}"
                                        name="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}" value="1"
                                        {{ old('imm_' . strtolower(str_replace(' ', '_', $vaccine)), $user->immunization->{strtolower(str_replace(' ', '_', $vaccine))} ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}">{{ $vaccine }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- COVID-19 Immunization -->
            <div class="mt-4">
                <h6 class="text-muted small fw-bold">COVID-19 Immunization Status</h6>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="covid_vaccine_name" class="form-label">Vaccine Name</label>
                        <input type="text" class="form-control" id="covid_vaccine_name"
                            name="covid_vaccine_name"
                            value="{{ old('covid_vaccine_name', $user->immunization->covid_vaccine_name ?? '') }}"
                            placeholder="e.g., Pfizer, Moderna, Sinovac">
                    </div>
                    <div class="col-md-3">
                        <label for="covid_first_dose" class="form-label">1st Dose</label>
                        <input type="date" class="form-control" id="covid_first_dose"
                            name="covid_first_dose"
                            value="{{ old('covid_first_dose', $user->immunization->covid_first_dose ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="covid_second_dose" class="form-label">2nd Dose</label>
                        <input type="date" class="form-control" id="covid_second_dose"
                            name="covid_second_dose"
                            value="{{ old('covid_second_dose', $user->immunization->covid_second_dose ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="covid_booster1" class="form-label">Booster 1</label>
                        <input type="date" class="form-control" id="covid_booster1" name="covid_booster1"
                            value="{{ old('covid_booster1', $user->immunization->covid_booster1 ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="covid_booster2" class="form-label">Booster 2</label>
                        <input type="date" class="form-control" id="covid_booster2" name="covid_booster2"
                            value="{{ old('covid_booster2', $user->immunization->covid_booster2 ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

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
