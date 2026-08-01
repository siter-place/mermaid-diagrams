# Phase 01 Tests and Acceptance

## PHP unit/integration

- [ ] Domain invariants and value objects.
- [ ] CPT/taxonomy/meta registration.
- [ ] Capability matrix and role assignment idempotency.
- [ ] Repository create/read/update/revision/trash/restore basics with valid fixtures.
- [ ] Migration and uninstall preserve/delete policies.
- [ ] Multisite per-site activation behavior where supported.

## JavaScript/Svelte unit tests

- [ ] None beyond shared contract type checks.

## Bruno REST tests

- [ ] No broad API yet; retain health/current-user smoke.

## Playwright and visual tests

- [ ] Plugin activation/admin menu visibility and permission-based menu absence for a subscriber.

## Acceptance outputs

- [ ] Plugin activates with no notices/fatals on WP 7.0/PHP 8.3.
- [ ] Domain and storage tests pass.
- [ ] No controller/UI writes directly to global WordPress APIs outside infrastructure adapters.
- [ ] Capabilities are documented and least-privileged.
- [ ] Database upgrades are repeatable.

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
