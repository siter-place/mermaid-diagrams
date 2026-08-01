# Project Bootstrap Checklist

Completed in Phase 00 under WSL2 Linux filesystem.

## Host

- [x] Docker Desktop uses the WSL2 backend and Ubuntu integration is enabled.
- [x] Repository is under `/home/black/workspace/wp-plugins-development/mermaid-diagrams`, not `/mnt/c`.
- [x] Cursor opens the WSL folder and its terminal resolves Linux Node (v24.18.1)/PHP (8.3.33)/Docker.
- [x] Node version satisfies `package.json` (`>=24.16.0`); npm 11 and Docker Compose work.

## Repository dependencies

- [x] Run `npm install` and committed `package-lock.json` with pinned exact versions.
- [x] Installed Chromium via `npx playwright install chromium`.
- [x] Run `composer update` and committed `composer.lock` with pinned dev dependencies.
- [x] Recorded source commits, versions, and licenses in `docs/reference/tools/sources-lock.md`.

## wp-env

- [x] Run `npm run env:start` (WordPress 7.0 on PHP 8.3 at http://localhost:8888).
- [x] Confirmed WordPress 7.0, PHP 8.3, site URL, and plugin mounts.
- [x] Confirmed MCP Adapter (0.5.0) and OpenAI provider (1.0.3) plugins are present and active.
- [x] Idempotent lifecycle script `tools/wp-env/after-start.mjs` configures test environment and user.
- [x] Created the dedicated Bruno test user `mdm_api_test` and Application Password.

## Browser tools

- [x] `npx playwright test` succeeds (`2 passed`).
- [x] Cursor Playwright MCP (`.cursor/mcp.json`) starts in WSL2 and opens `http://localhost:8888`.
- [x] Visual baseline snapshot `wp-env-admin-smoke.png` generated and stored in `.auth/` / test directory.

## REST tools

- [x] Copied `.env.example` to `.env` and added Application Password credentials.
- [x] `npm run test:rest` executes Bruno `00 Smoke` and `01 Auth` collections cleanly (`200 OK`).
- [x] Report output is ignored and secrets are redacted from source control.

## Architecture spikes

- [x] Pinned Mermaid (`11.4.1`) and recorded static Live Editor SPA adapter strategy (ADR-014, ADR-015).
- [x] Proved static Svelte admin asset output and CSP behavior (ADR-015).
- [x] Verified `@wordpress/components` + `@wordpress/element` React 18 compatibility (ADR-016).
- [x] Proved browser/worker Mermaid parse parity and validation concept (ADR-014).
- [x] Proved controlled sanitized SVG featured-image behavior via PHPUnit (ADR-017).
- [x] Proved Playwright visual baseline stability across test runs (Task 5).

The environment phase is fully complete with all tests passing and ADR-014 through ADR-018 committed.
