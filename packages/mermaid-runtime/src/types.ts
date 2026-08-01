export interface Diagnostic {
  message: string;
  line?: number;
  column?: number;
  code?: string;
}

export interface ParseResult {
  valid: boolean;
  diagramType: string;
  diagnostics: Diagnostic[];
}

export interface RenderResult {
  svg: string;
  bindFunctions?: (el: Element) => void;
  token?: string;
}

export interface ValidationReceiptPayload {
  sourceHash: string;
  mermaidVersion: string;
  diagramType: string;
  validatedAt: string;
  profile: 'browser' | 'worker';
}

export interface ConstraintCheckResult {
  valid: boolean;
  errors: string[];
}

export interface DownloadArtifact {
  filename: string;
  content: string;
  mimeType: string;
  blobUrl?: string;
  revoke?: () => void;
}
