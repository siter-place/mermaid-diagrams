# Phase 01 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [x] Create one plugin bootstrap and service-provider/loader architecture under `WebFalcon\MermaidDiagrams`.
- [x] Implement domain value objects and aggregate rules for source, source hash, title, description, status, detected type, validation receipt, version token, category IDs, tag IDs, and presentation defaults.
- [x] Register `mdm_diagram` CPT, `mdm_diagram_category`, and `mdm_diagram_tag` with explicit capability mapping and `show_in_rest` where appropriate.
- [x] Register protected post meta with schemas/auth callbacks; canonical source storage choice must match the data-model specification.
- [x] Create repository interfaces and WordPress implementations, but keep HTTP/UI out.
- [x] Create usage/dirty custom table migrations or equivalent database schema defined by ADR-accepted design.
- [x] Register activation roles/capabilities idempotently and supply WP-CLI inspection/repair commands where useful.

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
