# Phase 02 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [ ] Implement controller classes that delegate to application commands/queries.
- [ ] Define JSON Schemas for diagram summary/detail/mutation, settings, taxonomy operations, bulk results, validation receipt, error envelope, and version token.
- [ ] Implement permission callbacks per route and field-level source exposure rules.
- [ ] Implement idempotency for create/save-to-library operations.
- [ ] Implement ETag or explicit version-token conditional updates with 409 responses.
- [ ] Create framework-neutral TypeScript contracts/API client package generated or manually synchronized from authoritative schemas.
- [ ] Implement plugin-ui-inspired settings provider contract: bootstrap schema/values, PATCH one section, server normalize, return complete section.
- [ ] Document endpoint catalog and machine-readable schema/OpenAPI export strategy.

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
