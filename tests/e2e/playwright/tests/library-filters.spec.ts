import { test, expect } from '../fixtures/wordpress';
import { seedDiagramWithPage } from '../helpers/seed-diagram';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe('Diagram library filters', () => {
  test('persists search filter in URL after reload', async ({ adminPage: page }) => {
    const title = `Filter URL ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await page.locator('.mdm-filter-bar__search input').fill(title);
    await expect(
      page.getByTestId('mdm-diagram-table').getByRole('row', { name: new RegExp(title) })
    ).toBeVisible({ timeout: 15000 });

    await expect(page).toHaveURL(/search=/);

    await page.reload();
    await expect(
      page.getByTestId('mdm-diagram-table').getByRole('row', { name: new RegExp(title) })
    ).toBeVisible({ timeout: 15000 });
  });
});
