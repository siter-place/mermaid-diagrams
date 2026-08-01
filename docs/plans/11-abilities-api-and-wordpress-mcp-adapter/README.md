# Phase 11 — Abilities API and WordPress MCP Adapter

**Delivery label:** Agent integration  
**Depends on:** Application services from Phases 02–03, 09–10; MCP Adapter choice from Phase 00.  
**Previous phase:** Phase 10 — WordPress AI Client Diagram Assistance  
**Next phase:** Phase 12 — Flowchart Visual Editor Adapter

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- Authorized MCP/chat clients can discover approved Mermaid Diagrams capabilities and list/get/generate/create/update diagrams according to WordPress permissions.
- Direct autonomous persistence is available only with worker validation; otherwise generation returns a candidate requiring Live Editor validation.
- Abilities expose accurate schemas/annotations and no hidden privilege.

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
- `wp-abilities-api`
- `wp-abilities-audit`
- `wp-abilities-verify`
- `wp-wpcli-and-ops`
- `bruno-test-writer`

## Explicit exclusions

- Do not implement functionality assigned to a later phase unless it is a minimal seam/contract required by this phase.
- Do not change final product decisions without an ADR and product-owner approval.
- Do not introduce vendored reference-source copies without the Phase 00 license/version process.

## Documentation output

- Ability catalog/schemas/permissions/annotations, MCP setup for clients, worker requirement, threat model, and troubleshooting.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
