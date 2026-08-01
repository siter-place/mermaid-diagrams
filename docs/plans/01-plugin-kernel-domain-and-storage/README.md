# Phase 01 — Plugin Kernel, Domain Model, Storage, and Capabilities

**Delivery label:** Core foundation  
**Depends on:** Phase 00 accepted; toolchain and dependency decisions pinned.  
**Previous phase:** Phase 00 — Development Environment and Architecture Risk Spikes  
**Next phase:** Phase 02 — REST API, Settings, Error Model, and Shared Contracts

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- The Mermaid Diagrams plugin activates, registers its diagram library model, and exposes correct WordPress admin capabilities.
- Administrators can see a Diagrams menu placeholder; no React feature UI is required yet.
- The plugin stores diagrams as a dedicated post type with multiple hierarchical categories and non-hierarchical tags.
- Activation/upgrade/deactivation/uninstall behavior is safe, idempotent, versioned, and preserves content by default.

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
- `wp-wpcli-and-ops`
- `wp-phpstan`
- `wp-plugin-directory-guidelines`

## Explicit exclusions

- Do not implement functionality assigned to a later phase unless it is a minimal seam/contract required by this phase.
- Do not change final product decisions without an ADR and product-owner approval.
- Do not introduce vendored reference-source copies without the Phase 00 license/version process.

## Documentation output

- Update folder structure, domain model, storage schema, capabilities matrix, lifecycle, and relevant ADRs.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
