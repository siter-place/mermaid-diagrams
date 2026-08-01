import { TextEncoder, TextDecoder } from 'util';

if (typeof globalThis.TextEncoder === 'undefined') {
  globalThis.TextEncoder = TextEncoder;
  globalThis.TextDecoder = TextDecoder as typeof globalThis.TextDecoder;
}

if (typeof globalThis.structuredClone === 'undefined') {
  globalThis.structuredClone = (obj: unknown) => {
    if (obj === undefined) return undefined;
    return JSON.parse(JSON.stringify(obj));
  };
}

if (typeof globalThis.window === 'undefined') {
  // eslint-disable-next-line @typescript-eslint/no-var-requires
  const { JSDOM } = require('jsdom');
  const dom = new JSDOM('<!DOCTYPE html><html><body></body></html>');
  globalThis.window = dom.window as unknown as Window & typeof globalThis;
  globalThis.document = dom.window.document;
}

if (globalThis.window && globalThis.window.SVGElement) {
  if (!globalThis.window.SVGElement.prototype.getBBox) {
    globalThis.window.SVGElement.prototype.getBBox = () =>
      ({
        x: 0,
        y: 0,
        width: 100,
        height: 50,
        top: 0,
        left: 0,
        right: 100,
        bottom: 50,
      } as DOMRect);
  }
}

import mermaid from 'mermaid';

const m = (mermaid as unknown as { default: typeof mermaid }).default || mermaid;

export const PINNED_MERMAID_VERSION = '11.4.1';

export function getLockedConfig(customConfig: Record<string, unknown> = {}): Record<string, unknown> {
  const sanitized = { ...customConfig };
  delete sanitized.securityLevel;
  delete sanitized.startOnLoad;
  delete sanitized.secure;

  return {
    ...sanitized,
    securityLevel: 'strict',
    startOnLoad: false,
  };
}

let isInitialized = false;

export function initializeMermaid(config: Record<string, unknown> = {}): void {
  const finalConfig = getLockedConfig(config);
  m.initialize(finalConfig);
  isInitialized = true;
}

export function isMermaidInitialized(): boolean {
  return isInitialized;
}
