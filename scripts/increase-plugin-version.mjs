#!/usr/bin/env node
import process from 'node:process';
import { parseArgs } from 'node:util';
import { synchronizeVersion } from './lib/version-sync.mjs';

const HELP = `
Usage: npm run update-version -- [options]

Reads TARGET_VERSION from .env, synchronizes the active plugin version across project
files, creates a CHANGELOG.md release entry, and records the release in
 docs/decision-log.md.

Options:
  --root <path>          Project root. Defaults to the current directory.
  --env <path>           Environment file relative to root. Default: .env
  --date <YYYY-MM-DD>    Override the release date.
  --dry-run              Show planned changes without writing files.
  --allow-downgrade      Permit a lower target version for recovery only.
  --help                 Show this help.
`;

const { values } = parseArgs({
  options: {
    root: { type: 'string', default: process.cwd() },
    env: { type: 'string', default: '.env' },
    date: { type: 'string' },
    'dry-run': { type: 'boolean', default: false },
    'allow-downgrade': { type: 'boolean', default: false },
    help: { type: 'boolean', default: false },
  },
  allowPositionals: false,
  strict: true,
});

if (values.help) {
  console.log(HELP.trim());
  process.exit(0);
}

try {
  const summary = await synchronizeVersion({
    root: values.root,
    envFile: values.env,
    date: values.date,
    dryRun: values['dry-run'],
    allowDowngrade: values['allow-downgrade'],
  });

  if (summary.alreadyImplemented) {
    console.log(`Target version ${summary.targetVersion} is already implemented in ${summary.currentVersion}. No version changes needed.`);
    process.exit(0);
  }

  console.log(`${summary.dryRun ? 'Planned' : 'Updated'} Mermaid Diagrams version ${summary.currentVersion} -> ${summary.targetVersion}`);
  console.log(`Release date: ${summary.date}`);
  console.log(`Scanned text files: ${summary.scannedFiles}`);
  const phpOrdering = summary.phpVersionOrdering.checked
    ? 'verified'
    : `not checked (${summary.phpVersionOrdering.reason})`;
  console.log(`PHP version ordering: ${phpOrdering}`);
  console.log(`Changed files: ${summary.files.length}`);
  for (const file of summary.files) {
    const suffix = file.occurrences > 0 ? ` (${file.occurrences} occurrence${file.occurrences === 1 ? '' : 's'})` : '';
    console.log(`  - ${file.path} [${file.kind}]${suffix}`);
  }
  if (summary.skippedFiles.length > 0) {
    console.log(`Skipped non-text/large files: ${summary.skippedFiles.length}`);
  }
  if (summary.dryRun) {
    console.log('Dry run completed; no files were written.');
  }
} catch (error) {
  console.error(`Version update failed: ${error.message}`);
  process.exitCode = 1;
}
