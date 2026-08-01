# ADR-009: Enforce Always-Valid Mermaid Source

**Status:** Accepted

## Decision

No diagram create/update reaches persistence unless the exact source has passed the pinned Mermaid JS parser. First-party UIs validate in browser. Non-browser writers require the shared validation-worker profile or are limited to candidate generation.

A validation receipt binds source hash, Mermaid version, diagram type, and validation time. PHP verifies the receipt binding and all server-enforceable constraints. AI judgement is never considered syntax validation.

## Consequences

- Invalid drafts are kept only in unsaved local recovery storage.
- Changing Mermaid versions requires corpus validation and migration planning.
- MCP direct writes are conditional on a production-capable Mermaid-JS validator.
- REST tests cover forged/stale/mismatched receipts.
