# Phase 04 — React Diagram Library Shell and Settings UI

**Delivery label:** Admin UI foundation  
**Depends on:** Phases 01–03 provide data, REST, settings, and valid fixtures.  
**Previous phase:** Phase 03 — Mermaid Validation, Rendering, SVG, and Validation Worker  
**Next phase:** Phase 05 — Diagram Library Search, Taxonomy, Bulk Actions, and Preview

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- Users with permission can open a WordPress-native Diagrams admin page that loads, handles errors, and displays a paginated list shell.
- Administrators can open and save plugin settings through section-level REST integration.
- The React application has consistent providers, notices, routing/query state, and CSS isolation.

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
- `wp-plugin-development`
- `wp-rest-api`
- `wpds`
- `wp-performance`
- `bruno-test-writer`

## Explicit exclusions

- Do not implement functionality assigned to a later phase unless it is a minimal seam/contract required by this phase.
- Do not change final product decisions without an ADR and product-owner approval.
- Do not introduce vendored reference-source copies without the Phase 00 license/version process.

## Documentation output

- Document React app architecture, provider/adapter choices, settings sections, UI test IDs, and screenshots/baseline policy.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
