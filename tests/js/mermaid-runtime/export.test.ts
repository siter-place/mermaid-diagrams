import { exportSourceDownload, exportSvgDownload, sanitizeFilename } from '../../../packages/mermaid-runtime/src';

describe('mermaid-runtime export', () => {
  it('sanitizes filename correctly', () => {
    expect(sanitizeFilename('My Special Flowchart!@#')).toBe('my-special-flowchart');
    expect(sanitizeFilename('')).toBe('diagram');
  });

  it('builds source download artifact', () => {
    const source = "flowchart LR\n  A --> B";
    const artifact = exportSourceDownload('Test Flow', source, 42);

    expect(artifact.filename).toBe('test-flow-42.mmd');
    expect(artifact.content).toBe(source);
    expect(artifact.mimeType).toBe('text/plain;charset=utf-8');
  });

  it('builds SVG download artifact', () => {
    const svg = '<svg><g></g></svg>';
    const artifact = exportSvgDownload('Test SVG', svg, 99);

    expect(artifact.filename).toBe('test-svg-99.svg');
    expect(artifact.content).toContain('<svg');
    expect(artifact.mimeType).toBe('image/svg+xml;charset=utf-8');
  });
});
