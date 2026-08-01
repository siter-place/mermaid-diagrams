// Contract scaffold: activate in Phase 07 when its acceptance prerequisites are implemented.
import { test, expect } from '@playwright/test';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe.skip('Public diagram interaction', () => {
  test('does not load Mermaid assets on a page without a diagram block', async ({ page }) => {
    const mermaidRequests: string[] = [];
    page.on('request', (request) => {
      if (/mermaid/i.test(request.url())) mermaidRequests.push(request.url());
    });

    await page.goto(`${baseURL}/sample-page/`);
    expect(mermaidRequests).toEqual([]);
  });

  test.skip('zoom, fit, reset, fullscreen and downloads', async ({ page }) => {
    // Seed a published post in a fixture before enabling this test.
    await page.goto(`${baseURL}/e2e-diagram-post/`);
    const block = page.locator('.wp-block-mdm-diagram').first();
    await expect(block.locator('svg')).toBeVisible();
    await block.getByRole('button', { name: /zoom in/i }).click();
    await block.getByRole('button', { name: /fit/i }).click();
    await block.getByRole('button', { name: /reset/i }).click();
  });
});
