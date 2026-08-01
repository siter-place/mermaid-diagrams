# Phase 05 Tests and Acceptance

## PHP unit/integration

- [ ] Combined filters and permission-limited results.
- [ ] Bulk operation semantics/limits/partial failures.
- [ ] Taxonomy permissions and duplicate rules.
- [ ] Preview/source authorization.

## JavaScript/Svelte unit tests

- [ ] Filter/query serialization and cancellation.
- [ ] Selection/bulk reducers and partial failure UX.
- [ ] Preview open/close/focus/cache.
- [ ] Quick-create validation/save.

## Bruno REST tests

- [ ] Search/filter/sort/paginate workflows.
- [ ] Category Add/Remove/Replace; tags; partial bulk failure.
- [ ] Preview/detail/permission checks.

## Playwright and visual tests

- [ ] Search/filter and bookmark URL state.
- [ ] Bulk taxonomy workflow.
- [ ] Preview panel keyboard/focus behavior.
- [ ] Duplicate/trash/restore.
- [ ] Visual baselines populated/filtered/preview/error.

## Acceptance outputs

- [ ] All launch library workflows are usable without page reloads.
- [ ] List payload/performance meets documented budgets.
- [ ] Bulk labels/semantics match final decisions.
- [ ] No grid implementation is introduced.

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
