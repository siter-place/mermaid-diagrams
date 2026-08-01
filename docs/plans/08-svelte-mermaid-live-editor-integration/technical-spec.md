# Phase 08 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [ ] Pin/import the selected Live Editor source according to ADR; maintain patch inventory and upstream update notes.
- [ ] Compile static Svelte assets and mount them in a capability-gated admin page without a runtime SvelteKit server.
- [ ] Create WordPress adapter/bootstrap for REST root, nonce, diagram ID, capabilities, locale, settings, and navigation.
- [ ] Replace/wrap upstream persistence/share mechanisms with plugin REST commands.
- [ ] Add title/description/categories/tags/status controls and WordPress notices.
- [ ] Implement clean/dirty/valid/invalid/saving/saved/error/conflict state machine.
- [ ] Implement local unsaved recovery for invalid source; invalid work never reaches WordPress persistence.
- [ ] Implement revisions list/view/restore and optimistic conflict compare/reload/save-as-copy.
- [ ] Add source/SVG download. Define the Phase 09 adapter seam for coordinated featured-SVG save without implementing media persistence early.

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
