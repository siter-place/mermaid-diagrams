# Phase 12 — Flowchart Visual Editor Adapter

**Delivery label:** Post-source-editor feature  
**Depends on:** Phases 03 and 08 stable; representative corpus and visual baselines exist.  
**Previous phase:** Phase 11 — Abilities API and WordPress MCP Adapter  
**Next phase:** Phase 13 — Security, Accessibility, Performance, CI, and Release

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- Supported Mermaid flowcharts can be opened in a visual node/edge editor, modified, serialized back to Mermaid, validated, and applied without silent loss.
- Unsupported types or syntax show a compatibility/loss report and remain source-editor-only.
- Visual editing can ship behind a feature flag/beta label independently of 1.0 source editing.

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

## Explicit exclusions

- Do not implement functionality assigned to a later phase unless it is a minimal seam/contract required by this phase.
- Do not change final product decisions without an ADR and product-owner approval.
- Do not introduce vendored reference-source copies without the Phase 00 license/version process.

## Documentation output

- Supported syntax matrix, adapter contract, IR, loss policy, user guide, corpus, and upgrade procedure.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
