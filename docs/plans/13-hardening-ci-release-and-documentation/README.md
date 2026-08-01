# Phase 13 — Security, Accessibility, Performance, CI, and Release

**Delivery label:** Release candidate  
**Depends on:** All release-target phases complete; Phase 12 may be included or feature-flagged/deferred.  
**Previous phase:** Phase 12 — Flowchart Visual Editor Adapter  
**Next phase:** Release complete

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- A production ZIP can be built, installed, upgraded, tested, and operated with documented security/accessibility/performance guarantees.
- CI enforces static, unit, integration, Bruno, Playwright, visual, ability, dependency, and packaging gates.
- Users/admins/developers have complete installation, configuration, usage, AI/MCP, troubleshooting, privacy, and uninstall documentation.

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
- `wp-block-development`
- `wp-block-themes`
- `wp-rest-api`
- `wp-interactivity-api`
- `wp-abilities-api`
- `wp-abilities-audit`
- `wp-abilities-verify`
- `wp-wpcli-and-ops`
- `wp-performance`
- `wp-phpstan`
- `wp-playground`
- `wpds`
- `wp-plugin-directory-guidelines`
- `blueprint`
- `bruno-ci-setup`

## Explicit exclusions

- Do not implement functionality assigned to a later phase unless it is a minimal seam/contract required by this phase.
- Do not change final product decisions without an ADR and product-owner approval.
- Do not introduce vendored reference-source copies without the Phase 00 license/version process.

## Documentation output

- Finalize every user/developer/ops/release document, manifest, source lock, licenses, changelog, known limitations, and future roadmap.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
