import { test, expect } from '../fixtures/wordpress';
import { seedDiagramWithPage } from '../helpers/seed-diagram';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe('Diagram library shell', () => {
  test('shows loading then empty state', async ({ adminPage: page }) => {
    await page.route('**/wp-json/mdm/v1/diagrams*', async (route) => {
      await new Promise((resolve) => setTimeout(resolve, 500));
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          items: [],
          pagination: { page: 1, perPage: 20, totalItems: 0, totalPages: 0 },
          facets: { types: [], statuses: [] },
        }),
      });
    });

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-library-loading')).toBeVisible();
    await expect(page.getByTestId('mdm-library-empty')).toBeVisible();
    await expect(page).toHaveScreenshot('library-empty.png', {
      maxDiffPixelRatio: 0.02,
    });
  });

  test('shows populated table state', async ({ adminPage: page }) => {
    const title = `Shell E2E ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-library-shell')).toBeVisible();
    await expect(page.getByText(title)).toBeVisible({ timeout: 15000 });
    await expect(page.getByTestId('mdm-diagram-table')).toBeVisible();
    await expect(page).toHaveScreenshot('library-populated.png', {
      maxDiffPixelRatio: 0.02,
    });
  });

  test('shows error state with retry', async ({ adminPage: page }) => {
    await page.route('**/wp-json/mdm/v1/diagrams*', async (route) => {
      await route.fulfill({
        status: 500,
        contentType: 'application/json',
        body: JSON.stringify({ code: 'server_error', message: 'Server error' }),
      });
    });

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-library-error')).toBeVisible();
    await expect(page.getByRole('button', { name: /try again/i })).toBeVisible();
  });

  test('supports keyboard focus through shell controls', async ({ adminPage: page }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await page.keyboard.press('Tab');
    const focused = page.locator(':focus');
    await expect(focused).toBeVisible();
  });
});
