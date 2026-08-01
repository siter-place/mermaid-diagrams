#!/usr/bin/env node
import { JSDOM } from 'jsdom';
import crypto from 'crypto';

if (typeof globalThis.window === 'undefined') {
  const dom = new JSDOM('<!DOCTYPE html><html><body></body></html>');
  globalThis.window = dom.window;
  globalThis.document = dom.window.document;
}

const { default: mermaid } = await import('mermaid');
const m = mermaid.default || mermaid;

m.initialize({
  securityLevel: 'strict',
  startOnLoad: false,
});

async function main() {
  let inputData = '';
  for await (const chunk of process.stdin) {
    inputData += chunk;
  }

  let payload;
  try {
    payload = JSON.parse(inputData || '{}');
  } catch (err) {
    console.error(JSON.stringify({ valid: false, error: 'Invalid JSON input' }));
    process.exit(1);
  }

  const source = payload.source || '';
  const profile = payload.profile || 'worker';

  if (!source || typeof source !== 'string') {
    console.error(JSON.stringify({ valid: false, error: 'Empty source string' }));
    process.exit(1);
  }

  // Deny null bytes, click/callback, securityLevel overrides, script tags
  if (
    source.includes('\0') ||
    /^\s*(click|callback)\s+[A-Za-z0-9_-]+/im.test(source) ||
    /securityLevel/i.test(source) ||
    /<script[\s\S]*?>/i.test(source)
  ) {
    console.log(JSON.stringify({
      valid: false,
      error: 'Denied source constraint directive',
      diagnostics: [{ message: 'Denied source constraint directive', code: 'MDM_CONSTRAINT_DENIED' }],
    }));
    process.exit(2);
  }

  try {
    const parseRes = await m.parse(source);
    let diagramType = 'unknown';
    if (typeof parseRes === 'object' && parseRes !== null && 'diagramType' in parseRes) {
      diagramType = String(parseRes.diagramType);
    } else {
      const match = source.replace(/%%.*$/gm, '').trim().split('\n')[0].match(/^([a-zA-Z0-9_-]+)/);
      diagramType = match ? match[1].toLowerCase() : 'unknown';
    }

    const normalized = source.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
    const hash = crypto.createHash('sha256').update(normalized, 'utf8').digest('hex');
    const sourceHash = `sha256:${hash}`;

    const receipt = {
      valid: true,
      diagramType,
      sourceHash,
      mermaidVersion: '11.4.1',
      validatedAt: new Date().toISOString(),
      profile,
      diagnostics: [],
    };

    console.log(JSON.stringify(receipt));
    process.exit(0);
  } catch (err) {
    const errorObj = err;
    const message = errorObj?.message || String(err);
    console.log(JSON.stringify({
      valid: false,
      error: message,
      diagnostics: [{ message, code: 'MDM_SYNTAX_ERROR' }],
    }));
    process.exit(3);
  }
}

main().catch((err) => {
  console.error(JSON.stringify({ valid: false, error: String(err) }));
  process.exit(1);
});
