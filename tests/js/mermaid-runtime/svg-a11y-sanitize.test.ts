import { sanitizeSvg, ensureSvgAccessibility } from '../../../packages/mermaid-runtime/src';

describe('mermaid-runtime svg-a11y-sanitize', () => {
  it('strips script tags and onclick handlers', () => {
    const raw = '<svg><script>alert(1)</script><rect onclick="evil()"/></svg>';
    const sanitized = sanitizeSvg(raw);

    expect(sanitized).not.toContain('<script');
    expect(sanitized).not.toContain('onclick');
    expect(sanitized).toContain('<rect');
  });

  it('injects title, desc, and aria-labelledby attribute', () => {
    const raw = '<svg><g></g></svg>';
    const accessible = ensureSvgAccessibility(raw, 'Test Title', 'Test Description');

    expect(accessible).toContain('<title id="mdm-svg-title">Test Title</title>');
    expect(accessible).toContain('<desc id="mdm-svg-desc">Test Description</desc>');
    expect(accessible).toContain('role="img"');
    expect(accessible).toContain('aria-labelledby="mdm-svg-title mdm-svg-desc"');
  });
});
