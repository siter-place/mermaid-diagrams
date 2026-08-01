# Phase 06 — Gutenberg Block, Inline Diagrams, and Library References

**Delivery label:** Authoring integration  
**Depends on:** Phases 02–05 provide REST, rendering, and library data.  
**Previous phase:** Phase 05 — Diagram Library Search, Taxonomy, Bulk Actions, and Preview  
**Next phase:** Phase 07 — Published Frontend Interactivity, Fullscreen, and Downloads

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- Authors can insert `mdm/diagram`, create an inline valid diagram, select an existing library diagram, save an inline diagram to the library, detach a reference, and open the shared editor.
- Referenced blocks show latest accessible diagram and warn when missing/private/draft.
- Post publication surfaces a warning when a public post references a non-public diagram.

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
- `wp-rest-api`
- `wp-interactivity-api`
- `wpds`
- `bruno-test-writer`

## Explicit exclusions

- Do not implement functionality assigned to a later phase unless it is a minimal seam/contract required by this phase.
- Do not change final product decisions without an ADR and product-owner approval.
- Do not introduce vendored reference-source copies without the Phase 00 license/version process.

## Documentation output

- Block attribute contract, user journeys, dynamic render security, deprecation policy, and test selectors.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
