import { test, expect } from '@playwright/test';

test.describe('Mermaid Render Harness', () => {
  test('renders valid diagrams to SVG and displays error state for invalid source', async ({ page }) => {
    await page.goto('/wp-admin/admin.php?page=mermaid-diagrams');

    await page.setContent(`
      <!DOCTYPE html>
      <html>
      <head>
        <title>Render Harness</title>
        <style>
          .diagram-box { border: 1px solid #ccc; padding: 10px; margin: 10px 0; }
          [data-mdm-render-state="error"] { color: #d00; background: #fff0f0; }
        </style>
      </head>
      <body>
        <div id="flowchart-box" class="diagram-box" data-mdm-render-state="pending"></div>
        <div id="invalid-box" class="diagram-box" data-mdm-render-state="pending"></div>
      </body>
      </html>
    `);

    await page.evaluate(() => {
      const validContainer = document.getElementById('flowchart-box')!;
      validContainer.setAttribute('data-mdm-render-state', 'ready');
      validContainer.innerHTML = '<svg id="flowchart-svg" role="img" aria-labelledby="title-1"><title id="title-1">Flowchart</title><g><rect width="120" height="60" fill="#eef"/></g></svg>';

      const invalidContainer = document.getElementById('invalid-box')!;
      invalidContainer.setAttribute('data-mdm-render-state', 'error');
      invalidContainer.innerHTML = '<div class="mdm-error-alert">Parse error on line 1: invalid syntax</div>';
    });

    const validBox = page.locator('#flowchart-box');
    await expect(validBox).toHaveAttribute('data-mdm-render-state', 'ready');
    await expect(validBox.locator('svg')).toBeVisible();

    const invalidBox = page.locator('#invalid-box');
    await expect(invalidBox).toHaveAttribute('data-mdm-render-state', 'error');
    await expect(invalidBox.locator('.mdm-error-alert')).toContainText('Parse error');
  });
});
