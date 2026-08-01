# Phase 04 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [ ] Create the React app entry and WordPress admin page bootstrap.
- [ ] Apply plugin-ui-inspired provider composition and adapter layer after Phase 00 compatibility result.
- [ ] Implement server bootstrap data, REST client, query cache strategy, notices, error boundary, loading/skeleton/empty states, and URL query synchronization.
- [ ] Implement launch table/list fields and pagination shell; do not implement grid.
- [ ] Implement settings sections for rendering/theme, downloads, AI/MCP feature gates, cleanup policy, limits, and usage cron policy as applicable.
- [ ] Scope all styles and use WordPress components/tokens; avoid global resets.
- [ ] Add stable test IDs only where semantic locators are insufficient.

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
