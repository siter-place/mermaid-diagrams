---
name: versioning
description: "Automatically calculate and synchronize plugin version increments across project files, package metadata, WordPress headers, constants, changelog, and decision log using scripts/increase-plugin-version.mjs."
compatibility: "Requires Node.js 24.16.0+, npm 11+, and optional PHP CLI for version_compare()."
---

# Versioning Skill — Mermaid Diagrams

## When to use

Trigger this skill whenever:
- A new implementation phase or feature release is finalized.
- Incremental plugin version bumps are required.
- `.env` needs to be populated with target release metadata (`TARGET_VERSION`, `TARGET_VERSION_DATE`, `TARGET_VERSION_CHANGELOG`, `TARGET_VERSION_DECISION`).
- Synchronizing version numbers across WordPress main header, PHP constants, package manifests, lockfiles, docs, `CHANGELOG.md`, and `docs/decision-log.md`.

## Agent Automated Execution Procedure

1. **Calculate Target Version:**
   - Read current version from `package.json` (or `mermaid-diagrams.php`).
   - Determine increment type:
     - **Minor bump** (e.g. `1.0.0` → `1.1.0`): Completed standard implementation phase or major feature addition.
     - **Patch bump** (e.g. `1.0.0` → `1.0.1`): Maintenance fixes, refactoring, or incremental iteration within a phase.
     - **Major bump** (e.g. `1.x.x` → `2.0.0`): Breaking architectural shift or major product baseline release.

2. **Configure `.env` Metadata:**
   Write/update `.env` with the target release metadata:
   ```dotenv
   TARGET_VERSION=1.x.x
   TARGET_VERSION_DATE=YYYY-MM-DD
   TARGET_VERSION_CHANGELOG=Summary of changes for the changelog release entry.
   TARGET_VERSION_DECISION=Reason and approval rationale for the decision log.
   ```

3. **Run Dry Run Verification:**
   Execute dry-run to inspect affected files and planned occurrences without writing:
   ```bash
   npm run update-version:dry-run
   ```

4. **Execute Version Synchronization:**
   Execute the synchronization script to update package manifests, headers, constants, docs, `CHANGELOG.md`, and `docs/decision-log.md`:
   ```bash
   npm run update-version
   ```
   *Note:* If `TARGET_VERSION` is already implemented, the script detects this idempotently, reports `alreadyImplemented`, and completes safely without duplicating log entries or making unnecessary file edits.

5. **Automated Verification:**
   Run the versioning test suite:
   ```bash
   npm run test:versioning
   ```

## Rules & Safety Constraints

- **SemVer Strictness:** Target version must be valid Semantic Versioning (`X.Y.Z`) and higher than or equal to the current version in `package.json`. Downgrades are rejected unless `--allow-downgrade` is explicitly passed.
- **PHP Version Ordering:** When PHP CLI is available, target version must pass PHP `version_compare()` check.
- **Dependency Preservation:** Never replace third-party dependency version numbers, even if they match the plugin version.
- **Log Linkage:** The script moves `Unreleased` content in `CHANGELOG.md` to `## [VERSION] - YYYY-MM-DD` and creates `REL-VERSION` in `docs/decision-log.md`.
- **Atomic Operations:** Uses temporary file writes and rollback on failure.

## Verification Checklist

- [ ] Target version calculated and `.env` updated with `TARGET_VERSION`.
- [ ] `package.json` top-level `version` matches target.
- [ ] `package-lock.json` root package version matches target.
- [ ] `mermaid-diagrams.php` header `Version:` and `MDM_VERSION` constant match target.
- [ ] `CHANGELOG.md` contains release header `## [VERSION] - YYYY-MM-DD`.
- [ ] `docs/decision-log.md` contains entry `REL-VERSION`.
- [ ] `npm run test:versioning` passes without errors.
