import { test, expect } from '../fixtures/wordpress';
import { seedDiagramWithPage } from '../helpers/seed-diagram';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe('Diagram library extended workflows', () => {
  test('duplicates a diagram via DataViews action', async ({ adminPage: page }) => {
    const title = `E2E Dup ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(
      page.getByRole('row', { name: new RegExp(title) })
    ).toBeVisible({ timeout: 15000 });

    const row = page.getByRole('row', { name: new RegExp(title) });
    await row.getByRole('button', { name: /duplicate/i }).click();
    await expect(page.getByTestId('mdm-notices')).toContainText(/duplicated/i, {
      timeout: 15000,
    });
  });

  test('validates quick create rejects empty title', async ({ adminPage: page }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-library-shell')).toBeVisible({ timeout: 10000 });
    await page.getByRole('button', { name: /add diagram/i }).click();

    await expect(page.getByRole('heading', { name: /create diagram/i })).toBeVisible();

    await page
      .getByRole('textbox', { name: /mermaid source/i })
      .fill('flowchart TD\n  A --> B');
    await page.getByRole('button', { name: /^save/i }).click();

    await expect(page.locator('.mdm-form-error')).toBeVisible({ timeout: 5000 });
  });

  test('validates quick create rejects invalid mermaid syntax', async ({
    adminPage: page,
  }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-library-shell')).toBeVisible({ timeout: 10000 });
    await page.getByRole('button', { name: /add diagram/i }).click();

    await expect(page.getByRole('heading', { name: /create diagram/i })).toBeVisible();

    await page.getByLabel(/title/i).fill('Invalid Syntax Test');
    await page
      .getByRole('textbox', { name: /mermaid source/i })
      .fill('not valid mermaid {{{');
    await page.getByRole('button', { name: /^save/i }).click();

    await expect(page.locator('.mdm-form-error')).toBeVisible({ timeout: 5000 });
  });

  test('preview panel displays diagram viewport for valid source', async ({
    adminPage: page,
  }) => {
    const title = `E2E Preview Render ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(
      page.getByRole('row', { name: new RegExp(title) })
    ).toBeVisible({ timeout: 15000 });

    const row = page.getByRole('row', { name: new RegExp(title) });
    await row.getByRole('button', { name: /preview/i }).click();
    await expect(page.getByTestId('mdm-preview-panel')).toBeVisible();

    await expect(
      page.getByTestId('mdm-preview-panel').getByTestId('mdm-diagram-viewport')
    ).toBeVisible({ timeout: 10000 });
  });

  test('quick create fullscreen modal shows side-by-side on desktop', async ({
    adminPage: page,
  }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-library-shell')).toBeVisible({ timeout: 10000 });
    await page.getByRole('button', { name: /add diagram/i }).click();

    await expect(page.getByRole('heading', { name: /create diagram/i })).toBeVisible();

    await page.getByLabel(/title/i).fill('Fullscreen Layout Test');
    await page
      .getByRole('textbox', { name: /mermaid source/i })
      .fill('graph LR\n    A[Square Rect] -- Link text --> B((Circle))\n    A --> C(Round Rect)\n    B --> D{Rhombus}\n    C --> D');

    await expect(page.getByTestId('mdm-diagram-viewport')).toBeVisible({ timeout: 10000 });

    await expect(page).toHaveScreenshot('quick-create-fullscreen.png', {
      maxDiffPixelRatio: 0.02,
    });
  });

  test('populated library DataViews screenshot', async ({ adminPage: page }) => {
    const title = `E2E Screenshot ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(
      page.getByRole('row', { name: new RegExp(title) })
    ).toBeVisible({ timeout: 15000 });

    await expect(page).toHaveScreenshot('library-table-extended.png', {
      maxDiffPixelRatio: 0.02,
    });
  });
});
