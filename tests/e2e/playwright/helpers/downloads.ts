import { expect, type Download, type Page } from '@playwright/test';
import { stat } from 'node:fs/promises';

export async function expectNonEmptyDownload(
  page: Page,
  action: () => Promise<void>,
  extension: RegExp
): Promise<Download> {
  const downloadPromise = page.waitForEvent('download');
  await action();
  const download = await downloadPromise;
  expect(download.suggestedFilename()).toMatch(extension);
  const path = await download.path();
  expect(path).not.toBeNull();
  const info = await stat(path!);
  expect(info.size).toBeGreaterThan(0);
  return download;
}
