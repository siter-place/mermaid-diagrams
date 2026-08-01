import { test, expect } from '@playwright/test';

test.describe('Admin Menu Page', () => {
  test('administrator can access Diagrams admin menu page', async ({ page }) => {
    await page.goto('/wp-admin/admin.php?page=mdm-diagrams');

    await expect(page.locator('h1.wp-heading-inline')).toHaveText('Diagrams');
    await expect(page.locator('#mdm-diagram-library-root')).toBeVisible();
    await expect(page.locator('#toplevel_page_mdm-diagrams')).toBeVisible();

    await expect(page).toHaveScreenshot('admin-menu-placeholder.png', {
      mask: [page.locator('#wp-admin-bar-my-account')],
    });
  });
});
