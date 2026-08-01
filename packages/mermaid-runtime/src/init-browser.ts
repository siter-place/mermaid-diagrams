import mermaid from 'mermaid';

export function getMermaidInstance() {
  const anyWin = typeof window !== 'undefined' ? (window as any) : (globalThis as any);
  if (typeof anyWin.mermaid?.render === 'function') {
    return anyWin.mermaid;
  }

  const m = mermaid as any;
  if (typeof m?.render === 'function') return m;
  if (typeof m?.default?.render === 'function') return m.default;
  if (typeof m?.default?.default?.render === 'function') return m.default.default;

  for (const key of Object.keys(m || {})) {
    if (typeof m[key]?.render === 'function') {
      return m[key];
    }
  }

  return m?.default || m;
}

export const PINNED_MERMAID_VERSION = '11.4.1';

export function getLockedConfig(
	customConfig: Record< string, unknown > = {}
): Record< string, unknown > {
	const sanitized = { ...customConfig };
	delete sanitized.securityLevel;
	delete sanitized.startOnLoad;
	delete sanitized.secure;

	return {
		...sanitized,
		securityLevel: 'strict',
		startOnLoad: false,
	};
}

let isInitialized = false;

export function initializeMermaid(
	config: Record< string, unknown > = {}
): void {
	if ( isInitialized && Object.keys( config ).length === 0 ) {
		return;
	}
	const finalConfig = getLockedConfig( config );
	try {
		getMermaidInstance().initialize( finalConfig );
		isInitialized = true;
	} catch {
		// Guard re-initialization
	}
}

export function isMermaidInitialized(): boolean {
	return isInitialized;
}

export function resetInitialization(): void {
	isInitialized = false;
}
