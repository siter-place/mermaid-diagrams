# Phase 13 Tests and Acceptance

## PHP unit/integration

- [ ] Full suite and mutation/security regression.
- [ ] Upgrade/uninstall/multisite/cron/abilities/media.

## JavaScript/Svelte unit tests

- [ ] Full React/Svelte/runtime/visual suite and coverage thresholds.

## Bruno REST tests

- [ ] Full collection from clean reset; JSON/JUnit/HTML artifacts.
- [ ] Minimum/forward profile where feasible.

## Playwright and visual tests

- [ ] Full functional, accessibility smoke, download, conflict, AI fake, MCP fixture, visual suite.
- [ ] Production ZIP installed in a clean environment, not source tree.

## Acceptance outputs

- [ ] All release gates pass and artifacts are reproducible.
- [ ] No critical/high security issue or unresolved accessibility blocker.
- [ ] Performance budgets pass or are explicitly approved with evidence.
- [ ] Production ZIP contains only intended files.
- [ ] Documentation/ADRs/plans match implementation.

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


## Version synchronization acceptance

- [ ] `npm run test:versioning` passes.
- [ ] Dry run lists the expected files without modifying the working tree.
- [ ] Root npm and Composer project versions update while dependency versions remain untouched.
- [ ] WordPress plugin header, `MDM_VERSION`, and `readme.txt` Stable tag match the release.
- [ ] `CHANGELOG.md` contains the dated release and retains earlier release history.
- [ ] `docs/decision-log.md` contains `REL-<version>` with previous/new version and date.
- [ ] Same-version, malformed-version, duplicate-release, and downgrade attempts fail safely.
- [ ] Release evidence includes a reviewed `git diff`.
