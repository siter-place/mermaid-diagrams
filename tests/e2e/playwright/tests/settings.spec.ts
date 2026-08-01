import { test, expect } from '../fixtures/wordpress';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe('Settings UI', () => {
  test('saves rendering section and persists after reload', async ({ adminPage: page }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-settings`);
    await expect(page.getByTestId('mdm-settings-shell')).toBeVisible();
    await expect(page.getByTestId('mdm-settings-nav-rendering')).toBeVisible();

    const toolbarToggle = page.getByRole('checkbox', { name: /show default toolbar/i });
    await expect(toolbarToggle).toBeVisible();
    const initialChecked = await toolbarToggle.isChecked();
    await toolbarToggle.setChecked(!initialChecked);

    await page.getByTestId('mdm-settings-save').click();
    await expect(page.getByTestId('snackbar')).toContainText(/settings saved/i);

    await page.reload();
    await expect(page.getByTestId('mdm-settings-shell')).toBeVisible();
    await expect(toolbarToggle).toBeChecked({ checked: !initialChecked });

    await expect(page).toHaveScreenshot('settings-rendering.png', {
      maxDiffPixelRatio: 0.02,
    });
  });
});
