// Contract scaffold: activate in Phase 05 when its acceptance prerequisites are implemented.
import { test, expect } from '../fixtures/wordpress';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe.skip('Diagram library', () => {
  test('creates, searches, previews, and trashes a diagram', async ({ adminPage: page }) => {
    const uniqueTitle = `E2E Flow ${Date.now()}`;

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await page.getByRole('button', { name: /add diagram/i }).click();

    await page.getByLabel(/title/i).fill(uniqueTitle);
    await page.getByRole('textbox', { name: /mermaid source/i }).fill(
      'flowchart TD\n  A[Start] --> B[Finish]'
    );
    await page.getByRole('button', { name: /^save/i }).click();
    await expect(page.getByText(/saved/i)).toBeVisible();

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await page.getByRole('searchbox').fill(uniqueTitle);
    await expect(page.getByRole('row', { name: new RegExp(uniqueTitle) })).toBeVisible();

    await page.getByRole('button', { name: new RegExp(`preview ${uniqueTitle}`, 'i') }).click();
    await expect(page.locator('svg').first()).toBeVisible();
    await page.getByRole('button', { name: /close/i }).click();

    await page.getByRole('button', { name: new RegExp(`trash ${uniqueTitle}`, 'i') }).click();
    await page.getByRole('button', { name: /confirm/i }).click();
    await expect(page.getByText(/moved to trash/i)).toBeVisible();
  });
});
