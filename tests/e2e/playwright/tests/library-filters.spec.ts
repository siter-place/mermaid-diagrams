import { test, expect } from '../fixtures/wordpress';
import { seedDiagramWithPage } from '../helpers/seed-diagram';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe('Diagram library filters', () => {
  test('search filters diagrams in DataViews table', async ({ adminPage: page }) => {
    const title = `Filter Search ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-library-shell')).toBeVisible({ timeout: 10000 });

    await page.getByRole('searchbox').fill(title);
    await expect(
      page.getByRole('row', { name: new RegExp(title) })
    ).toBeVisible({ timeout: 15000 });
  });
});
