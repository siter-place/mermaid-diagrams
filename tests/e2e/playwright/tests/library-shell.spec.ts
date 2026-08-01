import { test, expect } from '../fixtures/wordpress';
import { seedDiagramWithPage } from '../helpers/seed-diagram';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe('Diagram library shell', () => {
  test('shows library shell with DataViews', async ({ adminPage: page }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-library-shell')).toBeVisible();
    await expect(page.getByRole('searchbox')).toBeVisible({ timeout: 10000 });
  });

  test('shows populated table state with DataViews', async ({ adminPage: page }) => {
    const title = `Shell E2E ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-library-shell')).toBeVisible();

    await expect(
      page.getByRole('row', { name: new RegExp(title) })
    ).toBeVisible({ timeout: 15000 });

    await expect(page).toHaveScreenshot('library-populated.png', {
      maxDiffPixelRatio: 0.02,
    });
  });

  test('supports keyboard focus through shell controls', async ({ adminPage: page }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-library-shell')).toBeVisible({ timeout: 10000 });
    await page.keyboard.press('Tab');
    const focused = page.locator(':focus');
    await expect(focused).toBeVisible();
  });

  test('opens quick create modal with live preview', async ({ adminPage: page }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-library-shell')).toBeVisible({ timeout: 10000 });
    await page.getByRole('button', { name: /add diagram/i }).click();

    await expect(page.getByRole('heading', { name: /create diagram/i })).toBeVisible();
    await page.getByLabel(/title/i).fill('Quick Create Visual Test');
    await page
      .getByRole('textbox', { name: /mermaid source/i })
      .fill('flowchart TD\n  A[Start] --> B[Finish]');

    await expect(page.getByTestId('mdm-diagram-viewport')).toBeVisible();
    await expect(page.locator('.mdm-diagram-viewport svg').first()).toBeVisible({
      timeout: 10000,
    });

    await expect(page).toHaveScreenshot('quick-create-modal.png', {
      maxDiffPixelRatio: 0.02,
    });
  });
});
