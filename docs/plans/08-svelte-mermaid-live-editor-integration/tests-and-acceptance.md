# Phase 08 Tests and Acceptance

## PHP unit/integration

- [ ] Admin route/capability/enqueue/bootstrap.
- [ ] Revision permissions/restore creates new current version.
- [ ] Conflict/version token behavior.

## JavaScript/Svelte unit tests

- [ ] Svelte adapter and state machine.
- [ ] Validation debounce/cancellation.
- [ ] Save/normalized response/error/conflict.
- [ ] Local recovery.
- [ ] Metadata/taxonomy and revisions.

## Bruno REST tests

- [ ] Create/update/revision/conflict/restore workflows used by editor.

## Playwright and visual tests

- [ ] Create valid; invalid Save disabled; fix/save.
- [ ] Edit metadata/categories/tags/status.
- [ ] Reload recovery.
- [ ] Two-context conflict and resolution.
- [ ] Revision view/restore.
- [ ] Visual baselines loading/valid/invalid/saving/error/conflict.

## Acceptance outputs

- [ ] Static Svelte app loads within wp-admin with no global collisions.
- [ ] No upstream persistence bypasses WordPress REST.
- [ ] Invalid source cannot be saved.
- [ ] Conflicts never silently overwrite.
- [ ] Patch/upgrade procedure is documented.

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
