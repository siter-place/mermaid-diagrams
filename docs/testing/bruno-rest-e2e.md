# Bruno REST End-to-End Strategy

## Role

The committed `bruno/` collection is the authoritative black-box contract suite for browser-facing REST routes. It tests the plugin as an HTTP client would; it does not replace controller/application unit tests.

## Local model

- Base URL: `http://localhost:8888`.
- Authentication: dedicated WordPress test user and Application Password through Basic Auth.
- Secrets: process environment or ignored `.env`; never committed to `.bru` files.
- CLI: project-local `@usebruno/cli` invoked by npm scripts.
- Runtime: Bruno CLI 3.x Safe Mode unless a reviewed script has a documented need for Developer Mode.

## Collection layout

```text
bruno/
  00 Smoke/          REST root and health
  01 Auth/           unauthenticated/forbidden/least privilege
  02 Diagrams/       CRUD, duplicate, conflict, trash/restore
  03 Validation/     receipt/source/version/worker rules
  04 Taxonomies/     categories/tags and cardinality
  05 Bulk/           Add/Remove/Replace and partial errors
  06 Settings/       section contracts and permissions
  07 Thumbnails/     repair-only regeneration and SVG attacks
  08 Usage/          counts, warnings, reindex status
  09 Abilities/      REST-visible ability discovery/invocation where applicable
  99 Cleanup/        deterministic fixture cleanup
```

Normal source-plus-thumbnail create/update cases belong in `02 Diagrams`; `07 Thumbnails` is only for regeneration/repair.

## Test design

Each endpoint gets:

- happy path with exact schema assertions;
- unauthenticated and insufficient-capability cases;
- unknown/additional properties;
- boundary lengths and invalid types;
- stable error code/status checks;
- idempotency replay where supported;
- optimistic conflict;
- cross-user information leakage checks;
- source/validation/SVG hash mismatch;
- cleanup or unique fixture isolation.

Avoid brittle whole-response snapshots. Assert contract fields and security-relevant absence/presence.

## Reporting

```bash
npx bru run bruno --env Local   --reporter-json bruno/reports/results.json   --reporter-junit bruno/reports/results.xml   --reporter-html bruno/reports/results.html   --reporter-skip-headers Authorization Cookie X-WP-Nonce
```

Reports are ignored locally and uploaded as CI artifacts. JUnit drives CI status. HTML is for human diagnosis; sensitive headers are skipped.

## Phase activation

Phase 00 validates collection syntax and runs smoke/auth bootstrap. Each later phase activates only its folder after the endpoint exists. The phase exit criterion includes a collection run and report; placeholder requests do not count as evidence.

## Bruno Agent Skills

Use collection-generator for structure/environments, test-writer for assertions/chaining/negative cases, and CI-setup for scripts/reports/secrets. Record the installed skill versions/commit in Phase 00 rather than copying their repository into this project.
