# Version Management and Release Logging

Status: **authoritative development workflow**  
Runtime: **Node.js 24.18.1**

## 1. Goal

A version change is one coordinated operation. The plugin version, active source references, changelog, and decision log must never be updated independently.

The target release version is entered locally in `.env`:

```dotenv
TARGET_VERSION=1.3.0
TARGET_VERSION_DATE=2026-08-15
TARGET_VERSION_CHANGELOG=Summarize the release-level change.
TARGET_VERSION_DECISION=Explain why this version was approved.
```

Only `TARGET_VERSION` is required. `.env` remains uncommitted because it may also contain local credentials. `.env.example` documents the expected fields and carries a safe example value.

## 2. Commands

```bash
cp .env.example .env
# Change TARGET_VERSION in .env.

npm run update-version:dry-run
npm run update-version
npm run test:versioning
```

Optional direct CLI usage:

```bash
node scripts/increase-plugin-version.mjs --help
node scripts/increase-plugin-version.mjs --dry-run
node scripts/increase-plugin-version.mjs --date 2026-08-15
```

If `TARGET_VERSION` matches the current version, the script handles it safely and idempotently, reporting that the target version is already implemented and exiting cleanly.
A downgrade is rejected. `--allow-downgrade` exists only for an explicitly reviewed recovery operation.

## 3. Source of current and target versions

- **Current version:** root `package.json` top-level `version`.
- **Target version:** `.env` `TARGET_VERSION`.
- **Release date:** `TARGET_VERSION_DATE`, CLI `--date`, or the current ISO date.

The target must be a valid Semantic Version and greater than or equal to the current version by default. When PHP CLI is available, the script also requires PHP `version_compare()` to consider the target greater or equal because WordPress uses PHP ordering for plugin updates.

## 4. Files updated

### Structured metadata

The script updates only project-owned fields:

- `package.json` top-level `version`;
- `package-lock.json` top-level `version` and `packages[""]` root version;
- `npm-shrinkwrap.json` equivalent root fields when present;
- `composer.json` top-level `version` when the project chooses to declare it.

Dependency versions are never globally replaced inside npm or Composer lock data, even when a dependency happens to use the same number as the plugin.

### Active text files

The script scans text files and replaces the exact previous plugin version. This covers common WordPress locations such as:

```php
/**
 * Version: 1.3.0
 */

define( 'MDM_VERSION', '1.3.0' );
```

```text
Stable tag: 1.3.0
```

It also updates matching active references in PHP, JavaScript, TypeScript, CSS, Markdown, XML, YAML, `block.json`, build configuration, and `.env.example`.

Generated output, dependencies, VCS data, reports, archives, images, fonts, PDFs, and files larger than 5 MiB are excluded.

### Historical records

`CHANGELOG.md` and `docs/decision-log.md` are excluded from global replacement because old version numbers there are historical facts.

Instead, the script:

1. creates `## [TARGET_VERSION] - YYYY-MM-DD` in `CHANGELOG.md`;
2. moves the current `Unreleased` content under that release;
3. resets `Unreleased` to an empty section;
4. adds `REL-TARGET_VERSION` to `docs/decision-log.md`;
5. records the previous version, new version, date, command, decision, and changelog link.

## 5. Safety model

- Exact previous-version replacement; no “replace every version-looking string” behavior.
- Idempotent execution: if `TARGET_VERSION` is already implemented, the script exits cleanly without duplicating entries.
- Binary detection and size limits.
- Structured JSON updates for package metadata.
- Dry-run preview.
- Temporary-file writes and rollback after a failed commit.
- Verification of `package.json`, changelog, and decision log after writing.
- WordPress/PHP ordering verification through `version_compare()` when PHP CLI is available.
- Duplicate release entries are rejected.
- Accidental downgrade requests are rejected.

Always review `git diff` after a successful run and before committing.

## 6. Why this is a custom Node script

Research considered these existing tools:

### `release-it`

Repository: https://github.com/release-it/release-it

It is a strong release orchestrator for version bumps, Git commits/tags, hosted releases, changelog generation, and npm publication. It remains a possible later wrapper around the Mermaid Diagrams release process.

It is not the synchronizer used here because our required behavior is WordPress-specific: preserve dependency versions, update PHP plugin headers and constants, move `Unreleased` content, and create a release-linked decision record. Adding release orchestration before Git/tag/package policy is finalized would also make the Phase 13 workflow harder to audit.

### `replace-in-file`

Repository: https://github.com/adamreisnz/replace-in-file

It is useful for generic text replacement over globs, but it does not understand root package fields, npm lockfile root metadata, WordPress headers, historical release records, or release decisions. A blind replacement would risk changing dependency versions and historical changelog entries.

### Node `semver`

Repository: https://github.com/npm/node-semver

This is the mature SemVer implementation used in the npm ecosystem. The current script requires only exact SemVer validation and ordering and includes a focused implementation with tests. If future release requirements add complex ranges, coercion, or advanced prerelease policies, replace the local comparator with `semver` as a development dependency rather than expanding custom parsing.

## 7. Why no additional runtime dependency is required

Node.js 24.18.1 provides the required primitives directly:

- stable `fs.promises.glob()` for project scanning;
- stable `util.parseEnv()` for reading `.env` without a dotenv package;
- stable `util.parseArgs()` for CLI handling;
- built-in `node:test` for the fixture suite.

This keeps the release script executable after `npm install` problems and avoids giving a generic release package authority over WordPress/PHP files.

WordPress-specific references:

- https://developer.wordpress.org/plugins/plugin-basics/header-requirements/
- https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/

Node.js references:

- https://nodejs.org/docs/latest-v24.x/api/fs.html#fspromisesglobpattern-options
- https://nodejs.org/docs/latest-v24.x/api/util.html#utilparseenvcontent
- https://nodejs.org/docs/latest-v24.x/api/util.html#utilparseargsconfig
- https://nodejs.org/docs/latest-v24.x/api/test.html

## 8. Required release sequence

1. Finish and test the release scope.
2. Write meaningful content under `CHANGELOG.md` → `Unreleased`.
3. Set the new `TARGET_VERSION` in local `.env`.
4. Optionally set `TARGET_VERSION_DATE`, `TARGET_VERSION_CHANGELOG`, and `TARGET_VERSION_DECISION`.
5. Run `npm run update-version:dry-run`.
6. Review the file list and occurrence counts.
7. Run `npm run update-version`.
8. Run `npm run test:versioning` and the full release test matrix.
9. Review `git diff`.
10. Commit the synchronized version, changelog, and decision log together.
11. Only after those checks, create a Git tag and release package.

## 9. Automated fixture coverage

`tests/fixtures/versioning/sample-plugin` models a small WordPress/PHP plugin with:

- plugin header;
- PHP version constant;
- WordPress.org `Stable tag`;
- npm package and lockfile;
- Composer metadata;
- active documentation;
- changelog history;
- decision history;
- a dependency whose version deliberately matches the old plugin version.

`tests/node/versioning/version-sync.test.mjs` verifies updates, dependency preservation, history preservation, dry runs, invalid versions, already-implemented version handling, and downgrade rejection.
