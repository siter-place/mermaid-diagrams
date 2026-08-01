/**
 * Playwright helper to seed a valid diagram via REST.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import crypto from 'crypto';
import type { APIRequestContext, Page } from '@playwright/test';

const SOURCE = 'flowchart LR\n  A --> B';

function buildValidationReceipt(source: string) {
  const normalized = source.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
  const hash = crypto.createHash('sha256').update(normalized, 'utf8').digest('hex');

  return {
    sourceHash: `sha256:${hash}`,
    mermaidVersion: '11.4.1',
    diagramType: 'flowchart',
    validatedAt: new Date().toISOString(),
    profile: 'worker' as const,
  };
}

function getRestAuth() {
  const username = process.env.BRUNO_USERNAME ?? process.env.WP_ADMIN_USER ?? 'admin';
  const password =
    process.env.BRUNO_APPLICATION_PASSWORD ??
    process.env.WP_ADMIN_PASSWORD ??
    'password';

  return { username, password };
}

async function postDiagram(
  request: APIRequestContext,
  baseURL: string,
  title: string,
  options: {
    headers?: Record<string, string>;
    useBasicAuth?: boolean;
  } = {}
): Promise<number> {
  const response = await request.post(`${baseURL}/wp-json/mdm/v1/diagrams`, {
    data: {
      title,
      source: SOURCE,
      status: 'draft',
      validation: buildValidationReceipt(SOURCE),
    },
    headers: {
      'Content-Type': 'application/json',
      'Idempotency-Key': `playwright-${Date.now()}-${Math.random().toString(36).slice(2)}`,
      ...options.headers,
    },
    auth: options.useBasicAuth ? getRestAuth() : undefined,
  });

  if (!response.ok()) {
    throw new Error(`Failed to seed diagram: ${response.status()} ${await response.text()}`);
  }

  const body = await response.json();
  return body.id as number;
}

export async function seedDiagram(
  request: APIRequestContext,
  baseURL: string,
  title: string
): Promise<number> {
  return postDiagram(request, baseURL, title, { useBasicAuth: true });
}

export async function seedDiagramWithPage(
  page: Page,
  baseURL: string,
  title: string
): Promise<number> {
  await page.goto(`${baseURL}/wp-admin/admin.php?page=mdm-diagrams`);
  await page.waitForFunction(() => Boolean((window as typeof window & { mdmAdminBootstrap?: { nonce?: string } }).mdmAdminBootstrap?.nonce));

  const nonce = await page.evaluate(() => {
    const bootstrap = (window as typeof window & { mdmAdminBootstrap?: { nonce?: string } }).mdmAdminBootstrap;
    return bootstrap?.nonce ?? '';
  });

  return postDiagram(page.request, baseURL, title, {
    headers: {
      'X-WP-Nonce': nonce,
    },
  });
}
