import assert from 'node:assert/strict';
import { cp, mkdtemp, readFile, rm, writeFile } from 'node:fs/promises';
import { tmpdir } from 'node:os';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import test from 'node:test';
import { spawnSync } from 'node:child_process';

const testDirectory = dirname(fileURLToPath(import.meta.url));
const projectRoot = resolve(testDirectory, '../../..');
const fixtureRoot = join(projectRoot, 'tests/fixtures/versioning/sample-plugin');
const cliPath = join(projectRoot, 'scripts/increase-plugin-version.mjs');

async function createFixture() {
  const directory = await mkdtemp(join(tmpdir(), 'mdm-version-sync-'));
  await cp(fixtureRoot, directory, { recursive: true });
  return directory;
}

function runCli(root, args = []) {
  return spawnSync(process.execPath, [cliPath, '--root', root, '--env', 'version.env', ...args], {
    cwd: projectRoot,
    encoding: 'utf8',
  });
}

test('updates WordPress, PHP, npm, Composer, docs, changelog, and decision log', async () => {
  const root = await createFixture();
  try {
    const result = runCli(root);
    assert.equal(result.status, 0, result.stderr);
    assert.match(result.stdout, /1\.2\.3 -> 1\.3\.0/);

    const packageJson = JSON.parse(await readFile(join(root, 'package.json'), 'utf8'));
    assert.equal(packageJson.version, '1.3.0');
    assert.equal(packageJson.dependencies['dependency-that-coincidentally-matches'], '1.2.3');

    const packageLock = JSON.parse(await readFile(join(root, 'package-lock.json'), 'utf8'));
    assert.equal(packageLock.version, '1.3.0');
    assert.equal(packageLock.packages[''].version, '1.3.0');
    assert.equal(packageLock.packages['node_modules/dependency-that-coincidentally-matches'].version, '1.2.3');

    const composer = JSON.parse(await readFile(join(root, 'composer.json'), 'utf8'));
    assert.equal(composer.version, '1.3.0');

    const plugin = await readFile(join(root, 'sample-wordpress-plugin.php'), 'utf8');
    assert.match(plugin, /Version: 1\.3\.0/);
    assert.match(plugin, /SAMPLE_PLUGIN_VERSION', '1\.3\.0'/);

    const readme = await readFile(join(root, 'readme.txt'), 'utf8');
    assert.match(readme, /Stable tag: 1\.3\.0/);

    const docs = await readFile(join(root, 'docs/current-version.md'), 'utf8');
    assert.match(docs, /`1\.3\.0`/);
    assert.match(docs, /`v1\.3\.0`/);

    const envExample = await readFile(join(root, '.env.example'), 'utf8');
    assert.match(envExample, /VERSION=1\.3\.0/);

    const changelog = await readFile(join(root, 'CHANGELOG.md'), 'utf8');
    assert.match(changelog, /## \[1\.3\.0\] - 2026-08-01/);
    assert.match(changelog, /A release candidate feature/);
    assert.match(changelog, /## \[1\.2\.3\] - 2026-07-20/);

    const decisionLog = await readFile(join(root, 'docs/decision-log.md'), 'utf8');
    assert.match(decisionLog, /REL-1\.3\.0/);
    assert.match(decisionLog, /Previous version: `1\.2\.3`/);
    assert.match(decisionLog, /REL-1\.2\.3/);
  } finally {
    await rm(root, { recursive: true, force: true });
  }
});

test('dry run reports changes without modifying files', async () => {
  const root = await createFixture();
  try {
    const before = await readFile(join(root, 'package.json'), 'utf8');
    const result = runCli(root, ['--dry-run']);
    assert.equal(result.status, 0, result.stderr);
    assert.match(result.stdout, /Dry run completed/);
    assert.equal(await readFile(join(root, 'package.json'), 'utf8'), before);
  } finally {
    await rm(root, { recursive: true, force: true });
  }
});

test('handles target version already implemented gracefully', async () => {
  const root = await createFixture();
  try {
    await writeFile(join(root, 'version.env'), 'TARGET_VERSION=1.2.3\n', 'utf8');
    const result = runCli(root);
    assert.equal(result.status, 0, result.stderr);
    assert.match(result.stdout, /Target version 1\.2\.3 is already implemented/);
  } finally {
    await rm(root, { recursive: true, force: true });
  }
});

test('rejects a lower target version by default', async () => {
  const root = await createFixture();
  try {
    await writeFile(join(root, 'version.env'), 'TARGET_VERSION=1.1.0\n', 'utf8');
    const result = runCli(root);
    assert.equal(result.status, 1);
    assert.match(result.stderr, /lower than current version/);
  } finally {
    await rm(root, { recursive: true, force: true });
  }
});

test('rejects an invalid semantic version', async () => {
  const root = await createFixture();
  try {
    await writeFile(join(root, 'version.env'), 'TARGET_VERSION=release-next\n', 'utf8');
    const result = runCli(root);
    assert.equal(result.status, 1);
    assert.match(result.stderr, /Invalid semantic version/);
  } finally {
    await rm(root, { recursive: true, force: true });
  }
});

test('rejects a duplicate changelog release entry', async () => {
  const root = await createFixture();
  try {
    const changelogPath = join(root, 'CHANGELOG.md');
    const changelog = await readFile(changelogPath, 'utf8');
    await writeFile(changelogPath, `${changelog}
## [1.3.0] - 2026-08-01
`, 'utf8');
    const result = runCli(root);
    assert.equal(result.status, 1);
    assert.match(result.stderr, /already contains version 1\.3\.0/);
  } finally {
    await rm(root, { recursive: true, force: true });
  }
});
