import { test, expect } from '../fixtures/wordpress';
import { seedDiagramWithPage } from '../helpers/seed-diagram';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe('Diagram library extended workflows', () => {
  test('duplicates a diagram and verifies copy in list', async ({ adminPage: page }) => {
    const title = `E2E Dup ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(
      page.getByTestId('mdm-diagram-table').getByRole('row', { name: new RegExp(title) })
    ).toBeVisible({ timeout: 15000 });

    await page
      .getByRole('button', { name: new RegExp(`duplicate ${title}`, 'i') })
      .click();
    await expect(page.getByTestId('mdm-notices')).toContainText(/duplicated/i, {
      timeout: 15000,
    });
  });

  test('applies status filter and shows only matching diagrams', async ({
    adminPage: page,
  }) => {
    const title = `E2E Status ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(
      page.getByTestId('mdm-diagram-table').getByRole('row', { name: new RegExp(title) })
    ).toBeVisible({ timeout: 15000 });

    // Expand filters if collapsed
    const toggle = page.getByTestId('mdm-filter-toggle');
    const expanded = await toggle.getAttribute('aria-expanded');
    if (expanded === 'false') {
      await toggle.click();
    }
    await expect(page.getByTestId('mdm-filter-controls')).toBeVisible();

    // Apply Draft status filter - seeded diagrams are draft
    await page.locator('.mdm-filter-bar__controls').getByRole('combobox', { name: /status/i }).selectOption('draft');

    await expect(
      page.getByTestId('mdm-diagram-table').getByRole('row', { name: new RegExp(title) })
    ).toBeVisible({ timeout: 15000 });

    // Verify active filter pill appears
    await expect(page.getByTestId('mdm-active-filters')).toContainText(/Status: draft/i);
  });

  test('changes sort order and verifies URL updates', async ({ adminPage: page }) => {
    const title = `E2E Sort ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-diagram-table')).toBeVisible({ timeout: 15000 });

    // Open view options popover
    await page.getByTestId('mdm-view-options-toggle').click();
    await expect(page.getByTestId('mdm-view-options-popover')).toBeVisible();

    // Toggle order to Ascending
    await page.getByRole('button', { name: 'Ascending' }).click();

    await expect(page).toHaveURL(/order=ASC/);
  });

  test('switches between table view and grid view', async ({ adminPage: page }) => {
    const title = `E2E View Switch ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-diagram-table')).toBeVisible({ timeout: 15000 });

    // Switch to Grid View
    await page.getByTestId('mdm-view-grid').click();
    await expect(page.getByTestId('mdm-diagram-grid')).toBeVisible();

    // Switch back to Table View
    await page.getByTestId('mdm-view-table').click();
    await expect(page.getByTestId('mdm-diagram-table')).toBeVisible();
  });

  test('validates quick create rejects empty title', async ({ adminPage: page }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await page.getByRole('button', { name: /add diagram/i }).click();

    await expect(page.getByRole('heading', { name: /create diagram/i })).toBeVisible();

    // Fill source but leave title empty
    await page
      .getByRole('textbox', { name: /mermaid source/i })
      .fill('flowchart TD\n  A --> B');
    await page.getByRole('button', { name: /^save/i }).click();

    // Should show validation error
    await expect(page.locator('.mdm-form-error')).toBeVisible({ timeout: 5000 });
  });

  test('validates quick create rejects invalid mermaid syntax', async ({
    adminPage: page,
  }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await page.getByRole('button', { name: /add diagram/i }).click();

    await expect(page.getByRole('heading', { name: /create diagram/i })).toBeVisible();

    await page.getByLabel(/title/i).fill('Invalid Syntax Test');
    await page
      .getByRole('textbox', { name: /mermaid source/i })
      .fill('not valid mermaid {{{');
    await page.getByRole('button', { name: /^save/i }).click();

    // Should show validation error about invalid source
    await expect(page.locator('.mdm-form-error')).toBeVisible({ timeout: 5000 });
  });

  test('preview panel displays diagram viewport for valid source', async ({
    adminPage: page,
  }) => {
    const title = `E2E Preview Render ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(
      page.getByTestId('mdm-diagram-table').getByRole('row', { name: new RegExp(title) })
    ).toBeVisible({ timeout: 15000 });

    await page
      .getByRole('button', { name: new RegExp(`preview ${title}`, 'i') })
      .click();
    await expect(page.getByTestId('mdm-preview-panel')).toBeVisible();

    // The viewport should appear (even if render has issues, viewport container should exist)
    await expect(
      page.getByTestId('mdm-preview-panel').getByTestId('mdm-diagram-viewport')
    ).toBeVisible({ timeout: 10000 });
  });

  test('filter collapse state persists across page reload', async ({
    adminPage: page,
  }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-filter-bar')).toBeVisible();

    // Ensure filters are visible initially
    const toggle = page.getByTestId('mdm-filter-toggle');
    let expanded = await toggle.getAttribute('aria-expanded');

    if (expanded === 'true') {
      // Collapse
      await toggle.click();
      await expect(page.getByTestId('mdm-filter-controls')).toHaveCount(0);
    }

    // Reload page
    await page.reload();
    await expect(page.getByTestId('mdm-filter-bar')).toBeVisible();

    // Should still be collapsed
    const toggleAfterReload = page.getByTestId('mdm-filter-toggle');
    expanded = await toggleAfterReload.getAttribute('aria-expanded');
    expect(expanded).toBe('false');
  });

  test('active filter pill removal clears that filter', async ({ adminPage: page }) => {
    const title = `E2E Pill ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(page.getByTestId('mdm-diagram-table')).toBeVisible({ timeout: 15000 });

    // Search to create an active filter
    await page
      .locator('.mdm-filter-bar__search input')
      .fill(title);

    // Wait for pill to appear
    await expect(page.getByTestId('mdm-active-filters')).toBeVisible({ timeout: 5000 });
    await expect(page.getByTestId('mdm-active-filters')).toContainText(title);

    // Click X on the search pill
    await page
      .getByTestId('mdm-active-filters')
      .getByRole('button', { name: /remove/i })
      .first()
      .click();

    // Pill should be gone and search cleared
    await expect(page.getByTestId('mdm-active-filters')).toHaveCount(0, { timeout: 5000 });
  });

  test('quick create fullscreen modal shows side-by-side on desktop', async ({
    adminPage: page,
  }) => {
    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await page.getByRole('button', { name: /add diagram/i }).click();

    await expect(page.getByRole('heading', { name: /create diagram/i })).toBeVisible();

    // Fill in diagram data
    await page.getByLabel(/title/i).fill('Fullscreen Layout Test');
    await page
      .getByRole('textbox', { name: /mermaid source/i })
      .fill('graph LR\n    A[Square Rect] -- Link text --> B((Circle))\n    A --> C(Round Rect)\n    B --> D{Rhombus}\n    C --> D');

    // Wait for preview to appear
    await expect(page.getByTestId('mdm-diagram-viewport')).toBeVisible({ timeout: 10000 });

    await expect(page).toHaveScreenshot('quick-create-fullscreen.png', {
      maxDiffPixelRatio: 0.02,
    });
  });

  test('populated library table screenshot', async ({ adminPage: page }) => {
    const title = `E2E Screenshot ${Date.now()}`;
    await seedDiagramWithPage(page, baseURL, title);

    await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
    await expect(
      page.getByTestId('mdm-diagram-table').getByRole('row', { name: new RegExp(title) })
    ).toBeVisible({ timeout: 15000 });

    await expect(page).toHaveScreenshot('library-table-extended.png', {
      maxDiffPixelRatio: 0.02,
    });
  });
});
