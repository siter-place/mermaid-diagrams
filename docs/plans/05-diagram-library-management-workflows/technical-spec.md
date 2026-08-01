# Phase 05 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [ ] Implement debounced/abortable search, category/tag/status/type/author filters, deterministic sort/pagination, and URL persistence.
- [ ] Implement selection and bulk category Add/Remove/Replace, tag Add/Remove, status, duplicate, trash/restore with partial failure reporting.
- [ ] Implement accessible preview side panel/dialog with metadata, rendered diagram, edit/open actions, usage summary, and missing thumbnail state.
- [ ] Implement quick-create flow that validates source before REST create.
- [ ] Implement taxonomy management/autocomplete with capability gates.
- [ ] Add optimistic UI only where rollback is reliable; server response remains authoritative.
- [ ] Instrument query counts and avoid source/SVG in list rows.

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
