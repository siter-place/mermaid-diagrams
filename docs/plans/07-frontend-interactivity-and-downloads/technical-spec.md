# Phase 07 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [ ] Create namespaced Interactivity API store with per-block local context and actions.
- [ ] Integrate SVG viewport/pan/zoom behavior with keyboard and pointer input.
- [ ] Implement fit/reset and state announcements.
- [ ] Implement Fullscreen API with focus-preserving full-viewport dialog fallback.
- [ ] Implement `.mmd` and SVG download controls intersecting global/diagram/block permissions; document source-affordance limitation.
- [ ] Conditionally enqueue Mermaid/runtime/view module only on pages containing the block.
- [ ] Handle reduced motion, RTL, high contrast, small screens, print/no-JS fallback.

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
