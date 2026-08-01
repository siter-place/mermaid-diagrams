import { test as base, expect, type Page } from '@playwright/test';

export type WordPressFixtures = {
  adminPage: Page;
};

async function login(page: Page, username: string, password: string): Promise<void> {
  const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';
  await page.goto(`${baseURL}/wp-login.php`);
  await page.getByLabel(/username|email address/i).fill(username);
  await page.locator('#user_pass').fill(password);
  await page.getByRole('button', { name: /log in/i }).click();
  await expect(page).toHaveURL(/wp-admin/);
}

export const test = base.extend<WordPressFixtures>({
  adminPage: async ({ browser }, use) => {
    const context = await browser.newContext({ acceptDownloads: true });
    const page = await context.newPage();
    await login(
      page,
      process.env.WP_ADMIN_USER ?? 'admin',
      process.env.WP_ADMIN_PASSWORD ?? 'password'
    );
    await use(page);
    await context.close();
  },
});

export { expect };
