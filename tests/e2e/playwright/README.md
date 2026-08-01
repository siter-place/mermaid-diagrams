# Playwright Test Scaffold

Playwright CLI is the committed browser-test authority. Playwright MCP in Cursor is an exploratory aid only.

## Layout

- `setup/auth.setup.ts` creates ignored administrator storage state.
- `tests/smoke.spec.ts` is active in Phase 00.
- Other `*.spec.ts` files are advance contracts and start skipped; each implementation phase activates and completes its own file.
- `__screenshots__/` holds reviewed Chromium/WSL2 baselines.

## Install and run in WSL2

```bash
npm install
npx playwright install --with-deps chromium
npm run env:start
npm run test:e2e
npm run test:e2e:ui
```

## Visual baseline profile

WordPress 7.0, PHP 8.3, wp-env/Docker Desktop WSL2 backend, Ubuntu WSL filesystem, pinned Chromium, 1440×1000 viewport, 1x device scale, `en-US`, Europe/Belgrade timezone, light scheme, reduced motion.

Feature screenshots must wait for `data-mdm-render-state="ready"`, disable plugin animation, mask nondeterministic UI, and target a stable region. Snapshot changes require explicit `--update-snapshots` and review.

## Test isolation

Create fixtures through REST/WP-CLI. Use unique test run IDs and cleanup. Never depend on manually created content or test ordering. Secrets and browser profiles stay in `.env`/`.auth`, both ignored.
