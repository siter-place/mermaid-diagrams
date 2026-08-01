# Phase 12 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [ ] Define visual adapter interface: detect, parse, compatibility report, toGraph, fromGraph, roundTripCheck.
- [ ] Implement a deliberately documented flowchart subset inspired by Mermaid React Flow Editor; do not depend blindly on Mermaid private internals.
- [ ] Create stable intermediate representation for nodes, edges, subgraphs, labels, basic styles, comments/provenance as supported.
- [ ] Integrate React Flow (or selected graph UI) as isolated visual mode in/alongside the Live Editor using a framework boundary such as custom element/mount adapter.
- [ ] Implement node/edge add/edit/delete, pan/zoom/select, layout, keyboard, undo/redo, and Apply transaction.
- [ ] Re-parse/validate serialized source; compare semantic/loss report; block Apply on unsafe loss.
- [ ] Create round-trip corpus including unsupported syntax and version upgrade gates.

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
