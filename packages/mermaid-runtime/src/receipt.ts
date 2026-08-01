import { PINNED_MERMAID_VERSION } from './init';
import { parseMermaid } from './parse';
import { ValidationReceiptPayload } from './types';

export async function computeSha256(str: string): Promise<string> {
  const normalized = str.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
  if (typeof crypto !== 'undefined' && crypto.subtle) {
    const encoder = new TextEncoder();
    const data = encoder.encode(normalized);
    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hex = hashArray.map((b) => b.toString(16).padStart(2, '0')).join('');
    return `sha256:${hex}`;
  }

  throw new Error('Web Crypto API is unavailable.');
}

export async function createValidationReceipt(
  source: string,
  profile: 'browser' | 'worker' = 'browser'
): Promise<ValidationReceiptPayload> {
  const parseRes = await parseMermaid(source);
  if (!parseRes.valid) {
    throw new Error(`Cannot create validation receipt for invalid source: ${parseRes.diagnostics.map((d) => d.message).join('; ')}`);
  }

  const sourceHash = await computeSha256(source);
  return {
    sourceHash,
    mermaidVersion: PINNED_MERMAID_VERSION,
    diagramType: parseRes.diagramType,
    validatedAt: new Date().toISOString(),
    profile,
  };
}
