# Phase 11 Tests and Acceptance

## PHP unit/integration

- [ ] Ability registration/category/schema/annotations.
- [ ] Permission matrix and shared-service delegation.
- [ ] Worker present/absent behavior.
- [ ] Version conflicts/idempotency/errors.
- [ ] MCP exposure allowlist.

## JavaScript/Svelte unit tests

- [ ] Optional client-side Abilities discovery UI/status if shipped; otherwise contract schema tests.

## Bruno REST tests

- [ ] Abilities discovery and execution over supported REST surface.
- [ ] Read/write permission matrix.
- [ ] Candidate-only and worker-backed create/update.
- [ ] Conflict and invalid candidate cases.

## Playwright and visual tests

- [ ] WordPress admin ability/MCP status and configuration if UI exists.
- [ ] End-to-end fixture created via ability then visible in library/block.

## Acceptance outputs

- [ ] `wp-abilities-audit` and `wp-abilities-verify` procedures pass.
- [ ] A subscriber/unauthorized agent cannot read/write private diagrams.
- [ ] MCP clients see only approved abilities.
- [ ] No invalid autonomous write is possible.

## Required evidence

- Exact commands and exit status.
- Test/report artifact locations.
- Screenshots/traces only for intentional UI evidence or failures.
- Before/after API/schema example when a public contract changes.
- Database migration/version evidence when storage changes.
- Accessibility and performance evidence when applicable.
- List of deferred items mapped to a later phase, not vague TODOs.

## Exit rule

Do not mark this phase complete while a required test is skipped without an approved, dated reason and target phase.
