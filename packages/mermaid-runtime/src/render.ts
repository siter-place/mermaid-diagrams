import mermaid from 'mermaid';
import { initializeMermaid, getLockedConfig } from './init';
import { parseMermaid } from './parse';
import { sanitizeSvg } from './sanitize-svg';
import { ensureSvgAccessibility } from './accessibility';
import { RenderResult } from './types';

const m = (mermaid as unknown as { default: typeof mermaid }).default || mermaid;

let activeToken = 0;

export async function renderMermaid(
  id: string,
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

  initializeMermaid(getLockedConfig(config));

  const renderId = id || `mdm-render-${Math.random().toString(36).substring(2, 9)}`;
  const { svg, bindFunctions } = await m.render(renderId, source);

  // Stale check
  if (token && token !== currentToken) {
    throw new Error('STALE_RENDER_CANCELLED');
  }

  const sanitized = sanitizeSvg(svg);
  const accessible = ensureSvgAccessibility(sanitized, options?.title, options?.description);

  return {
    svg: accessible,
    bindFunctions,
    token: currentToken,
  };
}
