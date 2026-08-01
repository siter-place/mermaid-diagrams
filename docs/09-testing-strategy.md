# 9. Testing Strategy

## 9.1 Test ownership

- PHP unit tests: domain/application logic.
- WordPress integration tests: CPT/taxonomy/meta, repositories, cron, dynamic block, media, permissions, migrations.
- JavaScript/TypeScript unit tests: contracts, Mermaid runtime, state machines, adapters, SVG sanitization client step.
- Svelte component/unit tests: adapted Live Editor state and WordPress adapter.
- Bruno: authoritative black-box REST workflow and contract collection.
- Playwright: browser workflows, accessibility-critical interactions, downloads, conflicts, and visual regression.
- Static quality: PHPCS/WPCS, PHPStan, ESLint, TypeScript, style linting, dependency/license checks, Plugin Check.

## 9.2 Environments

1. Minimum profile: WordPress 7.0, PHP 8.3, pinned Chromium, pinned Mermaid.
2. Forward profile: WordPress trunk/current RC with PHP 8.3.
3. CI profile: clean wp-env, deterministic fixtures, no manually configured state.
4. AI/MCP profile: OpenAI provider and MCP Adapter installed; secrets injected only in protected/manual jobs. Most tests use fake provider/transport adapters.
5. Validation-worker profile: same Mermaid version as browser bundle.

## 9.3 Required invariants

- Invalid Mermaid source is never persisted through REST, UI, Ability, MCP, import, revision restore, or bulk action.
- REST and Abilities enforce the same capabilities and domain rules.
- Referenced blocks never expose private/draft diagrams publicly.
- Mermaid security configuration cannot be weakened by source or request data.
- A failed featured-SVG operation prevents save acknowledgement, preserves the previous canonical source/thumbnail, and keeps the candidate in local recovery.
- Usage index is eventually correct and cron is idempotent.
- AI output cannot bypass validation.

## 9.4 PHP unit and integration coverage

- Diagram aggregate transitions and value objects.
- Source/hash/receipt matching and complexity constraints.
- Capability policy for owner/editor/manager/admin.
- Repository create/update/revision/duplicate/trash/restore.
- Multiple hierarchical categories and tags.
- REST schemas, error codes, version conflicts, idempotency.
- Worker-required policy for autonomous writers.
- Dynamic block rendering and inaccessible/missing fallbacks.
- Usage dirty queue, bounded cron batches, reconciliation, manual WP-CLI path.
- Controlled SVG upload, sanitizer, provenance, attachment reuse/orphan cleanup.
- Activation/upgrade/uninstall and multisite per-site behavior.
- Ability schemas, callbacks, annotations, permission checks, and shared-service delegation.

## 9.5 JavaScript and Svelte coverage

- Mermaid initialization and locked security configuration.
- `parse()` success/failure diagnostics and diagram type detection.
- Validation receipt generation and stale-source cancellation.
- SVG rendering, accessible title/description, sanitization and download.
- React Library query/filter/bulk state.
- Gutenberg inline/reference state transitions and save-to-library idempotency.
- Svelte Live Editor clean/dirty/invalid/saving/saved/conflict/recovery states.
- AI candidate application and validation failure handling.
- Visual adapter parsing/serialization/round-trip corpus when Phase 12 starts.

## 9.6 Bruno REST collection

The committed `bruno/` collection is the executable REST contract. It contains:

- health/schema/settings;
- authentication/current user;
- create valid diagram;
- reject invalid or missing validation receipt;
- search/filter/paginate;
- get/update with ETag/version token;
- 409 stale update;
- category Add/Remove/Replace and tags;
- duplicate/trash/restore;
- thumbnail upload positive/negative fixtures;
- usage and reindex endpoints;
- permission matrix;
- abilities discovery/execution where HTTP exposed;
- cleanup folder.

Use a dedicated WordPress test user and Application Password. Secrets live in `.env` or CI secret storage, never collection files. Bruno CLI 3.x safe sandbox is the default. Use developer sandbox only if a reviewed script truly needs filesystem or package access.

Required reports:

```bash
bru run bruno --env Local \
  --reporter-json bruno/reports/results.json \
  --reporter-junit bruno/reports/results.xml
```

## 9.7 Playwright workflows

- Admin login/setup project and reusable storage state.
- Gutenberg inline valid/invalid behavior.
- Save inline to library and reference refresh.
- Library filters, categories/tags, bulk operations, preview panel.
- Live Editor create/edit/save/revision/conflict/local recovery.
- Frontend zoom/pan/fit/reset/fullscreen/download and keyboard behavior.
- Private/missing referenced fallback.
- Coordinated source-plus-featured-SVG save completes and the attachment is visible.
- AI candidate flow using fake provider.
- MCP/ability scenario via test adapter where practical.

## 9.8 Visual regression

Baseline regions:

- block empty state, valid preview, invalid error;
- library table and preview panel;
- Live Editor loading, valid, invalid, saving, conflict;
- frontend toolbar and fullscreen fallback;
- representative small, wide, tall, dark-theme, and RTL diagrams.

Stabilization rules are defined in `development/cursor-playwright-mcp.md`. No arbitrary sleeps. Wait for plugin-owned render/status markers.

## 9.9 Release gate

- all static checks pass;
- unit/integration suites pass on minimum profile;
- Bruno contract suite passes from a clean reset;
- Playwright functional and visual suites pass;
- accessibility scan and manual keyboard review completed;
- ability audit/verify completed;
- dependency/license/security review completed;
- production ZIP contains built assets, no tests/dev secrets/node_modules;
- documentation and changelog match behavior.
