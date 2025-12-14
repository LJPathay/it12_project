<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Sync table with dark mode on page load
        const syncTableDark = () => {
            const isDark = document.body.classList.contains('bg-dark');
            const table = document.querySelector('.table-modern');
            if (table) {
                table.classList.toggle('table-dark', isDark);
            }
        };

        // Sync on load
        syncTableDark();

        // Watch for theme changes
        const observer = new MutationObserver(() => {
            syncTableDark();
        });
        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['class']
        });

        // Filter functionality
        const filterChips = document.querySelectorAll('.filter-chip');
        const appointmentRows = document.querySelectorAll('.table-modern tbody tr');

        filterChips.forEach(chip => {
            chip.addEventListener('click', () => {
                // Update active state
                filterChips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');

                const filter = chip.dataset.filter;

                // Filter rows
                appointmentRows.forEach(row => {
                    if (filter === 'all') {
                        row.style.display = '';
                    } else {
                        const statusBadge = row.querySelector('.status-badge');
                        if (statusBadge && statusBadge.textContent.trim().toLowerCase() === filter) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        });

        // Search functionality
        const searchInput = document.getElementById('appointmentSearch');
        const searchBox = searchInput.closest('.search-box');
        const clearBtn = document.getElementById('clearSearch');

        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();

            // Toggle clear button
            if (searchTerm) {
                searchBox.classList.add('has-value');
            } else {
                searchBox.classList.remove('has-value');
            }

            // Filter rows
            appointmentRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            searchBox.classList.remove('has-value');
            appointmentRows.forEach(row => row.style.display = '');
        });

        // Toast notifications for actions
        const cancelBtns = document.querySelectorAll('.cancel-appointment-btn');
        const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
        const confirmCancelBtn = document.getElementById('confirmCancelBtn');

        cancelBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();

                const appointmentId = btn.dataset.appointmentId;
                const cancelUrl = btn.dataset.cancelUrl;
                const appointmentDate = btn.dataset.appointmentDate;
                const appointmentTime = btn.dataset.appointmentTime;
                const serviceType = btn.dataset.serviceType;

                // Populate modal with appointment details
                const detailsContainer = document.getElementById('cancelAppointmentDetails');
                detailsContainer.innerHTML = `
                        <div class="confirmation-detail" style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f3f4;">
                            <span style="font-weight: 600; color: #495057;">Date:</span>
                            <span style="color: #6c757d;">${new Date(appointmentDate).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                        </div>
                        <div class="confirmation-detail" style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f3f4;">
                            <span style="font-weight: 600; color: #495057;">Time:</span>
                            <span style="color: #6c757d;">${appointmentTime}</span>
                        </div>
                        <div class="confirmation-detail" style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
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
        });

        // Confirm cancel button - use direct event listener without Bootstrap modal interference
        confirmCancelBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const cancelUrl = confirmCancelBtn.dataset.cancelUrl;
            const csrfToken = confirmCancelBtn.dataset.csrfToken;

            console.log('Cancel button clicked, URL:', cancelUrl);

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

                // Add PUT method
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);

                // Append to body and submit
                document.body.appendChild(form);
                console.log('Submitting form to:', form.action);
                form.submit();
            } else {
                console.error('No cancel URL found');
                if (window.toast && typeof window.toast.error === 'function') {
                    window.toast.error('Error: Could not cancel appointment', 'Error');
                } else {
                    alert('Error: Could not cancel appointment');
                }
            }
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
