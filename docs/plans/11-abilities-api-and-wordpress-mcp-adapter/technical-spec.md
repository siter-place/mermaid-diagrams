# Phase 11 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [ ] Register an `mdm` ability category and narrow abilities defined in final architecture.
- [ ] Map each ability to existing application commands/queries; no duplicate repository logic.
- [ ] Define strict input/output JSON Schemas, stable errors, permission callbacks, read/write/destructive/idempotent annotations, and exposure metadata.
- [ ] Implement worker-available policy for create/update and candidate-only fallback.
- [ ] Integrate the official MCP Adapter using the Phase 00 selected loading mode; prevent duplicate adapter loading.
- [ ] Create allowlist/configuration and documentation for Cursor/Codex/Copilot clients.
- [ ] Add audit/verify scripts and a least-privileged agent test user.
- [ ] Log ability ID/result metadata without secrets/raw source by default.

## Cross-cutting requirements

- Use `WebFalcon\MermaidDiagrams` and `mdm` identifiers consistently.
- Keep WordPress/PHP minimums at 7.0/8.3.
- Validate and sanitize input early; escape output late; always check capabilities.
- Use stable error codes and schemas.
- Add observability without secrets/raw source by default.
- Keep migrations and background work idempotent.
- Preserve backward compatibility of already released block/API/data contracts or add explicit migration/deprecation.
- Update docs/tests in the same change.

## Completion artifacts

- Production code and built assets for this slice.
- Unit/integration/API/browser tests listed in `tests-and-acceptance.md`.
- Updated contracts/schemas and generated artifacts.
- ADR for any significant choice made during implementation.
- Phase completion report with commands and evidence.
