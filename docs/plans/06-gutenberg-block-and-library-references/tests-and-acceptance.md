# Phase 06 Tests and Acceptance

## PHP unit/integration

- [ ] Block registration and metadata.
- [ ] Inline/reference server render, private/missing fallback, policy flags, unique IDs, escaping.
- [ ] Post reference validation warning service.

## JavaScript/Svelte unit tests

- [ ] Attribute invariants/transitions.
- [ ] Inline parse/render states.
- [ ] Library selector and save-to-library idempotency.
- [ ] Detach/reference refresh/error states.
- [ ] Deprecated attribute migration if present.

## Bruno REST tests

- [ ] Fixtures for block tests and reference permissions; no block UI through Bruno.

## Playwright and visual tests

- [ ] Insert inline valid/invalid diagram.
- [ ] Save to library and confirm reference mode.
- [ ] Choose existing, edit shared link, detach.
- [ ] Publish warning for private diagram.
- [ ] Visual baselines for block states in iframed editor.

## Acceptance outputs

- [ ] Block never saves invalid inline source.
- [ ] Referenced source remains canonical in library.
- [ ] WP 7.0 iframe editor styles/scripts work.
- [ ] All author workflows are keyboard usable and recover from REST errors.

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
