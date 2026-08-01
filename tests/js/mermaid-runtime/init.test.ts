import { initializeMermaid, getLockedConfig, isMermaidInitialized } from '../../../packages/mermaid-runtime/src';

describe('mermaid-runtime init', () => {
  it('returns locked config with strict security level', () => {
    const custom = { theme: 'forest', securityLevel: 'loose', startOnLoad: true };
    const locked = getLockedConfig(custom);

    expect(locked.securityLevel).toBe('strict');
    expect(locked.startOnLoad).toBe(false);
    expect(locked.theme).toBe('forest');
  });

  it('initializes mermaid without throwing', () => {
    initializeMermaid({ theme: 'dark' });
    expect(isMermaidInitialized()).toBe(true);
  });
});
