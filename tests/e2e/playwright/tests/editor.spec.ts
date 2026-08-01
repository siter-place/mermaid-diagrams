// Contract scaffold: activate in Phase 08 when its acceptance prerequisites are implemented.
import { test, expect } from '../fixtures/wordpress';
import { expectNonEmptyDownload } from '../helpers/downloads';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe.skip('Diagram editor', () => {
  test('validates, saves, and exports a diagram', async ({ adminPage: page }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagram-editor&action=new`);

    await page.getByLabel(/title/i).fill(`Export test ${Date.now()}`);
    const source = page.getByRole('textbox', { name: /mermaid source/i });
    await source.fill('flowchart LR\n  A --> B');
    await expect(page.locator('svg').first()).toBeVisible();

    await page.getByRole('button', { name: /^save/i }).click();
    await expect(page.getByText(/saved/i)).toBeVisible();

    await expectNonEmptyDownload(
      page,
      () => page.getByRole('button', { name: /download source/i }).click(),
      /\.mmd$/i
    );
    await expectNonEmptyDownload(
      page,
      () => page.getByRole('button', { name: /download svg/i }).click(),
      /\.svg$/i
    );

    await source.fill('flowchart TD\n  A -->');
    await expect(page.getByRole('alert')).toContainText(/invalid|syntax|parse/i);
  });
});
