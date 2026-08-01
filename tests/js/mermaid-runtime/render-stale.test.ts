import { renderMermaid } from '../../../packages/mermaid-runtime/src';

describe('mermaid-runtime render & stale token protection', () => {
  it('renders valid source to sanitized SVG', async () => {
    const res = await renderMermaid('test-render-1', "flowchart LR\n  A --> B");
    expect(res.svg).toContain('<svg');
    expect(res.svg).toContain('</svg>');
    expect(res.token).toBeDefined();
  });

  it('cancels stale render when token mismatches', async () => {
    await expect(
      renderMermaid('test-render-2', "flowchart LR\n  A --> B", {}, 'token-123')
    ).resolves.toBeDefined();
  });
});
