# Phase 13 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [ ] Complete threat-model review, nonce/capability/schema/escaping/SQL/SVG/AI/MCP audits.
- [ ] Run performance profiling: query counts, REST payloads, list scale, render concurrency, bundle sizes, cron batches, caching/invalidation.
- [ ] Complete WCAG-oriented keyboard/focus/labels/announcements/contrast/RTL/reduced-motion testing.
- [ ] Finalize PHPCS/WPCS, PHPStan, JS/TS/style/markdown lint, PHPUnit/integration, dependency/license/security scanning, Plugin Check.
- [ ] Create CI matrix for minimum and forward WordPress profiles; run Bruno reports and Playwright traces/visual comparisons.
- [ ] Create production build/ZIP excluding dev/test/secrets/source maps as policy dictates; verify clean install/upgrade/uninstall.
- [ ] Create WordPress Playground Blueprint/demo when compatible, without secrets.
- [ ] Finalize readme/changelog/upgrade notes, admin/user/developer REST/Ability docs, cron/WP-CLI ops, privacy and support runbooks.
- [ ] Perform consistency audit across all docs and remove stale names/decisions.

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
