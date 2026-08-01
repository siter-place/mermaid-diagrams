# Phase 05 — Diagram Library Search, Taxonomy, Bulk Actions, and Preview

**Delivery label:** Admin feature complete  
**Depends on:** Phase 04 shell and Phases 02–03 APIs.  
**Previous phase:** Phase 04 — React Diagram Library Shell and Settings UI  
**Next phase:** Phase 06 — Gutenberg Block, Inline Diagrams, and Library References

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- Diagram managers can search, filter, sort, preview, create, duplicate, categorize, tag, publish, trash, restore, and bulk-manage diagrams.
- Category bulk actions are unambiguous Add/Remove/Replace.
- The preview panel loads rendered content on demand and displays usage/thumbnail status without bloating list payloads.

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

- Update user workflow, filters/actions, accessibility, endpoint examples, and operational limits.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
