# Phase 02 — REST API, Settings, Error Model, and Shared Contracts

**Delivery label:** API foundation  
**Depends on:** Phase 01 domain/repositories/capabilities complete.  
**Previous phase:** Phase 01 — Plugin Kernel, Domain Model, Storage, and Capabilities  
**Next phase:** Phase 03 — Mermaid Validation, Rendering, SVG, and Validation Worker

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- Authenticated clients can list, get, create, update, duplicate, trash, restore, and manage taxonomy assignments through a stable `mdm/v1` REST contract.
- Settings are loaded/saved section by section using server-authoritative schemas and normalized responses.
- Clients receive stable error codes, field diagnostics, pagination headers, and optimistic concurrency/version tokens.
- Invalid source cannot yet be saved because source mutation routes require the validation contract stub that Phase 03 completes.

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
- `wp-phpstan`
- `bruno-collection-generator`
- `bruno-test-writer`
- `bruno-ci-setup`

## Explicit exclusions

- Do not implement functionality assigned to a later phase unless it is a minimal seam/contract required by this phase.
- Do not change final product decisions without an ADR and product-owner approval.
- Do not introduce vendored reference-source copies without the Phase 00 license/version process.

## Documentation output

- Publish endpoint/settings/error/idempotency/concurrency docs and Bruno collection usage.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
