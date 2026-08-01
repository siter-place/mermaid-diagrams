import { parseMermaid } from '../../../packages/mermaid-runtime/src';

describe('mermaid-runtime parse', () => {
  it('parses valid flowchart and detects type', async () => {
    const res = await parseMermaid("flowchart LR\n  A --> B");
    expect(res.valid).toBe(true);
    expect(res.diagramType).toBe('flowchart-v2');
    expect(res.diagnostics).toHaveLength(0);
  });

  it('detects syntax errors and returns diagnostics', async () => {
    const res = await parseMermaid("invalid %%% garbage syntax");
    expect(res.valid).toBe(false);
    expect(res.diagnostics.length).toBeGreaterThan(0);
    expect(res.diagnostics[0].code).toBe('MDM_SYNTAX_ERROR');
  });

  it('rejects click directives as constraint violation', async () => {
    const res = await parseMermaid("flowchart LR\n  A --> B\n  click A call alert()");
    expect(res.valid).toBe(false);
    expect(res.diagnostics[0].code).toBe('MDM_CONSTRAINT_DENIED');
  });
});
