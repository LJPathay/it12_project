import { expect } from '@playwright/test';

/**
 * Login Page Object
 */
export class LoginPage {
    /**
     * @param {import('@playwright/test').Page} page
     */
    constructor(page) {
        this.page = page;
        // Selectors
        this.emailInput = page.locator('input[name="email"]');
        this.passwordInput = page.locator('input[name="password"]');
        this.loginButton = page.locator('button[type="submit"]');
        this.rememberCheckbox = page.locator('input[name="remember"]');
        this.forgotPasswordLink = page.locator('a[href*="password.request"]');
        this.registerLink = page.locator('a[href*="register"]');

        // Error messages
        this.invalidFeedback = page.locator('.invalid-feedback');
        this.alertDanger = page.locator('.alert-danger');
    }

    async goto() {
        await this.page.goto('/login');
    }

    /**
     * Login with credentials
     * @param {string} email
     * @param {string} password
     */
    async login(email, password) {
        await this.emailInput.fill(email);
        await this.passwordInput.fill(password);
        await this.loginButton.click();
    }
}
