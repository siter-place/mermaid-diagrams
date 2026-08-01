# Phase 03 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [ ] Create `packages/mermaid-runtime` with pinned initialization, parse, render, diagnostic normalization, unique IDs, cancellation/stale generation handling, SVG accessibility, and client sanitization.
- [ ] Implement source constraints and denied configuration/directive rules shared with PHP.
- [ ] Implement validation receipt generation and REST verification.
- [ ] Implement a Node Mermaid-JS validation worker interface using the same lockfile version; choose local process/service/deployment adapter according to ADR.
- [ ] Wire create/update routes to reject missing/mismatched/invalid receipts and require worker receipts for autonomous profiles.
- [ ] Implement source and SVG download helpers, filename rules, object URL cleanup, and public policy intersection.
- [ ] Create a versioned Mermaid corpus covering supported types, edge syntax, directives, large diagrams, malicious fixtures, and known regressions.

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
