import { ConstraintCheckResult } from './types';

export function checkSourceConstraints(source: string): ConstraintCheckResult {
  const errors: string[] = [];

  if (!source || typeof source !== 'string') {
    return { valid: false, errors: ['Source code is empty or not a string.'] };
  }

  // Reject null bytes
  if (source.includes('\0')) {
    errors.push('Source code contains forbidden null bytes.');
  }

  // Reject click or callback directives
  const clickCallbackRegex = /^\s*(click|callback)\s+[A-Za-z0-9_-]+/im;
  if (clickCallbackRegex.test(source)) {
    errors.push('Author-defined click and callback directives are forbidden.');
  }

  // Reject securityLevel mutation in init directives or frontmatter
  if (/securityLevel/i.test(source)) {
    errors.push('Custom securityLevel overrides in Mermaid source are forbidden.');
  }

  // Reject inline <script> tags
  if (/<script[\s\S]*?>/i.test(source)) {
    errors.push('Inline script tags in Mermaid source are forbidden.');
  }

  return {
    valid: errors.length === 0,
    errors,
  };
}
