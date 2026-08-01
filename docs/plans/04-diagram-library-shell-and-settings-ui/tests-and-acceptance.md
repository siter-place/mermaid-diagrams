# Phase 04 Tests and Acceptance

## PHP unit/integration

- [ ] Admin page enqueue only for correct screen/capability.
- [ ] Settings permissions and sanitized data.
- [ ] Asset metadata/translation registration.

## JavaScript/Svelte unit tests

- [ ] Provider composition, bootstrap failure, query state, error boundary, list loading/empty/error, settings dirty/save/error/normalization.

## Bruno REST tests

- [ ] Use settings/diagrams contracts as backend regression.

## Playwright and visual tests

- [ ] Open library, loading/empty/populated/error states.
- [ ] Settings section save and refresh.
- [ ] Visual baselines for library shell and settings.
- [ ] Keyboard/focus smoke.

## Acceptance outputs

- [ ] Library/settings load without console errors.
- [ ] No CSS leakage outside plugin screen.
- [ ] Settings are server-authoritative and section-level.
- [ ] Table/list is responsive and accessible.

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
