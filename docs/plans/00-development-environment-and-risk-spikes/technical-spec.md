# Phase 00 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [ ] Pin Node/npm and dependency lockfiles; replace placeholder package/composer scripts with executable quality commands.
- [ ] Finalize `.wp-env.json`, `.wp-env.test.json`, local override guidance, and idempotent setup scripts.
- [ ] Create a minimal plugin bootstrap that activates without warnings only as needed to map the project in wp-env; feature work belongs to Phase 01.
- [ ] Install/configure Playwright and a smoke setup project; produce deterministic screenshot proof.
- [ ] Create Bruno collection metadata, Local environment, auth convention, health smoke request, reporters, and ignored report/secret paths.
- [ ] Perform and document five spikes: Live Editor static admin bundle, plugin-ui/WordPress React compatibility, Mermaid parse/render in browser and Node worker, controlled sanitized SVG media upload, MCP Adapter standalone-vs-Composer integration.
- [ ] Fill `docs/reference/tools/sources-lock.md` and write ADRs for selected implementation modes.

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
