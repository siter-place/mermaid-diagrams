# Phase 03 — Mermaid Validation, Rendering, SVG, and Validation Worker

**Delivery label:** Rendering foundation  
**Depends on:** Phase 02 contracts/routes exist.  
**Previous phase:** Phase 02 — REST API, Settings, Error Model, and Shared Contracts  
**Next phase:** Phase 04 — React Diagram Library Shell and Settings UI

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- First-party clients can validate Mermaid source, obtain normalized diagnostics/type/hash, render accessible SVG, and save only valid source.
- Users can download `.mmd` and sanitized SVG according to policy.
- Autonomous writers have a defined worker-backed validation path or receive candidate-only behavior.
- Mermaid security and configuration cannot be weakened by source.

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
- `wp-performance`
- `wp-phpstan`
- `bruno-test-writer`

## Explicit exclusions

- Do not implement functionality assigned to a later phase unless it is a minimal seam/contract required by this phase.
- Do not change final product decisions without an ADR and product-owner approval.
- Do not introduce vendored reference-source copies without the Phase 00 license/version process.

## Documentation output

- Pin Mermaid version, validation trust model, worker deployment profile, corpus policy, security settings, and upgrade checklist.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
