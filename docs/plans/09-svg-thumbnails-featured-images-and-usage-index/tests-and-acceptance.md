# Phase 09 Tests and Acceptance

## PHP unit/integration

- [ ] SVG sanitizer malicious fixtures, capability, provenance, MIME scope, attachment assignment/reuse/cleanup.
- [ ] Usage table/schema, dirty queue, parser, cron batches/idempotency/reconciliation.
- [ ] WP-CLI targeted/all reindex.

## JavaScript/Svelte unit tests

- [ ] Coordinated source+SVG command construction, matching hash/version, local recovery, and retry behavior.
- [ ] Library/editor pending/success/failure states.

## Bruno REST tests

- [ ] Coordinated create/update valid/invalid/unauthorized/oversize/hash mismatch plus repair-only regeneration cases.
- [ ] Usage endpoint, cron/manual reindex workflow.

## Playwright and visual tests

- [ ] Save creates featured SVG and Library shows it.
- [ ] Featured-SVG failure blocks save acknowledgement, preserves prior version, retains local candidate, then succeeds on retry.
- [ ] Create/update/remove references and observe eventual usage after cron run.
- [ ] Deletion warning.
- [ ] Visual snapshot with thumbnails.

## Acceptance outputs

- [ ] No arbitrary SVG upload is enabled.
- [ ] Malicious SVG is rejected/sanitized.
- [ ] Featured-SVG failure never mutates prior canonical state and never loses the local valid candidate.
- [ ] Usage counts converge and cron can be rerun safely.
- [ ] Large-site operations are bounded.

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
