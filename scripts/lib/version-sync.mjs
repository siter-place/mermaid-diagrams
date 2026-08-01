import { access, chmod, readFile, rename, rm, stat, writeFile } from 'node:fs/promises';
import { glob } from 'node:fs/promises';
import { dirname, join, relative, resolve } from 'node:path';
import { parseEnv } from 'node:util';
import { spawnSync } from 'node:child_process';

const SEMVER_PATTERN = /^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-((?:0|[1-9]\d*|\d*[A-Za-z-][0-9A-Za-z-]*)(?:\.(?:0|[1-9]\d*|\d*[A-Za-z-][0-9A-Za-z-]*))*))?(?:\+([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?$/;

const DEFAULT_EXCLUDES = [
  '.git/**',
  '.svn/**',
  '.hg/**',
  'node_modules/**',
  'vendor/**',
  'build/**',
  'dist/**',
  'coverage/**',
  'playwright-report/**',
  'test-results/**',
  'bruno/reports/**',
  '.wp-env/**',
  '**/*.zip',
  '**/*.tar',
  '**/*.tar.gz',
  '**/*.tgz',
  '**/*.7z',
  '**/*.png',
  '**/*.jpg',
  '**/*.jpeg',
  '**/*.gif',
  '**/*.webp',
  '**/*.ico',
  '**/*.woff',
  '**/*.woff2',
  '**/*.ttf',
  '**/*.eot',
  '**/*.pdf',
];

const STRUCTURED_FILES = new Set([
  'package.json',
  'package-lock.json',
  'npm-shrinkwrap.json',
  'composer.json',
]);

const HISTORY_FILES = new Set([
  'CHANGELOG.md',
  'docs/decision-log.md',
]);

const MAX_TEXT_FILE_BYTES = 5 * 1024 * 1024;

export function parseSemver(value) {
  const match = SEMVER_PATTERN.exec(value);
  if (!match) {
    throw new Error(`Invalid semantic version: ${value}`);
  }

  return {
    raw: value,
    major: Number(match[1]),
    minor: Number(match[2]),
    patch: Number(match[3]),
    prerelease: match[4] ? match[4].split('.') : [],
    build: match[5] ? match[5].split('.') : [],
  };
}

function compareIdentifier(left, right) {
  const leftNumeric = /^\d+$/.test(left);
  const rightNumeric = /^\d+$/.test(right);

  if (leftNumeric && rightNumeric) {
    return Number(left) - Number(right);
  }
  if (leftNumeric) {
    return -1;
  }
  if (rightNumeric) {
    return 1;
  }
  return left.localeCompare(right);
}

export function compareSemver(leftValue, rightValue) {
  const left = parseSemver(leftValue);
  const right = parseSemver(rightValue);

  for (const key of ['major', 'minor', 'patch']) {
    if (left[key] !== right[key]) {
      return left[key] - right[key];
    }
  }

  if (left.prerelease.length === 0 && right.prerelease.length === 0) {
    return 0;
  }
  if (left.prerelease.length === 0) {
    return 1;
  }
  if (right.prerelease.length === 0) {
    return -1;
  }

  const maxLength = Math.max(left.prerelease.length, right.prerelease.length);
  for (let index = 0; index < maxLength; index += 1) {
    const leftPart = left.prerelease[index];
    const rightPart = right.prerelease[index];

    if (leftPart === undefined) {
      return -1;
    }
    if (rightPart === undefined) {
      return 1;
    }

    const comparison = compareIdentifier(leftPart, rightPart);
    if (comparison !== 0) {
      return comparison;
    }
  }

  return 0;
}

function verifyPhpVersionOrdering(currentVersion, targetVersion) {
  const php = spawnSync(
    'php',
    [
      '-r',
      'exit(version_compare($argv[1], $argv[2], ">") ? 0 : 1);',
      targetVersion,
      currentVersion,
    ],
    { encoding: 'utf8' },
  );

  if (php.error?.code === 'ENOENT') {
    return { checked: false, reason: 'PHP CLI is not available on PATH.' };
  }
  if (php.error) {
    throw new Error(`Unable to run PHP version_compare(): ${php.error.message}`);
  }
  if (php.status !== 0) {
    throw new Error(
      `PHP version_compare() does not consider ${targetVersion} greater than ${currentVersion}. `
      + 'WordPress uses PHP ordering for plugin updates, so choose a compatible version.',
    );
  }

  return { checked: true };
}

function normalizePath(root, absolutePath) {
  return relative(root, absolutePath).split('\\').join('/');
}

async function fileExists(path) {
  try {
    await access(path);
    return true;
  } catch {
    return false;
  }
}

async function readJson(path) {
  const source = await readFile(path, 'utf8');
  try {
    return { source, value: JSON.parse(source) };
  } catch (error) {
    throw new Error(`Cannot parse JSON file ${path}: ${error.message}`);
  }
}

function stringifyJson(value, originalSource) {
  const indentationMatch = originalSource.match(/\n([ \t]+)"/);
  const indentation = indentationMatch ? indentationMatch[1] : '  ';
  const newline = originalSource.includes('\r\n') ? '\r\n' : '\n';
  return `${JSON.stringify(value, null, indentation).replaceAll('\n', newline)}${newline}`;
}

function countOccurrences(source, needle) {
  if (!needle) {
    return 0;
  }
  return source.split(needle).length - 1;
}

function isLikelyBinary(buffer) {
  const sampleLength = Math.min(buffer.length, 8192);
  for (let index = 0; index < sampleLength; index += 1) {
    if (buffer[index] === 0) {
      return true;
    }
  }
  return false;
}

function todayIso() {
  return new Date().toISOString().slice(0, 10);
}

function normalizeReleaseBody(body, fallback) {
  const normalized = body.trim();
  return normalized.length > 0 ? normalized : fallback;
}

export function updateChangelog(source, { currentVersion, targetVersion, date, message }) {
  if (source.includes(`## [${targetVersion}]`)) {
    throw new Error(`CHANGELOG.md already contains version ${targetVersion}.`);
  }

  const fallbackBody = [
    '### Changed',
    '',
    `- ${message || `Synchronized the plugin version from \`${currentVersion}\` to \`${targetVersion}\` using \`npm run update-version\`.`}`,
  ].join('\n');

  const releaseHeading = `## [${targetVersion}] - ${date}`;
  const unreleasedHeading = '## [Unreleased]';

  if (!source.trim()) {
    return [
      '# Changelog',
      '',
      'All notable changes to this project are documented in this file.',
      '',
      unreleasedHeading,
      '',
      releaseHeading,
      '',
      fallbackBody,
      '',
    ].join('\n');
  }

  const unreleasedIndex = source.indexOf(unreleasedHeading);
  if (unreleasedIndex === -1) {
    const firstHeadingEnd = source.indexOf('\n');
    const insertionIndex = firstHeadingEnd === -1 ? source.length : firstHeadingEnd + 1;
    return `${source.slice(0, insertionIndex)}\n${unreleasedHeading}\n\n${releaseHeading}\n\n${fallbackBody}\n${source.slice(insertionIndex)}`;
  }

  const bodyStart = unreleasedIndex + unreleasedHeading.length;
  const remainder = source.slice(bodyStart);
  const nextReleaseMatch = remainder.match(/\n## \[(?!Unreleased\])[^\n]+/);
  const bodyEnd = nextReleaseMatch ? bodyStart + nextReleaseMatch.index : source.length;
  const unreleasedBody = source.slice(bodyStart, bodyEnd);
  const releaseBody = normalizeReleaseBody(unreleasedBody, fallbackBody);

  return [
    source.slice(0, bodyStart),
    '\n\n',
    releaseHeading,
    '\n\n',
    releaseBody,
    '\n',
    source.slice(bodyEnd).replace(/^\n+/, '\n'),
  ].join('');
}

export function updateDecisionLog(source, { currentVersion, targetVersion, date, decision }) {
  const releaseId = `REL-${targetVersion}`;
  if (source.includes(`## ${releaseId} `) || source.includes(`- Version: \`${targetVersion}\``)) {
    throw new Error(`docs/decision-log.md already contains version ${targetVersion}.`);
  }

  const entry = [
    `## ${releaseId} — Version ${targetVersion}`,
    '',
    `- Date: ${date}`,
    `- Version: \`${targetVersion}\``,
    `- Previous version: \`${currentVersion}\``,
    '- Status: Applied',
    '- Command: `npm run update-version`',
    `- Changelog: [${targetVersion}](../CHANGELOG.md#${targetVersion.replaceAll('.', '')}---${date})`,
    '',
    `**Decision:** ${decision || `Adopt \`${targetVersion}\` as the synchronized Mermaid Diagrams plugin version across active project metadata and documentation.`}`,
    '',
  ].join('\n');

  if (!source.trim()) {
    return [
      '# Decision Log',
      '',
      'This log records release-linked project decisions. Newest entries appear first.',
      '',
      '<!-- release-entries -->',
      '',
      entry,
    ].join('\n');
  }

  const marker = '<!-- release-entries -->';
  const markerIndex = source.indexOf(marker);
  if (markerIndex !== -1) {
    const insertionIndex = markerIndex + marker.length;
    return `${source.slice(0, insertionIndex)}\n\n${entry}${source.slice(insertionIndex).replace(/^\n+/, '\n')}`;
  }

  const firstLineEnd = source.indexOf('\n');
  const insertionIndex = firstLineEnd === -1 ? source.length : firstLineEnd + 1;
  return `${source.slice(0, insertionIndex)}\n${entry}${source.slice(insertionIndex)}`;
}

function addChange(changes, absolutePath, before, after, metadata = {}) {
  if (before === after) {
    return;
  }
  changes.set(absolutePath, {
    before,
    after,
    occurrences: metadata.occurrences ?? 0,
    kind: metadata.kind ?? 'text',
  });
}

async function prepareStructuredChanges(root, currentVersion, targetVersion, changes) {
  const packagePath = join(root, 'package.json');
  if (!(await fileExists(packagePath))) {
    throw new Error(`package.json is required at ${packagePath}.`);
  }

  const packageJson = await readJson(packagePath);
  if (typeof packageJson.value.version !== 'string') {
    throw new Error('package.json must contain a string version field.');
  }
  if (packageJson.value.version !== currentVersion) {
    throw new Error(`package.json version changed during execution: expected ${currentVersion}, found ${packageJson.value.version}.`);
  }
  packageJson.value.version = targetVersion;
  addChange(changes, packagePath, packageJson.source, stringifyJson(packageJson.value, packageJson.source), {
    kind: 'package-json',
    occurrences: 1,
  });

  for (const filename of ['package-lock.json', 'npm-shrinkwrap.json']) {
    const path = join(root, filename);
    if (!(await fileExists(path))) {
      continue;
    }

    const lock = await readJson(path);
    let updated = false;
    if (lock.value.version === currentVersion) {
      lock.value.version = targetVersion;
      updated = true;
    }
    if (lock.value.packages?.['']?.version === currentVersion) {
      lock.value.packages[''].version = targetVersion;
      updated = true;
    }
    if (updated) {
      addChange(changes, path, lock.source, stringifyJson(lock.value, lock.source), {
        kind: 'npm-lock-root',
        occurrences: 1,
      });
    }
  }

  const composerPath = join(root, 'composer.json');
  if (await fileExists(composerPath)) {
    const composer = await readJson(composerPath);
    if (composer.value.version !== undefined && composer.value.version !== currentVersion) {
      throw new Error(`composer.json version ${composer.value.version} does not match package.json version ${currentVersion}.`);
    }
    if (composer.value.version === currentVersion) {
      composer.value.version = targetVersion;
      addChange(changes, composerPath, composer.source, stringifyJson(composer.value, composer.source), {
        kind: 'composer-json',
        occurrences: 1,
      });
    }
  }
}

async function prepareTextChanges(root, currentVersion, targetVersion, changes) {
  const scanned = [];
  const skipped = [];
  const seen = new Set();

  for await (const entry of glob(['**/*', '.*', '**/.*'], {
    cwd: root,
    exclude: DEFAULT_EXCLUDES,
    followSymlinks: false,
    withFileTypes: true,
  })) {
    if (!entry.isFile()) {
      continue;
    }

    const absolutePath = resolve(entry.parentPath ?? dirname(join(root, entry.name)), entry.name);
    const projectPath = normalizePath(root, absolutePath);
    if (seen.has(projectPath)) {
      continue;
    }
    seen.add(projectPath);

    if (
      STRUCTURED_FILES.has(projectPath)
      || HISTORY_FILES.has(projectPath)
      || projectPath === '.env'
      || projectPath.endsWith('/.env')
    ) {
      continue;
    }

    const fileStat = await stat(absolutePath);
    if (fileStat.size > MAX_TEXT_FILE_BYTES) {
      skipped.push({ path: projectPath, reason: 'larger than 5 MiB' });
      continue;
    }

    const buffer = await readFile(absolutePath);
    if (isLikelyBinary(buffer)) {
      skipped.push({ path: projectPath, reason: 'binary content' });
      continue;
    }

    const source = buffer.toString('utf8');
    const occurrences = countOccurrences(source, currentVersion);
    scanned.push(projectPath);

    if (occurrences === 0) {
      continue;
    }

    const after = source.replaceAll(currentVersion, targetVersion);
    addChange(changes, absolutePath, source, after, {
      kind: 'text',
      occurrences,
    });
  }

  return { scanned, skipped };
}

async function prepareHistoryChanges(root, currentVersion, targetVersion, date, env, changes) {
  const changelogPath = join(root, 'CHANGELOG.md');
  const changelogBefore = (await fileExists(changelogPath)) ? await readFile(changelogPath, 'utf8') : '';
  const changelogAfter = updateChangelog(changelogBefore, {
    currentVersion,
    targetVersion,
    date,
    message: env.TARGET_VERSION_CHANGELOG || env.VERSION_CHANGELOG,
  });
  addChange(changes, changelogPath, changelogBefore, changelogAfter, { kind: 'changelog' });

  const decisionPath = join(root, 'docs', 'decision-log.md');
  const decisionBefore = (await fileExists(decisionPath)) ? await readFile(decisionPath, 'utf8') : '';
  const decisionAfter = updateDecisionLog(decisionBefore, {
    currentVersion,
    targetVersion,
    date,
    decision: env.TARGET_VERSION_DECISION || env.VERSION_DECISION,
  });
  addChange(changes, decisionPath, decisionBefore, decisionAfter, { kind: 'decision-log' });
}

async function commitChanges(changes) {
  const committed = [];
  try {
    for (const [path, change] of changes) {
      const existed = await fileExists(path);
      const mode = existed ? (await stat(path)).mode : 0o644;
      const temporaryPath = `${path}.mdm-version-${process.pid}.tmp`;

      await writeFile(temporaryPath, change.after, { encoding: 'utf8', mode });
      await chmod(temporaryPath, mode);
      await rename(temporaryPath, path);
      committed.push({ path, existed, before: change.before, mode });
    }
  } catch (error) {
    for (const item of committed.reverse()) {
      if (item.existed) {
        await writeFile(item.path, item.before, { encoding: 'utf8', mode: item.mode });
        await chmod(item.path, item.mode);
      } else {
        await rm(item.path, { force: true });
      }
    }
    throw new Error(`Version update failed and committed files were rolled back: ${error.message}`);
  }
}

async function verifyResult(root, targetVersion) {
  const packagePath = join(root, 'package.json');
  const packageJson = JSON.parse(await readFile(packagePath, 'utf8'));
  if (packageJson.version !== targetVersion) {
    throw new Error(`Verification failed: package.json contains ${packageJson.version}, expected ${targetVersion}.`);
  }

  const changelog = await readFile(join(root, 'CHANGELOG.md'), 'utf8');
  if (!changelog.includes(`## [${targetVersion}]`)) {
    throw new Error(`Verification failed: CHANGELOG.md has no ${targetVersion} release entry.`);
  }

  const decisionLog = await readFile(join(root, 'docs', 'decision-log.md'), 'utf8');
  if (!decisionLog.includes(`- Version: \`${targetVersion}\``)) {
    throw new Error(`Verification failed: decision log has no ${targetVersion} entry.`);
  }
}

export async function synchronizeVersion({
  root = process.cwd(),
  envFile = '.env',
  dryRun = false,
  allowDowngrade = false,
  date,
} = {}) {
  const absoluteRoot = resolve(root);
  const absoluteEnvPath = resolve(absoluteRoot, envFile);

  if (!(await fileExists(absoluteEnvPath))) {
    throw new Error(`Missing ${normalizePath(absoluteRoot, absoluteEnvPath)}. Copy .env.example to .env and set TARGET_VERSION.`);
  }

  const envSource = await readFile(absoluteEnvPath, 'utf8');
  const env = parseEnv(envSource);
  const targetVersion = (env.TARGET_VERSION || env.VERSION)?.trim();
  if (!targetVersion) {
    throw new Error(`${normalizePath(absoluteRoot, absoluteEnvPath)} must define TARGET_VERSION.`);
  }
  parseSemver(targetVersion);

  const packagePath = join(absoluteRoot, 'package.json');
  const packageJson = await readJson(packagePath);
  const currentVersion = packageJson.value.version;
  if (typeof currentVersion !== 'string') {
    throw new Error('package.json must contain a string version field.');
  }
  parseSemver(currentVersion);

  const comparison = compareSemver(targetVersion, currentVersion);
  if (comparison === 0) {
    return {
      root: absoluteRoot,
      currentVersion,
      targetVersion,
      alreadyImplemented: true,
      files: [],
      scannedFiles: 0,
      skippedFiles: [],
      phpVersionOrdering: { checked: true },
      date: date || env.TARGET_VERSION_DATE || env.VERSION_DATE || todayIso(),
      dryRun,
    };
  }
  if (comparison < 0 && !allowDowngrade) {
    throw new Error(`TARGET_VERSION ${targetVersion} is lower than current version ${currentVersion}. Use --allow-downgrade only for an intentional recovery.`);
  }

  const phpVersionOrdering = comparison > 0
    ? verifyPhpVersionOrdering(currentVersion, targetVersion)
    : { checked: false, reason: 'Downgrade recovery bypass.' };

  const releaseDate = date || env.TARGET_VERSION_DATE || env.VERSION_DATE || todayIso();
  if (!/^\d{4}-\d{2}-\d{2}$/.test(releaseDate)) {
    throw new Error(`Invalid release date ${releaseDate}; expected YYYY-MM-DD.`);
  }

  const changes = new Map();
  await prepareStructuredChanges(absoluteRoot, currentVersion, targetVersion, changes);
  const scan = await prepareTextChanges(absoluteRoot, currentVersion, targetVersion, changes);
  await prepareHistoryChanges(absoluteRoot, currentVersion, targetVersion, releaseDate, env, changes);

  const summary = {
    root: absoluteRoot,
    currentVersion,
    targetVersion,
    date: releaseDate,
    dryRun,
    files: [...changes.entries()].map(([path, change]) => ({
      path: normalizePath(absoluteRoot, path),
      kind: change.kind,
      occurrences: change.occurrences,
    })),
    scannedFiles: scan.scanned.length,
    skippedFiles: scan.skipped,
    phpVersionOrdering,
  };

  if (!dryRun) {
    await commitChanges(changes);
    await verifyResult(absoluteRoot, targetVersion);
  }

  return summary;
}
