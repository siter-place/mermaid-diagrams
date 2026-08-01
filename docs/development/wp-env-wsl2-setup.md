# wp-env Development Environment on WSL2, Docker Desktop, and Cursor

This is Phase 00's first deliverable.

## 1. Host assumptions

- Windows 11 with WSL2 enabled.
- Ubuntu distribution running under WSL2.
- Docker Desktop configured to use the WSL2 backend and integrated with the Ubuntu distribution.
- Project files stored in the Linux filesystem, for example `~/projects/mermaid-diagrams`, not under `/mnt/c`, to avoid slow bind mounts and file-watch problems.
- Cursor opened against the WSL folder through its WSL/remote integration.
- Node.js version compatible with the pinned Mermaid Live Editor toolchain. At research time its package required Node 24.16 or newer, so the project baseline is Node 24 LTS-compatible.
- PHP does not need to be installed on the host for normal wp-env use, but Composer on the host can improve editor integration. Composer, PHPUnit, and WP-CLI are available inside wp-env containers.

## 2. Install and verify prerequisites

Inside Ubuntu:

```bash
node --version
npm --version
docker version
docker compose version
```

The Docker server output must be present. A client-only result means Docker Desktop integration is not enabled for this distribution.

## 3. Clone/open the project

```bash
mkdir -p ~/projects
cd ~/projects
# Place or clone the repository as ~/projects/mermaid-diagrams
cd mermaid-diagrams
cursor .
```

Do not run Node installs once from Windows and once from Linux. `node_modules` belongs to the WSL environment.

## 4. Install project dependencies

```bash
npm install
```

Phase 00 must commit the resulting lockfile. Reference tools and Agent Skills are installed separately; their source is not copied into this documentation package.

## 5. Understand `.wp-env.json`

The supplied configuration:

- maps the repository itself as an active plugin using `"plugins": ["."]`;
- pins WordPress 7.0 for the minimum-supported development profile;
- pins PHP 8.3;
- installs the official MCP Adapter release ZIP and the stable WordPress.org OpenAI provider plugin for local integration work;
- enables debug/script-debug and plugin development mode;
- exposes WordPress on `http://localhost:8888` and phpMyAdmin on `8890`;
- runs an idempotent `afterStart` lifecycle script.

wp-env lifecycle scripts run for both new and existing environments, so setup scripts must be repeatable. Local changes belong in `.wp-env.override.json`, which is ignored by Git.

## 6. Start and inspect

```bash
npm run env:start
npx wp-env install-path
npx wp-env run cli wp core version
npx wp-env run cli wp plugin list
npx wp-env run cli wp option get siteurl
```

Expected site: `http://localhost:8888`  
Default wp-env credentials: `admin` / `password` for local use only.

## 7. Container commands

```bash
npm run env:logs
npx wp-env run cli bash
npx wp-env run cli wp shell
npx wp-env run cli composer install
npx wp-env run cli phpunit
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

## 11. WSL2 troubleshooting

- Slow installs/watchers: move the repository from `/mnt/c/...` into `~/projects/...`.
- Docker permission errors: verify Docker Desktop WSL integration rather than adding broad socket permissions.
- Ports occupied: use a user-local override or `--auto-port`; CI uses fixed ports.
- Browser cannot reach site: verify `wp-env` port output and Windows firewall/VPN interference.
- File ownership: never run project npm commands with `sudo`.
- Stale generated files: stop wp-env before moving/renaming the repository.

## 12. Environment acceptance

- WordPress 7.0 runs on PHP 8.3.
- Mermaid Diagrams appears and activates without warnings, once its bootstrap exists.
- MCP Adapter and the OpenAI provider are installed; the OpenAI key is supplied through WordPress Connectors or a local environment/constant and is never committed.
- WP-CLI works inside `cli`.
- Cursor can edit files and receive filesystem changes immediately.
- Playwright can open the admin login from WSL.
- Bruno can reach the REST root using a dedicated Application Password.
