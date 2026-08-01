import { test, expect } from '@playwright/test';

test('wp-env admin and REST index are reachable', async ({ page, request }) => {
  const rest = await request.get('/wp-json/');
  expect(rest.ok()).toBeTruthy();

  await page.goto('/wp-admin/');
  await expect(page.locator('#wpadminbar')).toBeVisible();
  await expect(page).toHaveScreenshot('wp-env-admin-smoke.png', {
    mask: [page.locator('#wp-admin-bar-my-account')],
  });
});
