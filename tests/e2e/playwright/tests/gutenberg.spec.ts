// Contract scaffold: activate in Phase 06 when its acceptance prerequisites are implemented.
import { test, expect } from '../fixtures/wordpress';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe.skip('Gutenberg Mermaid block', () => {
  test('creates inline diagram and renders it on the front end', async ({ adminPage: page }) => {
    await page.goto(`${baseURL}/wp-admin/post-new.php`);
    await page.getByRole('textbox', { name: /add title/i }).fill(`Diagram post ${Date.now()}`);

    await page.getByRole('button', { name: /add block/i }).click();
    await page.getByPlaceholder(/search/i).fill('Mermaid Diagram');
    await page.getByRole('option', { name: /mermaid diagram/i }).click();

    await page.getByRole('button', { name: /create inline diagram/i }).click();
    await page.getByRole('textbox', { name: /mermaid source/i }).fill(
      'flowchart TD\n  Start --> Finish'
    );
    await expect(page.locator('svg').first()).toBeVisible();

    await page.getByRole('button', { name: /publish/i }).click();
    await page.getByRole('button', { name: /publish/i }).last().click();
    const viewLink = page.getByRole('link', { name: /view post/i });
    await expect(viewLink).toBeVisible();
    await viewLink.click();

    await expect(page.locator('.wp-block-mdm-diagram svg')).toBeVisible();
    await expect(page.getByRole('button', { name: /zoom in/i })).toBeVisible();
  });
});
