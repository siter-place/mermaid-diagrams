# Phase 09 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [ ] Extend create/update clients so source validation is followed by SVG render, client sanitization, and one source+thumbnail mutation with matching hashes/versions.
- [ ] Implement coordinated REST create/update handling: capability, nonce/auth, schemas, provenance, server sanitization, media staging/reuse, featured assignment, and save acknowledgement only after both assets commit.
- [ ] Implement narrowly scoped SVG MIME support; do not enable general SVG uploads.
- [ ] Implement compensating rollback, retry, orphan cleanup, and previous-version preservation for every partial failure; keep a repair-only regeneration endpoint.
- [ ] Implement usage dirty marking from post lifecycle and block parsing.
- [ ] Implement bounded idempotent cron indexing batches, aggregated count update, daily reconciliation, locking, failure telemetry.
- [ ] Implement `wp mdm usage reindex`, status, and repair commands.
- [ ] Expose usage summary/deletion warning without expensive page scans.

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
