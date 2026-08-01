# Phase 00 Tests and Acceptance

## PHP unit/integration

- [ ] Plugin header/bootstrap syntax and activation smoke only.
- [ ] No production domain behavior is expected yet.

## JavaScript/Svelte unit tests

- [ ] Toolchain smoke build.
- [ ] Mermaid browser parse/render spike.
- [ ] Node validation-worker parse spike using same version.

## Bruno REST tests

- [ ] GET WordPress REST index or plugin temporary health route.
- [ ] Authenticated current-user request with Application Password.

## Playwright and visual tests

- [ ] Login setup and storage state.
- [ ] Open wp-admin and verify WordPress version/plugin page.
- [ ] One deterministic baseline image proving WSL2 visual setup.

## Acceptance outputs

- [ ] `npm install`, `npm run env:start`, Playwright smoke, and Bruno smoke succeed from documented clean steps.
- [ ] All source versions/licenses and spike results are committed.
- [ ] No secret is committed.
- [ ] ADRs select build boundaries and fallback behavior.
- [ ] Phase 01 can begin without unresolved environment uncertainty.

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
