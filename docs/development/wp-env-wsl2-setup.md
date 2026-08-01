# wp-env Development Environment on WSL2, Docker Desktop, and Cursor

This is Phase 00's primary development environment setup deliverable.

## 1. Host assumptions

- Windows 11 with WSL2 enabled.
- Ubuntu distribution running under WSL2.
- Docker Desktop configured to use the WSL2 backend and integrated with the Ubuntu distribution.
- Project files stored in the Linux filesystem, for example `/home/black/workspace/wp-plugins-development/mermaid-diagrams`, not under `/mnt/c`, to avoid slow bind mounts and file-watch problems.
- Cursor opened against the WSL folder through its WSL/remote integration.
- Node.js version v24.18.1 (satisfies `>=24.16.0`).
- PHP 8.3.33 on host and inside wp-env containers. Composer, PHPUnit, and WP-CLI available inside wp-env containers.

## 2. Install and verify prerequisites

Inside Ubuntu:

```bash
node --version # v24.18.1
npm --version  # 11.x
docker version
docker compose version
php -v        # PHP 8.3.33
composer --version
```

The Docker server output must be present. A client-only result means Docker Desktop integration is not enabled for this distribution.

## 3. Clone/open the project

```bash
cd /home/black/workspace/wp-plugins-development/mermaid-diagrams
cursor .
```

Do not run Node installs once from Windows and once from Linux. `node_modules` belongs to the WSL environment.

## 4. Install project dependencies

```bash
npm install
composer update
```

Phase 00 pins exact versions for all devDependencies and PHP packages.

## 5. `.wp-env.json` Configuration

The supplied configuration:

- maps the repository itself as an active plugin using `"plugins": ["."]`;
- pins WordPress 7.0 for the minimum-supported development profile;
- pins PHP 8.3;
- sets `"testsEnvironment": false` to prevent port conflicts on phpMyAdmin (port 8890);
- installs the official MCP Adapter release ZIP (0.5.0), OpenAI provider plugin (1.0.3), and Secure Custom Fields (6.9.3);
- enables debug/script-debug and plugin development mode;
- exposes WordPress on `http://localhost:8888` and phpMyAdmin on `8890`;
- runs an idempotent `afterStart` lifecycle script (`node tools/wp-env/after-start.mjs`).

wp-env lifecycle scripts run for both new and existing environments, so setup scripts must be repeatable. Local changes belong in `.wp-env.override.json`, which is ignored by Git.

## 6. Start and inspect

```bash
npm run env:start
npx wp-env run cli wp core version
npx wp-env run cli wp plugin list
npx wp-env run cli wp option get siteurl
```

Expected site: `http://localhost:8888`  
Default wp-env credentials: `admin` / `password` for local use only.
Bruno test user: `mdm_api_test` / Application Password stored in `.env`.

## 7. Container commands

```bash
npm run env:logs
npx wp-env run cli bash
npx wp-env run cli wp shell
npx wp-env run cli --env-cwd=wp-content/plugins/mermaid-diagrams vendor/bin/phpunit
```

Use `wp-env run cli --env-cwd=wp-content/plugins/mermaid-diagrams ...` when a command must run from the mounted plugin directory.

## 8. Separate trunk/test profile

Run a second configuration when validating upcoming WordPress changes:

```bash
npx wp-env start --config=.wp-env.test.json
```

Do not rely on the deprecated combined tests environment. Current wp-env guidance favors separate config files for parallel environments.

## 9. Xdebug and profiling

```bash
npx wp-env start --xdebug=debug
# or
npx wp-env start --spx
```

Cursor's PHP debugger should map the container plugin path to the WSL workspace. Establish the exact path after `wp-env install-path` and document it in a user-local launch configuration.

## 10. Reset policy

```bash
npm run env:stop       # preserve containers/data
npm run env:reset      # reset databases
npm run env:clean      # remove generated environment files/data
npm run env:destroy    # remove environment completely
```

Tests must create their own fixtures and never depend on manually created local content.

## 11. WSL2-Specific Findings & Troubleshooting

- **Node.js Subshell Pathing**: In non-interactive WSL subshells, prepend `export PATH="$HOME/.nvm/versions/node/v24.18.1/bin:$PATH"` if nvm version reverts to system default.
- **REST Application Password Header**: Ensure `HTTP_AUTHORIZATION` is forwarded from `REDIRECT_HTTP_AUTHORIZATION` in `wp-config.php` / `.htaccess` so Apache does not strip HTTP Basic Auth headers sent by Bruno or API clients.
- **Slow installs/watchers**: Keep repository in `/home/black/workspace/...`, not `/mnt/c/...`.
- **Docker permission errors**: Verify Docker Desktop WSL integration rather than adding broad socket permissions.
- **Ports occupied**: `"testsEnvironment": false` in `.wp-env.json` prevents duplicate phpMyAdmin port 8890 binding errors.

## 12. Environment acceptance

- WordPress 7.0 runs on PHP 8.3.
- Mermaid Diagrams appears and activates without warnings.
- MCP Adapter, OpenAI provider, and Secure Custom Fields plugins are active.
- WP-CLI works inside `cli`.
- Cursor can edit files and receive filesystem changes immediately.
- Playwright can open the admin login from WSL and take visual snapshot baselines (`2 passed`).
- Bruno can reach the REST root and authenticated current user endpoints using a dedicated Application Password (`2 passed`).
