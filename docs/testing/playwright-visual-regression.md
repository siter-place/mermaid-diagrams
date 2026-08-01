# Playwright Functional and Visual Regression Strategy

## Role split

Playwright Test is the committed browser test system. Playwright MCP is an exploratory Cursor tool used to inspect the local site, reproduce failures, and discover robust selectors. MCP interaction is never accepted as test evidence until converted to a committed test.

## Deterministic baseline profile

Visual baselines are generated only with the project profile:

- WSL2 Ubuntu project filesystem;
- Docker/wp-env WordPress 7.0 and PHP 8.3;
- project-pinned Playwright and Chromium;
- fixed viewport/device scale, locale, timezone, reduced motion;
- controlled WordPress theme/content/users;
- pinned Mermaid version/theme/fonts;
- animations disabled;
- network idle plus plugin render-state markers.

Do not mix Windows-host and WSL baseline images.

## Snapshot scopes

Prefer stable regions:

- block editor diagram preview and inspector;
- library table/filter/preview panel;
- Live Editor shell, valid/invalid/conflict states;
- frontend toolbar, fullscreen fallback, and diagram viewport;
- AI candidate/diff UI with mocked response;
- visual flowchart adapter later.

Mask timestamps, avatars, generated IDs, nonces, and provider data. Full-page screenshots are reserved for a few layout smoke tests.

## Functional suites

- setup/login/fixtures;
- library CRUD/search/filter/category/tag/bulk workflows;
- Gutenberg inline/reference/save-to-library/publish warnings;
- frontend zoom/pan/fit/reset/fullscreen/download;
- Live Editor validation, local recovery, coordinated save, revisions, conflicts;
- failed featured-SVG save preserving prior server state and local candidate;
- accessibility keyboard/focus/announcements;
- AI mocked candidate/apply/reject;
- MCP/abilities UI-visible effects only where appropriate.

## Render readiness

Applications expose stable markers such as:

- `data-mdm-app-state="ready"`;
- `data-mdm-render-state="ready|error"`;
- `data-mdm-save-state="clean|dirty|validating|saving|error|conflict"`.

Tests wait for these and semantic UI states, never arbitrary sleeps.

## Update policy

Run `npm run test:e2e:update` only for intended UI changes. Review before/after/diff artifacts and record the reason in the phase completion report or pull request. A wholesale baseline regeneration without review fails the release process.

## Playwright MCP safety

Configure the official server with `npx @playwright/mcp@latest` inside the WSL project context. Do not enable arbitrary unsafe browser-host code execution in routine agent work. Use standard navigation, accessibility snapshot, click, fill, and screenshot capabilities; treat persistent profiles as local secrets.
