# Phase 00 — Development Environment and Architecture Risk Spikes

**Delivery label:** Foundation  
**Depends on:** None. This is the first phase.  
**Previous phase:** None  
**Next phase:** Phase 01 — Plugin Kernel, Domain Model, Storage, and Capabilities

## Objective

Deliver one bounded, demonstrable vertical slice without implementing later-phase features early.

## Functional specification

- A developer can clone/open the repository in WSL2, install dependencies, start WordPress 7.0 on PHP 8.3 with wp-env, and access it from Windows/Cursor.
- Playwright CLI can authenticate and run one smoke test; Playwright MCP can inspect the local site from Cursor.
- Bruno CLI can authenticate with a dedicated WordPress Application Password and run one REST smoke request.
- The exact upstream versions/commits/licenses for Mermaid, Mermaid Live Editor, plugin-ui, MCP Adapter, OpenAI provider, Playwright, Bruno, and Agent Skills are recorded.
- Architecture spikes answer Live Editor static bundling, React compatibility, controlled SVG upload, validation-worker viability, and MCP Adapter packaging mode.

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
- `wp-rest-api`
- `wp-interactivity-api`
- `wp-abilities-api`
- `wp-wpcli-and-ops`
- `wp-phpstan`
- `wpds`
- `bruno-collection-generator`
- `bruno-ci-setup`

## Explicit exclusions

- Do not implement functionality assigned to a later phase unless it is a minimal seam/contract required by this phase.
- Do not change final product decisions without an ADR and product-owner approval.
- Do not introduce vendored reference-source copies without the Phase 00 license/version process.

## Documentation output

- Update environment docs, command catalog, sources lock, relevant ADRs, CI outline, and troubleshooting evidence.

## Handoff to next phase

The completion report must list stable APIs/contracts, fixtures, commands, migrations, feature flags, and known limitations that the next phase can rely on.
