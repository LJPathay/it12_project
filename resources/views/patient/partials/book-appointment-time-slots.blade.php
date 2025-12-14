<!-- Time Slots Column -->
<div class="col-xl-6 col-lg-6 col-md-12">
    <div class="border rounded p-3 time-slots-container">
        <h6 class="mb-3 pb-2 border-bottom text-center" id="selectedDateDisplay">
            Select a date to view available time slots
        </h6>
        <div id="timeSlotsGrid" class="time-slots-grid-wrapper">
            <!-- Time slots will be populated here -->
        </div>
        <input type="hidden" id="appointment_date" name="appointment_date"
            value="{{ old('appointment_date') }}" required>
        @error('appointment_date')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
        
        <input type="hidden" id="appointment_time" name="appointment_time"
            value="{{ old('appointment_time') }}" required>
        @error('appointment_time')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>
