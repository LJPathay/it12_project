<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cancelBtn = document.querySelector('.cancel-appointment-btn');

        if (cancelBtn) {
            const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
            const confirmCancelBtn = document.getElementById('confirmCancelBtn');

            cancelBtn.addEventListener('click', (e) => {
                e.preventDefault();

                const cancelUrl = cancelBtn.dataset.cancelUrl;
                const appointmentDate = cancelBtn.dataset.appointmentDate;
                const appointmentTime = cancelBtn.dataset.appointmentTime;
                const serviceType = cancelBtn.dataset.serviceType;

                // Populate modal with appointment details
                const detailsContainer = document.getElementById('cancelAppointmentDetails');
                detailsContainer.innerHTML = `
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f3f4;">
                            <span style="font-weight: 600; color: #495057;">Date:</span>
                            <span style="color: #6c757d;">${new Date(appointmentDate).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f3f4;">
                            <span style="font-weight: 600; color: #495057;">Time:</span>
                            <span style="color: #6c757d;">${appointmentTime}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
                            <span style="font-weight: 600; color: #495057;">Service:</span>
                            <span style="color: #6c757d;">${serviceType}</span>
                        </div>
                    `;

                // Store the cancel URL
                confirmCancelBtn.dataset.cancelUrl = cancelUrl;
                confirmCancelBtn.dataset.csrfToken = '{{ csrf_token() }}';

                // Show modal
                cancelModal.show();
            });

            // Confirm cancel button
            confirmCancelBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const cancelUrl = confirmCancelBtn.dataset.cancelUrl;
                const csrfToken = confirmCancelBtn.dataset.csrfToken;

                if (cancelUrl) {
                    // Hide modal manually
                    const modalElement = document.getElementById('cancelModal');
                    modalElement.classList.remove('show');
                    modalElement.style.display = 'none';
                    modalElement.setAttribute('aria-hidden', 'true');

                    // Remove backdrop
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }

                    // Remove modal-open class from body
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');

                    // Show toast if available
                    if (window.toast && typeof window.toast.info === 'function') {
                        window.toast.info('Cancelling appointment...', 'Please wait');
                    }

                    // Create form dynamically and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = cancelUrl;
                    form.style.display = 'none';

                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    // Append to body and submit
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

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
