import { initializeMermaid, getLockedConfig, getMermaidInstance, resetInitialization } from './init-browser';
import { parseMermaid } from './parse';
import { sanitizeSvg } from './sanitize-svg';
import { ensureSvgAccessibility } from './accessibility';
import { RenderResult } from './types';

let activeToken = 0;

async function attemptRender(
  id: string | undefined,
  source: string,
  config: Record<string, unknown>
): Promise<{ svg: string; bindFunctions?: (element: Element) => void }> {
  initializeMermaid(config);

  const instance = getMermaidInstance();
  if (typeof instance?.render !== 'function') {
    throw new Error('Mermaid renderer is not available.');
  }

  const renderId = id || `mdm-render-${Math.random().toString(36).substring(2, 9)}`;
  return instance.render(renderId, source);
}

export async function renderMermaid(
  id: string | undefined,
  source: string,
  config: Record<string, unknown> = {},
  token?: string,
  options?: { title?: string; description?: string }
): Promise<RenderResult> {
  const parseRes = await parseMermaid(source);
  if (!parseRes.valid) {
    throw new Error(`Cannot render invalid Mermaid source: ${parseRes.diagnostics.map((d) => d.message).join('; ')}`);
  }

  const currentToken = token || String(++activeToken);

  let result: { svg: string; bindFunctions?: (element: Element) => void };
  try {
    result = await attemptRender(id, source, config);
  } catch (err) {
    const msg = err instanceof Error ? err.message : String(err);
    if (msg.includes('render is not a function') || msg.includes('is not available')) {
      resetInitialization();
      result = await attemptRender(id, source, config);
    } else {
      throw err;
    }
  }

  if (token && token !== currentToken) {
    throw new Error('STALE_RENDER_CANCELLED');
  }

  const sanitized = sanitizeSvg(result.svg);
  const accessible = ensureSvgAccessibility(sanitized, options?.title, options?.description);

  return {
    svg: accessible,
    bindFunctions: result.bindFunctions,
    token: currentToken,
  };
}
