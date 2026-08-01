# Phase 10 Tests and Acceptance

## PHP unit/integration

- [ ] No-connector/unsupported-model errors.
- [ ] Prompt construction and fake provider success/failure/timeout.
- [ ] Capability/rate/privacy policy.
- [ ] Candidate response never persists.

## JavaScript/Svelte unit tests

- [ ] AI menu states, cancellation, candidate/diff/apply/discard.
- [ ] Repair invalid source and validate before Save.
- [ ] Accessibility metadata proposal.

## Bruno REST tests

- [ ] AI endpoint permission/no connector/fake provider candidate.
- [ ] Assert no automatic diagram mutation.

## Playwright and visual tests

- [ ] Fake provider generate/apply/save.
- [ ] Repair invalid source.
- [ ] Cancel/error/no connector UI.
- [ ] Visual baselines for candidate/diff/loading/error.

## Acceptance outputs

- [ ] No direct OpenAI SDK/key handling exists in plugin.
- [ ] AI output cannot bypass validation or explicit user action.
- [ ] Provider-neutral tests pass.
- [ ] Privacy/cost/error behavior is documented.

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
