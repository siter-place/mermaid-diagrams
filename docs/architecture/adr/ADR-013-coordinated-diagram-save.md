# ADR-013: Coordinate Canonical Source and Featured SVG in One Save Command

**Status:** Accepted

## Context

The product requires a featured SVG to be generated in the browser during diagram save. Treating source persistence and thumbnail upload as two independent successes creates a misleading saved state, stale previews, and hard-to-reconcile failures.

## Decision

After Phase 09, first-party create/update requests carry canonical source, validation receipt, featured SVG envelope, and optimistic version token together. One application command validates all inputs, stages the derived attachment, commits the diagram and `_thumbnail_id`, and acknowledges success only when the whole logical operation completes.

WordPress cannot provide a transaction spanning posts, attachment files, and metadata. Therefore the implementation uses explicit staging and compensating cleanup. Existing persisted state remains unchanged on update failure. A newly created partial record is deleted on failure. The browser retains the valid candidate locally until a successful response.

A separate regeneration route is operational repair only and can update a thumbnail only when its hash matches the currently persisted source.

## Consequences

- The UI cannot display “saved” before the featured SVG exists.
- Create/update DTOs grow after Phase 09 and require contract versioning/tests.
- Media failure is visible and retryable without data loss.
- Old derived attachments need replacement/orphan cleanup rules.
- Bruno, PHPUnit, and Playwright tests must cover every partial-failure boundary.
