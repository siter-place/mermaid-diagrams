# Project scripts

## Plugin version synchronization

`increase-plugin-version.mjs` is the release-version synchronizer for Mermaid Diagrams.

```bash
cp .env.example .env
# Edit VERSION in .env.
npm run update-version:dry-run
npm run update-version
npm run test:versioning
```

The script intentionally uses the correctly spelled filename `increase-plugin-version.mjs`. It:

- reads the target `VERSION` from `.env`;
- reads the current version from the root `package.json`;
- requires a valid SemVer increase unless `--allow-downgrade` is explicitly supplied;
- updates only root npm/Composer package metadata structurally;
- scans eligible text files and replaces the exact previous plugin version;
- updates WordPress plugin headers, constants, `readme.txt`, source files, and active documentation when they contain that exact version;
- preserves dependency versions and release-history entries;
- moves the current `Unreleased` changelog body into the new release entry;
- creates a version-linked release decision in `docs/decision-log.md`;
- supports `--dry-run`, transactional rollback, and post-write verification.

Run `node scripts/increase-plugin-version.mjs --help` for all options.
