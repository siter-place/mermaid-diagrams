import { test, expect } from '../fixtures/wordpress';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe('Admin Menu Page', () => {
  test('administrator can access Diagrams admin menu page', async ({ adminPage: page }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);

    await expect(page.locator('h1.wp-heading-inline')).toHaveText('Diagrams');
    await expect(page.locator('#mdm-diagram-library-root')).toBeVisible();
    await expect(page.locator('#toplevel_page_mdm-diagrams')).toBeVisible();
    await expect(page.getByTestId('mdm-library-shell')).toBeVisible();

    await expect(page).toHaveScreenshot('admin-menu-shell.png', {
      mask: [page.locator('#wp-admin-bar-my-account')],
      maxDiffPixelRatio: 0.02,
    });
  });
});
