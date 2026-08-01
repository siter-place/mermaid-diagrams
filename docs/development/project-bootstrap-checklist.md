# Project Bootstrap Checklist

Use this after extracting the scaffold into the WSL Linux filesystem.

## Host

- [ ] Docker Desktop uses the WSL2 backend and Ubuntu integration is enabled.
- [ ] Repository is under `~/projects/mermaid-diagrams`, not `/mnt/c`.
- [ ] Cursor opens the WSL folder and its terminal resolves Linux Node/npm/Docker.
- [ ] Node version satisfies `package.json`; npm and Docker Compose work.

## Repository dependencies

- [ ] Run `npm install` and commit the generated lockfile after Phase 00 pins versions.
- [ ] Install Chromium with `npx playwright install --with-deps chromium`.
- [ ] Run Composer install/setup selected by Phase 00 and commit `composer.lock`.
- [ ] Install WordPress and Bruno Agent Skills at project scope; record source commits.

## wp-env

- [ ] Run `npm run env:start`.
- [ ] Confirm WordPress 7.0, PHP 8.3, site URL, and plugin mounts.
- [ ] Confirm MCP Adapter and OpenAI provider plugins are present.
- [ ] Configure a local OpenAI connector/key outside Git only when manual AI testing is needed.
- [ ] Create the dedicated Bruno test user and Application Password.

## Browser tools

- [ ] `npx playwright test --list` succeeds.
- [ ] Cursor Playwright MCP starts in WSL and opens `http://localhost:8888`.
- [ ] Authentication state is stored only under ignored test directories.

## REST tools

- [ ] Copy `.env.example` to `.env` and add local Bruno credentials.
- [ ] `npx bru run bruno/00\ Smoke --env Local` succeeds after collection validation.
- [ ] JSON/JUnit/HTML report output is ignored and secrets are redacted.

## Architecture spikes

- [ ] Pin Mermaid and Mermaid Live Editor versions/commits/licenses.
- [ ] Prove static Svelte admin asset output and CSP behavior.
- [ ] Verify `plugin-ui` and WordPress 7 React compatibility behind adapters.
- [ ] Prove browser/worker Mermaid parity and validation receipt signing/trust model.
- [ ] Prove controlled dual-sanitized SVG featured-image behavior.
- [ ] Prove Playwright visual baseline stability after two clean environment rebuilds.

The environment phase is complete only when its tests/acceptance document contains command output/evidence and all unresolved risks have an ADR or explicit blocker.
