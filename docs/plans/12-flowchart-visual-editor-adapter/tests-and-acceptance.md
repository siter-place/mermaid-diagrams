# Phase 12 Tests and Acceptance

## PHP unit/integration

- [ ] Feature flag/settings and permissions only; source mutation still uses existing REST/validation.

## JavaScript/Svelte unit tests

- [ ] Parser/serializer/IR and round-trip corpus.
- [ ] Compatibility/loss reports.
- [ ] Graph actions/undo/redo.
- [ ] Apply transaction and validation.

## Bruno REST tests

- [ ] Existing update/validation routes regression; no special visual REST API unless justified.

## Playwright and visual tests

- [ ] Supported edit/apply/save.
- [ ] Unsupported read-only/loss warning.
- [ ] Keyboard graph actions, undo/redo.
- [ ] Visual baselines for graph canvas/panels/errors.

## Acceptance outputs

- [ ] No corpus case loses unsupported semantics silently.
- [ ] Source remains canonical.
- [ ] Feature can be disabled without affecting source editor.
- [ ] Accessibility limitations are documented and mitigated.

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
