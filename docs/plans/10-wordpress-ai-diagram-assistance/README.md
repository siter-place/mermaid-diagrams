# Phase 10 — WordPress AI Client Diagram Assistance

**Delivery label:** AI-assisted editing  
**Depends on:** Phase 08 editor, Phase 03 validation, WordPress 7 AI connector configured.  
**Previous phase:** Phase 09 — SVG Thumbnails, Featured Images, Usage Index, and WP-Cron  
**Next phase:** Phase 11 — Abilities API and WordPress MCP Adapter

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- Authorized users can ask the configured WordPress AI provider to generate a Mermaid candidate, repair a validation error, explain, simplify, or propose accessible title/description.
- The initial provider may be OpenAI configured in WordPress, but plugin behavior is provider-neutral.
- AI output is previewed as a candidate and never saved until Mermaid validation passes and the user applies/saves it.

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
- `wpds`
- `wp-performance`
- `bruno-test-writer`

## Explicit exclusions

- Do not implement functionality assigned to a later phase unless it is a minimal seam/contract required by this phase.
- Do not change final product decisions without an ADR and product-owner approval.
- Do not introduce vendored reference-source copies without the Phase 00 license/version process.

## Documentation output

- AI feature UX, provider setup, prompts/data flow, privacy, testing/fake provider, and operational limits.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
