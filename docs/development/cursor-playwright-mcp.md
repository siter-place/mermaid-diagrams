# Cursor, Playwright CLI, and Playwright MCP

## Roles

- **Playwright Test CLI** is the committed, deterministic test runner for functional browser tests and visual regression.
- **Playwright MCP** is an interactive development aid for Cursor. It helps the coding agent inspect the running wp-env site, reproduce issues, and develop selectors, but it does not replace committed tests.

## Local CLI installation

The project installs `@playwright/test` as a development dependency. In WSL2:

```bash
npm install
npx playwright install --with-deps chromium
npx playwright test --list
```

Chromium is the required CI browser. Firefox/WebKit may be added later for compatibility coverage, but visual baselines use one pinned browser/OS profile.

## Cursor MCP configuration

Install the official Microsoft Playwright MCP server in Cursor according to its current documentation. Configure it to:

- execute inside WSL, not Windows;
- use the repository as working directory;
- open `http://localhost:8888`;
- use a persistent local browser profile only for exploratory work;
- never commit profile cookies or connector secrets.

Keep the MCP configuration in a user-local file when it contains machine-specific paths. A sanitized project example may be committed under `.cursor/` during Phase 00.

Use the normal browser/navigation/snapshot tools first. Do not enable or invoke Playwright MCP's unsafe arbitrary-code execution capability in this project unless a human explicitly approves a trusted local debugging session; it is equivalent to granting the agent code execution in the browser host context.

## Visual regression policy

- Baselines are produced only on the documented WSL2/Docker/Chromium profile.
- Lock viewport, device scale factor, locale, timezone, reduced-motion preference, WordPress theme, Mermaid theme, test data, and fonts.
- Disable animations/transitions for snapshots.
- Wait for a plugin-emitted `data-mdm-render-state="ready"` marker, not arbitrary timeouts.
- Mask timestamps, nonce values, avatars, and other nondeterministic regions.
- Prefer component/region snapshots over full-page snapshots.
- Updating snapshots requires an explicit command and human review of the diff.

## Authentication

Use a Playwright setup project to log into WordPress and store `storageState` under ignored `.auth/`. Never hard-code a production credential. The default wp-env account is acceptable only locally.

## Required commands

```bash
npx playwright test
npx playwright test --ui
npx playwright test --debug
npx playwright show-report
npx playwright test --update-snapshots
```

## MCP-to-test workflow

1. Reproduce or explore with Playwright MCP.
2. Identify semantic role/name or a stable `data-testid` owned by this plugin.
3. Add a failing Playwright Test.
4. Implement the change.
5. Run the individual test, then the suite.
6. Add/update visual evidence only when the UI intentionally changes.
7. Keep MCP transcripts out of the repository unless converted into a concise issue/ADR.
