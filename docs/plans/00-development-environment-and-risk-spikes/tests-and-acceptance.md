# Phase 00 Tests and Acceptance

## PHP unit/integration

- [x] Plugin header/bootstrap syntax and activation smoke only.
- [x] No production domain behavior is expected yet.

## JavaScript/Svelte unit tests

- [x] Toolchain smoke build.
- [x] Mermaid browser parse/render spike.
- [x] Node validation-worker parse spike using same version.

## Bruno REST tests

- [x] GET WordPress REST index or plugin temporary health route.
- [x] Authenticated current-user request with Application Password.

## Playwright and visual tests

- [x] Login setup and storage state.
- [x] Open wp-admin and verify WordPress version/plugin page.
- [x] One deterministic baseline image proving WSL2 visual setup.

## Acceptance outputs

- [x] `npm install`, `npm run env:start`, Playwright smoke, and Bruno smoke succeed from documented clean steps.
- [x] All source versions/licenses and spike results are committed.
- [x] No secret is committed.
- [x] ADRs select build boundaries and fallback behavior.
- [x] Phase 01 can begin without unresolved environment uncertainty.

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
