# Phase 07 — Published Frontend Interactivity, Fullscreen, and Downloads

**Delivery label:** Public experience  
**Depends on:** Phase 06 dynamic block and Phase 03 runtime.  
**Previous phase:** Phase 06 — Gutenberg Block, Inline Diagrams, and Library References  
**Next phase:** Phase 08 — Adapted Mermaid Live Editor, Save, Revisions, and Conflicts

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- Visitors can view rendered diagrams and use zoom in/out, pan, fit, reset, native fullscreen or accessible dialog fallback, and allowed source/SVG downloads.
- Multiple diagrams on a page operate independently and only load assets when needed.
- Private/missing diagrams never leak content publicly.

## Required inputs

- `../../00-product-charter-and-decisions.md`
- `../../01-functional-specification.md`
- `../../02-enterprise-architecture.md`
- `../../03-data-model-rest-api.md`
- `../../07-security-performance-accessibility.md`
- `../../09-testing-strategy.md`
- Relevant ADRs under `../../architecture/adr/`
- Tool notes under `../../reference/tools/`

## Required skills

- `wordpress-router`
- `wp-project-triage`
- `wp-block-development`
- `wp-interactivity-api`
- `wp-performance`
- `wpds`

## Explicit exclusions

- Do not implement functionality assigned to a later phase unless it is a minimal seam/contract required by this phase.
- Do not change final product decisions without an ADR and product-owner approval.
- Do not introduce vendored reference-source copies without the Phase 00 license/version process.

## Documentation output

- Frontend controls, accessibility keyboard map, download policy, asset loading, and no-JS behavior.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
