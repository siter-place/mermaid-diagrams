import mermaid from 'mermaid';

const m = (mermaid as unknown as { default: typeof mermaid }).default || mermaid;

describe('Spike 1: Mermaid JS Parse & Render', () => {
  beforeAll(() => {
    // Mock SVGElement.prototype.getBBox for jsdom rendering support
    if (typeof window !== 'undefined' && typeof SVGElement !== 'undefined') {
      SVGElement.prototype.getBBox = () => ({
        x: 0,
        y: 0,
        width: 100,
        height: 50,
        top: 0,
        right: 100,
        bottom: 50,
        left: 0,
        toJSON: () => {},
      });
    }

    m.initialize({ startOnLoad: false, securityLevel: 'strict' });
  });

  it('parses valid flowchart syntax without throwing', async () => {
    const validDiagram = 'flowchart LR\nA-->B';
    const result = await m.parse(validDiagram);
    expect(result).toBeTruthy();
  });

  it('rejects invalid diagram syntax with error', async () => {
    const invalidDiagram = 'invalid %%% garbage syntax';
    await expect(m.parse(invalidDiagram)).rejects.toThrow();
  });

  it('renders SVG output string for valid diagram', async () => {
    if (typeof document !== 'undefined') {
      const { svg } = await m.render('test-spike-id', 'flowchart LR\nA-->B');
      expect(svg).toContain('<svg');
      expect(svg).toContain('A');
      expect(svg).toContain('B');
    }
  });
});
