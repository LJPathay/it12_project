@push('scripts')
    <script>
        console.log('Calendar script loading...');

        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM loaded, initializing calendar...');

            // Calendar functionality
            class PatientAppointmentCalendar {
                constructor() {
                    console.log('PatientAppointmentCalendar constructor called');
                    this.currentDate = new Date();
                    this.selectedDate = null;
                    this.selectedTime = null;
                    this.calendarData = [];
                    this.init();
                }

                init() {
                    this.attachEventListeners();
                    this.loadCalendar();
                }

                attachEventListeners() {
                    document.getElementById('prevMonth').addEventListener('click', () => {
                        this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                        this.loadCalendar();
                    });

                    document.getElementById('nextMonth').addEventListener('click', () => {
                        this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                        this.loadCalendar();
                    });

                    // Form validation
                    const form = document.querySelector('form[method="POST"]');
                    if (form) {
                        form.addEventListener('submit', (e) => {
                            const dateInput = document.getElementById('appointment_date');
                            const timeInput = document.getElementById('appointment_time');

                            if (!dateInput.value || !timeInput.value) {
                                e.preventDefault();
                                alert('Please select both date and time for your appointment.');
                                return false;
                            }
                        });
                    }
                }

                async loadCalendar() {
                    const year = this.currentDate.getFullYear();
                    const month = this.currentDate.getMonth() + 1;

                    console.log('Loading calendar for:', year, month);

                    try {
                        const response = await fetch(`/patient/appointments/calendar?year=${year}&month=${month}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        console.log('Response status:', response.status);

                        if (!response.ok) {
                            console.error('Response not ok:', response.statusText);
                            throw new Error('Failed to load calendar data');
                        }

                        const data = await response.json();
                        console.log('Calendar data received:', data);
                        this.calendarData = data.calendar;
                        this.renderCalendar();
                        this.updateMonthDisplay();
                    } catch (error) {
                        console.error('Error loading calendar:', error);
                        document.getElementById('calendarGrid').innerHTML = '<div class="col-12 text-center text-danger">Error loading calendar: ' + error.message + '</div>';
                    }
                }

                renderCalendar() {
                    const calendarGrid = document.getElementById('calendarGrid');
                    calendarGrid.innerHTML = '';

                    // Add day headers
                    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    dayHeaders.forEach(day => {
                        const header = document.createElement('div');
                        header.className = 'calendar-header';
                        header.textContent = day;
                        calendarGrid.appendChild(header);
                    });

                    // Add empty cells for days before month starts
                    const firstDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay();
                    for (let i = 0; i < firstDay; i++) {
                        const emptyDay = document.createElement('div');
                        calendarGrid.appendChild(emptyDay);
                    }

                    // Add calendar days
                    this.calendarData.forEach(dayData => {
                        const dayElement = document.createElement('div');
                        dayElement.className = 'calendar-day';
                        dayElement.textContent = dayData.day;

                        if (dayData.is_weekend) {
                            dayElement.classList.add('weekend');
                            // Visually disable weekends
                            dayElement.style.opacity = '0.5';
                            dayElement.style.cursor = 'not-allowed';
                            dayElement.style.backgroundColor = '#f8f9fa';
                            dayElement.style.pointerEvents = 'none';
                        }

                        if (dayData.is_past) {
                            dayElement.classList.add('past');
                        } else if (dayData.is_fully_occupied) {
                            dayElement.classList.add('occupied');
                        } else if (dayData.occupied_slots > 0) {
                            dayElement.classList.add('partially-occupied');
                        }

                        // Add slot indicator (Only if occupied > 0)
                        if (dayData.occupied_slots > 0) {
                            const indicator = document.createElement('span');
                            indicator.className = 'slot-indicator';
                            indicator.textContent = `${dayData.occupied_slots}/${dayData.total_slots}`;
                            dayElement.appendChild(indicator);
                        }

                        if (!dayData.is_past && !dayData.is_fully_occupied && !dayData.is_weekend) {
                            dayElement.addEventListener('click', () => {
                                this.selectDate(dayData.date);
                            });
                        }

                        calendarGrid.appendChild(dayElement);
                    });
                }

                updateMonthDisplay() {
                    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
                    document.getElementById('currentMonth').textContent =
                        `${monthNames[this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}`;
                }

                async selectDate(date) {
                    this.selectedDate = date;

                    // Update selected state in calendar
                    document.querySelectorAll('.calendar-day').forEach(day => {
                        day.classList.remove('selected');
                    });
                    event.target.classList.add('selected');

                    // Update selected date display
                    const dateObj = new Date(date);
                    const formattedDate = dateObj.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    document.getElementById('selectedDateDisplay').textContent = formattedDate;

                    // Update hidden input
                    document.getElementById('appointment_date').value = date;

                    // Load time slots for selected date
                    await this.loadTimeSlots(date);
                }

                async loadTimeSlots(date) {
                    try {
                        // Get selected service type if available
                        const serviceSelect = document.getElementById('service_id');
                        const serviceType = serviceSelect ? serviceSelect.options[serviceSelect.selectedIndex]?.text : null;
                        
                        let url = `/patient/appointments/slots?date=${date}`;
                        if (serviceType && serviceType !== 'Select a service') {
                            url += `&service_type=${encodeURIComponent(serviceType)}`;
                        }
                        
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) throw new Error('Failed to load time slots');

                        const data = await response.json();
                        this.renderTimeSlots(data.slots);
                    } catch (error) {
                        console.error('Error loading time slots:', error);
                        document.getElementById('timeSlotsGrid').innerHTML = '<div class="col-12 text-center text-danger">Error loading time slots</div>';
                    }
                }

                renderTimeSlots(slots) {
                    const timeSlotsWrapper = document.getElementById('timeSlotsGrid');
                    timeSlotsWrapper.innerHTML = '';
                    
                    // Create the grid container
                    const gridContainer = document.createElement('div');
                    gridContainer.className = 'time-slots-grid';

                    slots.forEach(slot => {
                        const slotElement = document.createElement('div');
                        
                        // Determine slot class based on availability and time
                        let slotClass = 'time-slot';
                        let statusText = '';
                        
                        if (slot.is_past) {
                            slotClass += ' past';
                            statusText = 'Unavailable';
                        } else if (!slot.available) {
                            slotClass += ' occupied';
                            statusText = `Occupied (${slot.occupied_count})`;
                        } else {
                            slotClass += ' available';
                            statusText = 'Available';
                        }
                        
                        slotElement.className = slotClass;

                        const timeElement = document.createElement('div');
                        timeElement.className = 'time';
                        timeElement.textContent = slot.display;

                        const statusElement = document.createElement('div');
                        statusElement.className = 'status';
                        statusElement.textContent = statusText;

                        slotElement.appendChild(timeElement);
                        slotElement.appendChild(statusElement);

                        // Only allow clicking on available slots that haven't passed
                        if (slot.available && !slot.is_past) {
                            slotElement.addEventListener('click', () => {
                                this.selectTimeSlot(slot);
                            });
                        }

                        gridContainer.appendChild(slotElement);
                    });
                    
                    timeSlotsWrapper.appendChild(gridContainer);
                }

                selectTimeSlot(slot) {
                    // Remove previous selection
                    document.querySelectorAll('.time-slot').forEach(s => {
                        s.classList.remove('selected');
                    });

                    // Add selection to clicked slot
                    event.currentTarget.classList.add('selected');

                    // Update hidden input
                    document.getElementById('appointment_time').value = slot.time;
                    this.selectedTime = slot.time;
                }
            }

            // Initialize calendar
            console.log('Creating calendar instance...');
            const calendar = new PatientAppointmentCalendar();
            console.log('Calendar initialized successfully');

            // Step tracking
            const steps = document.querySelectorAll('.step');
            const form = document.querySelector('form[method="POST"]');
            const bookBtn = document.getElementById('bookAppointmentBtn');
            const confirmBtn = document.getElementById('confirmBookingBtn');
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

            // Track form progress and update steps
            const updateStepProgress = () => {
                const hasPatientInfo = document.getElementById('patient_name').value &&
                    document.getElementById('patient_phone').value;
                const hasDateTime = document.getElementById('appointment_date').value &&
                    document.getElementById('appointment_time').value;
                const hasService = document.getElementById('service_id').value;

                // Update step 1
                if (hasPatientInfo) {
                    steps[0].classList.add('completed');
                } else {
                    steps[0].classList.remove('completed');
                }

                // Update step 2
                if (hasDateTime) {
                    steps[1].classList.add('completed');
                } else {
                    steps[1].classList.remove('completed');
                }

                // Update step 3
                if (hasService) {
                    steps[2].classList.add('completed');
                } else {
                    steps[2].classList.remove('completed');
                }
            };

            // Listen for form changes
            form.addEventListener('change', updateStepProgress);
            form.addEventListener('input', updateStepProgress);

            // Book appointment button - show confirmation modal
            bookBtn.addEventListener('click', (e) => {
                e.preventDefault();

                // Validate form
                if (!form.checkValidity()) {
                    form.reportValidity();
                    window.toast.warning('Please fill in all required fields', 'Incomplete Form');
                    return;
                }

                // Check if date and time are selected
                const dateInput = document.getElementById('appointment_date');
                const timeInput = document.getElementById('appointment_time');

                if (!dateInput.value || !timeInput.value) {
                    if (window.toast && typeof window.toast.warning === 'function') {
                        window.toast.warning('Please select both date and time for your appointment', 'Missing Information');
                    } else {
                        alert('Please select both date and time for your appointment');
                    }
                    return;
                }

                // Populate confirmation modal
                const confirmationDetails = document.getElementById('confirmationDetails');
                const patientName = document.getElementById('patient_name').value;
                const patientPhone = document.getElementById('patient_phone').value;
                const appointmentDate = new Date(dateInput.value).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                const appointmentTime = timeInput.value;
                // Get selected service name using the select element
                const serviceSelect = document.getElementById('service_id');
                const serviceType = serviceSelect.options[serviceSelect.selectedIndex].text;
                const medicalHistory = document.getElementById('medical_history').value || 'None provided';
                const notes = document.getElementById('notes').value || 'None';

                confirmationDetails.innerHTML = `
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Patient Name:</span>
                                                    <span class="confirmation-value">${patientName}</span>
                                                </div>
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Phone Number:</span>
                                                    <span class="confirmation-value">${patientPhone}</span>
                                                </div>
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Appointment Date:</span>
                                                    <span class="confirmation-value">${appointmentDate}</span>
                                                </div>
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Appointment Time:</span>
                                                    <span class="confirmation-value">${appointmentTime}</span>
                                                </div>
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Service Type:</span>
                                                    <span class="confirmation-value">${serviceType}</span>
                                                </div>
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Medical History:</span>
                                                    <span class="confirmation-value">${medicalHistory}</span>
                                                </div>
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Additional Notes:</span>
                                                    <span class="confirmation-value">${notes}</span>
                                                </div>
                                            `;

                // Mark step 4 as active
                steps.forEach(s => s.classList.remove('active'));
                steps[3].classList.add('active');

                // Show modal
                confirmationModal.show();
            });

            // Confirm booking button - submit form
            confirmBtn.addEventListener('click', () => {
                confirmationModal.hide();
                if (window.toast && typeof window.toast.info === 'function') {
                    window.toast.info('Submitting your appointment...', 'Please wait');
                }
                form.submit();
            });

            // Show session messages
            @if(session('success'))
                if (window.toast && typeof window.toast.success === 'function') {
                    window.toast.success('{{ session('success') }}');
                }
            @endif

            @if(session('error'))
                if (window.toast && typeof window.toast.error === 'function') {
                    window.toast.error('{{ session('error') }}');
                }
            @endif
                                    });
    </script>
@endpush
