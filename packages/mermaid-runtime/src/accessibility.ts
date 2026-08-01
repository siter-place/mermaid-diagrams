function escapeXml(unsafe: string): string {
  return unsafe
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;');
}

export function ensureSvgAccessibility(
  svgString: string,
  title?: string,
  description?: string
): string {
  if (!svgString) return '';

  let result = svgString;

  if (title || description) {
    const titleTag = title ? `<title id="mdm-svg-title">${escapeXml(title)}</title>` : '';
    const descTag = description ? `<desc id="mdm-svg-desc">${escapeXml(description)}</desc>` : '';
    const accessibilityMarkup = `${titleTag}${descTag}`;

    // Insert after opening <svg ...> tag
    result = result.replace(/(<svg[^>]*>)/i, `$1${accessibilityMarkup}`);

    // Ensure role="img"
    if (!/role=["']img["']/i.test(result)) {
      result = result.replace(/<svg(\s|>)/i, '<svg role="img"$1');
    }

    const ariaIds = [title ? 'mdm-svg-title' : null, description ? 'mdm-svg-desc' : null].filter(Boolean).join(' ');
    if (ariaIds && !/aria-labelledby=/i.test(result)) {
      result = result.replace(/<svg(\s|>)/i, `<svg aria-labelledby="${ariaIds}"$1`);
    }
  }

  return result;
}
