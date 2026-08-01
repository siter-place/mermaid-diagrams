import mermaid from 'mermaid';

const m = (mermaid as unknown as { default: typeof mermaid }).default || mermaid;

describe('Spike 1: Node-side Mermaid Parse Validation Worker Concept', () => {
  it('validates syntax in Node environment using mermaid.parse', async () => {
    const valid = 'sequenceDiagram\nAlice->>John: Hello John';
    const isValid = await m.parse(valid).then(() => true).catch(() => false);
    expect(isValid).toBe(true);

    const invalid = 'sequenceDiagram\nAlice--John';
    const isInvalidValid = await m.parse(invalid).then(() => true).catch(() => false);
    expect(isInvalidValid).toBe(false);
  });
});
