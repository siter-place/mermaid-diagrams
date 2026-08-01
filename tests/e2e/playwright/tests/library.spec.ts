import { test, expect } from '../fixtures/wordpress';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe('Diagram library workflows', () => {
  test('creates, searches, previews, and trashes a diagram', async ({ adminPage: page }) => {
    const uniqueTitle = `E2E Flow ${Date.now()}`;

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-library-shell')).toBeVisible({ timeout: 10000 });
    await page.getByRole('button', { name: /add diagram/i }).click();

    await page.getByLabel(/title/i).fill(uniqueTitle);
    await page.getByRole('textbox', { name: /mermaid source/i }).fill(
      'flowchart TD\n  A[Start] --> B[Finish]'
    );
    await page.getByRole('button', { name: /^save/i }).click();
    await expect(page.getByTestId('mdm-notices')).toContainText(/saved/i, { timeout: 15000 });

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await page.getByRole('searchbox').fill(uniqueTitle);
    await expect(
      page.getByRole('row', { name: new RegExp(uniqueTitle) })
    ).toBeVisible({ timeout: 15000 });

    // Preview via the actions menu
    const row = page.getByRole('row', { name: new RegExp(uniqueTitle) });
    await row.getByRole('button', { name: /preview/i }).click();
    await expect(page.getByTestId('mdm-preview-panel')).toBeVisible();
    await page
      .getByTestId('mdm-preview-panel')
      .getByRole('button', { name: /^close$/i })
      .click();

    // Trash via the "Actions" dropdown
    await row.getByRole('button', { name: /actions/i }).click();
    await page.getByRole('menuitem', { name: /trash/i }).click();
    await page.getByRole('button', { name: /confirm/i }).click();
    await expect(page.getByTestId('mdm-notices')).toContainText(/moved to trash/i, {
      timeout: 15000,
    });
  });
});
