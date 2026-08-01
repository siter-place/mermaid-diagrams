// Contract scaffold: activate in Phase 02/11 when its acceptance prerequisites are implemented.
import { test, expect } from '@playwright/test';

const baseURL = process.env.WP_BASE_URL ?? 'http://localhost:8888';

test.describe.skip('Permissions', () => {
  test.skip('author cannot update another user shared diagram through REST', async ({ request }) => {
    // Authenticate a test author through the environment fixture, then replace the ID.
    const response = await request.patch(`${baseURL}/wp-json/mdm/v1/diagrams/123`, {
      data: { title: 'Unauthorized change', expectedVersion: 'test' },
    });
    expect(response.status()).toBe(403);
  });
});
