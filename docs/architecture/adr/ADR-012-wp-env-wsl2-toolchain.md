# ADR-012: wp-env on WSL2 as the Reference Development Environment

**Status:** Accepted

## Decision

Use wp-env with Docker Desktop's WSL2 backend, Ubuntu-hosted project files, WordPress 7.0, and PHP 8.3. Playwright CLI and Bruno CLI run inside WSL against wp-env. Cursor uses Playwright MCP for interactive browser work.

## Consequences

- Visual baselines are tied to a documented WSL/Chromium profile.
- Lifecycle scripts must be idempotent.
- Machine-specific overrides and secrets are ignored.
- A separate `.wp-env.test.json` covers trunk/forward-compatibility checks.
