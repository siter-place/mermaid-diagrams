export function sanitizeSvg(svgString: string): string {
  if (!svgString) return '';

  let sanitized = svgString;

  // Remove <script> tags and content
  sanitized = sanitized.replace(/<script[\s\S]*?<\/script>/gi, '');

  // Remove <foreignObject> tags and content
  sanitized = sanitized.replace(/<foreignObject[\s\S]*?<\/foreignObject>/gi, '');

  // Remove event handler attributes like onload, onclick, onerror
  sanitized = sanitized.replace(/\s+on[a-z]+\s*=\s*(?:"[^"]*"|'[^']*'|[^\s>]+)/gi, '');

  // Neutralize javascript: URLs in href/xlink:href
  sanitized = sanitized.replace(/(href|xlink:href)\s*=\s*["']?\s*javascript:[^"'>\s]*/gi, '$1="#"');

  return sanitized;
}
