# Phase 02 Tests and Acceptance

## PHP unit/integration

- [ ] Every route unauthenticated/unauthorized/owner/editor/admin cases.
- [ ] Argument validation and stable response/error schemas.
- [ ] Pagination/filter/sort boundaries.
- [ ] Idempotency retry and mismatched-payload rejection.
- [ ] Version-token success/conflict.
- [ ] Settings merge/normalization/security constraints.

## JavaScript/Svelte unit tests

- [ ] REST client serialization, abort, error mapping, pagination, idempotency, 409 mapping, settings merge.
- [ ] Contract fixture validation.

## Bruno REST tests

- [ ] Create folders for Auth, Diagrams, Settings, Taxonomies, Negative, Cleanup.
- [ ] Run create/list/get/update/conflict/duplicate/trash/restore and permission workflows.
- [ ] Persist IDs/tokens in collection variables and clean up.

## Playwright and visual tests

- [ ] Minimal API-driven fixture creation helper; no full feature UI expected.

## Acceptance outputs

- [ ] REST and Bruno suites pass from clean wp-env reset.
- [ ] No route bypasses application services or capabilities.
- [ ] Schemas are reusable by React, Svelte, Abilities, and tests.
- [ ] Settings behave according to plugin-ui integration principles.

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
