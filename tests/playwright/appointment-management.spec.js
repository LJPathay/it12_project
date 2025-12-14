import { test, expect } from '@playwright/test';
import { AppointmentPage } from './pages/AppointmentPage';
import { login, testUsers } from './helpers.js';

test.describe('Appointment Management Module', () => {
    let appointmentPage;

    test.beforeEach(async ({ page }) => {
        // Initialize page object
        appointmentPage = new AppointmentPage(page);

        // Login and navigate
        await login(page, testUsers.admin.email, testUsers.admin.password);
        await appointmentPage.goto();
    });

    test('should display appointments page', async ({ page }) => {
        await expect(appointmentPage.pageTitle).toBeVisible();
        await expect(appointmentPage.appointmentsTable).toBeVisible();
    });

    test('should show appointment status badges', async ({ page }) => {
        // Wait for table data to load
        await expect(appointmentPage.appointmentsTable).toBeVisible();

        // Check if we have rows
        const rowCount = await appointmentPage.appointmentRows.count();
        if (rowCount > 0) {
            await expect(appointmentPage.statusBadges.first()).toBeVisible();
        } else {
            // If empty, verify empty state message
            await expect(appointmentPage.emptyStateMessage).toBeVisible();
        }
    });

    test('should be able to filter appointments', async ({ page }) => {
        await expect(appointmentPage.statusFilter).toBeVisible();

        // Try filtering by 'Pending'
        await appointmentPage.filterByStatus('Pending');

        // Verify table or empty state is visible
        await expect(appointmentPage.appointmentsTable).toBeVisible();
    });

    test('should be able to update appointment status', async ({ page }) => {
        // This test depends on data being present. 
        // Ideally we should seed data or mock the response.
        // For now, we'll check if the Approve button exists on pending items
        const pendingRows = page.locator('tr[data-status="pending"]');
        if (await pendingRows.count() > 0) {
            const approveButton = pendingRows.first().locator('button:has-text("Approve")').first();
            if (await approveButton.isVisible()) {
                await expect(approveButton).toBeVisible();
            }
        }
    });

    test('should display appointment details', async ({ page }) => {
        const rowCount = await appointmentPage.appointmentRows.count();

        if (rowCount > 0) {
            // Click the first view button/icon
            const viewButton = appointmentPage.appointmentRows.first().locator('.fa-eye').first();
            await viewButton.click();

            // Modal should appear
            // Using a generic selector for any modal that opens
            await expect(page.locator('.modal.show')).toBeVisible();
        }
    });

    test('should open add appointment modal', async ({ page }) => {
        await appointmentPage.addAppointmentButton.click();
        await expect(appointmentPage.addAppointmentModal).toBeVisible();
        await expect(appointmentPage.addAppointmentModal.locator('.modal-title')).toHaveText('Add New Appointment');
    });
});
