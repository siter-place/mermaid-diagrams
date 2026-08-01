import { test, expect } from '../fixtures/wordpress';
import { seedDiagramWithPage } from '../helpers/seed-diagram';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe('Diagram library preview accessibility', () => {
  test('supports keyboard focus for preview close control', async ({ adminPage: page }) => {
    const title = `Preview A11y ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(
      page.getByTestId('mdm-diagram-table').getByRole('row', { name: new RegExp(title) })
    ).toBeVisible({ timeout: 15000 });

    await page.getByRole('button', { name: new RegExp(`preview ${title}`, 'i') }).click();
    await expect(page.getByTestId('mdm-preview-panel')).toBeVisible();

    await page.keyboard.press('Tab');
    await page
      .getByTestId('mdm-preview-panel')
      .getByRole('button', { name: /^close$/i })
      .click();
    await expect(page.getByTestId('mdm-preview-panel')).toHaveCount(0);
  });
});
