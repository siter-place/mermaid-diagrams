// Contract scaffold: activate in Phase 12 when its acceptance prerequisites are implemented.
import { test, expect } from '../fixtures/wordpress';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe.skip('Flowchart visual editor Beta', () => {
  test.skip('applies a supported visual edit back to Mermaid source', async ({ adminPage: page }) => {
    // Enable after the visual adapter feature flag and stable test IDs are implemented.
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagram-editor&diagram=123`);
    await page.getByRole('tab', { name: /visual/i }).click();
    await expect(page.getByText(/flowchart visual editor/i)).toBeVisible();

    const node = page.locator('[data-testid="visual-node-A"]');
    await node.dragTo(page.locator('[data-testid="visual-drop-target"]'));
    await page.getByRole('button', { name: /apply to code/i }).click();
    await expect(page.getByRole('textbox', { name: /mermaid source/i })).toContainText(/A/);
  });
});
