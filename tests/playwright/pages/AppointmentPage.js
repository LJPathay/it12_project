export class AppointmentPage {
    /**
     * @param {import('@playwright/test').Page} page
     */
    constructor(page) {
        this.page = page;

        // Header & Title
        this.pageTitle = page.locator('h1, h2, h3, .page-title').filter({ hasText: /appointment/i });

        // Filters
        this.statusFilter = page.locator('#appointmentStatusFilter');
        this.serviceFilter = page.locator('#appointmentServiceFilter');
        this.searchFilter = page.locator('#appointmentSearch');
        this.resetFiltersButton = page.locator('#appointmentFiltersReset');

        // Table Elements
        this.appointmentsTable = page.locator('.table-modern');
        this.appointmentRows = page.locator('#appointmentsTableBody tr');
        this.emptyStateMessage = page.locator('text=No appointments match your filters');

        // Status Badges
        this.statusBadges = page.locator('.status-badge');

        // Modals
        this.addAppointmentModal = page.locator('#addAppointmentModal');
        this.addAppointmentButton = page.locator('button[data-bs-target="#addAppointmentModal"]');

        // Pagination
        this.paginationContainer = page.locator('#appointmentsPaginationContainer');
    }

    async goto() {
        await this.page.goto('/admin/appointments');
    }

    /**
     * Filter appointments by status
     * @param {string} status 
     */
    async filterByStatus(status) {
        await this.statusFilter.selectOption({ label: status });
        await this.page.waitForTimeout(500);
    }

    /**
     * Search for an appointment
     * @param {string} query 
     */
    async search(query) {
        await this.searchFilter.fill(query);
        await this.page.waitForTimeout(500);
    }
}
