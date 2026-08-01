# Phase 08 — Adapted Mermaid Live Editor, Save, Revisions, and Conflicts

**Delivery label:** Primary editor  
**Depends on:** Phases 02–03 persistence/validation and Phase 00 Live Editor spike.  
**Previous phase:** Phase 07 — Published Frontend Interactivity, Fullscreen, and Downloads  
**Next phase:** Phase 09 — SVG Thumbnails, Featured Images, Usage Index, and WP-Cron

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- Users can create/edit diagrams in a dedicated WordPress admin page powered by the adapted Mermaid Live Editor.
- Save is enabled only for valid changed source.
- Metadata, categories, tags, status, revisions, conflicts, unsaved recovery, and Save as Copy are integrated.
- Visual editor is not implemented in this phase.

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

- Live Editor adaptation map, upstream pin/patches, state machine, REST adapter, revisions/conflicts/recovery, and upgrade runbook.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
