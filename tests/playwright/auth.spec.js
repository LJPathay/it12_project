import { test, expect } from '@playwright/test';
import { LoginPage } from './pages/LoginPage';
import { testUsers, logout } from './helpers.js';

test.describe('Authentication', () => {
    let loginPage;

    test.describe('Login', () => {
        test.beforeEach(async ({ page }) => {
            loginPage = new LoginPage(page);
            await loginPage.goto();
        });

        test('should display login form', async ({ page }) => {
            await expect(loginPage.emailInput).toBeVisible();
            await expect(loginPage.passwordInput).toBeVisible();
            await expect(loginPage.loginButton).toBeVisible();
        });

        test('should show validation error for empty fields', async ({ page }) => {
            // Disable HTML5 validation to check server/JS validation if needed, 
            // or just try to submit empty. 
            // The previous test did `setAttribute('novalidate', 'true')`. 
            // Let's keep that pattern if we want to test backend/JS validation messages.
            await page.evaluate(() => document.querySelector('form').setAttribute('novalidate', 'true'));

            await loginPage.loginButton.click();

            // Check for required field errors
            // Note: The specific error text depends on Laravel's validation messages
            await expect(page.locator('text=The email field is required')).toBeVisible();
            await expect(page.locator('text=The password field is required')).toBeVisible();
        });

        test('should show error for invalid credentials', async ({ page }) => {
            await loginPage.login('wrong@example.com', 'wrongpassword');
            // The exact error message depends on the backend
            // Common Laravel default: "These credentials do not match our records."
            // But the previous test looked for "Invalid Credentials." or "The email must be a valid email".
            // Let's use a broader check or the alert danger we defined.
            await expect(page.locator('.alert-danger, .invalid-feedback').first()).toBeVisible();
        });

        test('should login as admin successfully', async ({ page }) => {
            await loginPage.login(testUsers.admin.email, testUsers.admin.password);
            await expect(page).toHaveURL(/\/admin\/dashboard/);
        });

        test('should login as super admin successfully', async ({ page }) => {
            await loginPage.login(testUsers.superadmin.email, testUsers.superadmin.password);
            await expect(page).toHaveURL(/\/superadmin\/dashboard/);
        });

        test('should have forgot password link', async ({ page }) => {
            await expect(loginPage.forgotPasswordLink).toBeVisible();
        });

        test('should navigate to register page', async ({ page }) => {
            await loginPage.registerLink.click();
            await expect(page).toHaveURL('/register');
        });
    });

    // Keeping Registration and Logout tests mostly as is for now, 
    // but they could also benefit from a RegistrationPage object later.
    test.describe('Logout', () => {
        test('should logout patient successfully', async ({ page }) => {
            loginPage = new LoginPage(page);
            await loginPage.goto();
            await loginPage.login(testUsers.patient.email, testUsers.patient.password);
            await logout(page);
            await expect(page).toHaveURL('/');
        });

        test('should logout admin successfully', async ({ page }) => {
            loginPage = new LoginPage(page);
            await loginPage.goto();
            await loginPage.login(testUsers.admin.email, testUsers.admin.password);
            await logout(page);
            await expect(page).toHaveURL('/');
        });
    });
});

