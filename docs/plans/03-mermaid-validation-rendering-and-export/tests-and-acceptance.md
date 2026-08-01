# Phase 03 Tests and Acceptance

## PHP unit/integration

- [ ] Receipt hash/version/profile policy.
- [ ] Source constraints/denied configuration.
- [ ] Route rejection for missing, mismatched, stale and unauthorized receipts.
- [ ] Download authorization and filename/header policy.

## JavaScript/Svelte unit tests

- [ ] Valid/invalid parse and diagnostics.
- [ ] Locked security configuration.
- [ ] Concurrent/stale render protection.
- [ ] Accessible sanitized SVG.
- [ ] Source/SVG downloads.
- [ ] Corpus compatibility for pinned Mermaid version.

## Bruno REST tests

- [ ] Valid create/update with worker or approved test receipt.
- [ ] Reject invalid/mismatched/missing receipt.
- [ ] Download policy and private source exposure.

## Playwright and visual tests

- [ ] Small representative render and error state harness.
- [ ] Visual snapshots for representative flowchart/sequence/class diagrams.

## Acceptance outputs

- [ ] No mutation path used by the plugin persists unvalidated source.
- [ ] Browser and worker report the same result for the corpus.
- [ ] Malicious fixtures cannot execute or weaken security.
- [ ] SVG/source downloads are correct and deterministic.

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
