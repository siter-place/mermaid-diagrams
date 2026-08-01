# Phase 06 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [ ] Scaffold/register dynamic block from block.json API version 3.
- [ ] Define stable attributes/invariants for inline and reference modes; add deprecations only when required.
- [ ] Build empty chooser, inline source/preview, validation errors, library selector, reference preview, inspector controls, save-to-library and detach workflows.
- [ ] Use REST clients and idempotency; do not duplicate persistence in block code.
- [ ] Implement server render callback with capability/status checks and safe payload/context for frontend Interactivity API.
- [ ] Implement editor refresh/version handling for shared updates.
- [ ] Add post pre-publish validation warning using supported editor data APIs.
- [ ] Theme resolution is global then diagram default only.

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
