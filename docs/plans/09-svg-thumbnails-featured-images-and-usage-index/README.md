# Phase 09 — SVG Thumbnails, Featured Images, Usage Index, and WP-Cron

**Delivery label:** Library scale/operations  
**Depends on:** Phases 05–08 can create/reference/render diagrams.  
**Previous phase:** Phase 08 — Adapted Mermaid Live Editor, Save, Revisions, and Conflicts  
**Next phase:** Phase 10 — WordPress AI Client Diagram Assistance

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- Every normal diagram save generates a browser SVG thumbnail and commits source plus featured image through one coordinated mutation; the save is not acknowledged when media persistence fails.
- Library rows/preview display the persisted featured SVG; failed save candidates remain local and expose a retryable error rather than a false saved state.
- Usage counts and reverse references are stored in WordPress DB, updated by WP-Cron, and repairable through WP-CLI.

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
- `wp-wpcli-and-ops`
- `wp-performance`
- `wp-phpstan`
- `bruno-test-writer`

## Explicit exclusions

- Do not implement functionality assigned to a later phase unless it is a minimal seam/contract required by this phase.
- Do not change final product decisions without an ADR and product-owner approval.
- Do not introduce vendored reference-source copies without the Phase 00 license/version process.

## Documentation output

- SVG threat model/workflow, media behavior, cron/index schema, WP-CLI operations, troubleshooting and performance budgets.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
