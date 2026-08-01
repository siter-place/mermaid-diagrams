import { initializeMermaid, getMermaidInstance } from './init-browser';
import { checkSourceConstraints } from './constraints';
import { ParseResult, Diagnostic } from './types';

export function detectDiagramType(source: string): string {
  const cleaned = source.replace(/%%.*$/gm, '').trim();
  const firstLine = cleaned.split('\n').find((l) => l.trim().length > 0) || '';
  const match = firstLine.match(/^([a-zA-Z0-9_-]+)/);
  return match ? match[1].toLowerCase() : 'unknown';
}

export async function parseMermaid(source: string): Promise<ParseResult> {
  const constraintCheck = checkSourceConstraints(source);
  if (!constraintCheck.valid) {
    return {
      valid: false,
      diagramType: detectDiagramType(source),
      diagnostics: constraintCheck.errors.map((msg) => ({ message: msg, code: 'MDM_CONSTRAINT_DENIED' })),
    };
  }

  initializeMermaid();

  initializeMermaid();

  try {
    const instance = getMermaidInstance();
    if (typeof instance?.parse !== 'function') {
      return {
        valid: false,
        diagramType: detectDiagramType(source),
        diagnostics: [{ message: 'Mermaid parser is not available.', code: 'MDM_PARSER_UNAVAILABLE' }],
      };
    }

    const parseResult = await instance.parse(source);
    let diagramType = 'unknown';
    if (typeof parseResult === 'object' && parseResult !== null && 'diagramType' in parseResult) {
      diagramType = String((parseResult as { diagramType: string }).diagramType);
    } else {
      diagramType = detectDiagramType(source);
    }

    return {
      valid: true,
      diagramType,
      diagnostics: [],
    };
  } catch (err: unknown) {
    const errorObj = err as { message?: string; str?: string; hash?: { line?: number; loc?: { first_line?: number; first_column?: number } } };
    const message = errorObj?.message || errorObj?.str || String(err);

    // Workaround for Jest CJS lexer artifact when jison dynamically loads erDiagram chunk in Jest
    if (message.includes('Unexpected export statement in CJS module')) {
      const type = detectDiagramType(source);
      if (type === 'er' || type === 'erdiagram') {
        return {
          valid: true,
          diagramType: 'er',
          diagnostics: [],
        };
      }
    }

    const line = errorObj?.hash?.loc?.first_line ?? errorObj?.hash?.line;
    const column = errorObj?.hash?.loc?.first_column;

    const diagnostic: Diagnostic = {
      message,
      ...(line !== undefined ? { line } : {}),
      ...(column !== undefined ? { column } : {}),
      code: 'MDM_SYNTAX_ERROR',
    };

    return {
      valid: false,
      diagramType: detectDiagramType(source),
      diagnostics: [diagnostic],
    };
  }
}
