import { execFileSync } from 'node:child_process';
import { readFileSync, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';

function wp(...args) {
  return execFileSync('npx', ['wp-env', 'run', 'cli', 'wp', ...args], {
    encoding: 'utf-8',
    stdio: ['ignore', 'pipe', 'inherit'],
    shell: process.platform === 'win32',
  });
}

function wpInherit(...args) {
  execFileSync('npx', ['wp-env', 'run', 'cli', 'wp', ...args], {
    stdio: 'inherit',
    shell: process.platform === 'win32',
  });
}

function updateDotEnvPassword(password) {
  const envPath = resolve(process.cwd(), '.env');
  try {
    let envContent = readFileSync(envPath, 'utf-8');
    envContent = envContent.replace(
      /^BRUNO_APPLICATION_PASSWORD=.*$/m,
      `BRUNO_APPLICATION_PASSWORD=${password}`
    );
    writeFileSync(envPath, envContent, 'utf-8');
    console.log('Updated .env with BRUNO_APPLICATION_PASSWORD.');
  } catch (err) {
    console.error('Failed to update .env:', err);
  }
}

// Keep this script idempotent because wp-env lifecycle hooks run on fresh and existing environments.
wpInherit('theme', 'activate', 'twentytwentyfive');
wpInherit('option', 'update', 'template', 'twentytwentyfive');
wpInherit('option', 'update', 'stylesheet', 'twentytwentyfive');

wpInherit('plugin', 'activate', 'mermaid-diagrams');
wpInherit('plugin', 'activate', 'mcp-adapter');
wpInherit('plugin', 'activate', 'ai-provider-for-openai');
wpInherit('plugin', 'activate', 'secure-custom-fields');

wpInherit('option', 'update', 'blogname', 'Mermaid Diagrams Development');
wpInherit('rewrite', 'structure', '/%postname%/', '--hard');
wpInherit('rewrite', 'flush', '--hard');

try {
  wpInherit('user', 'update', 'admin', '--user_pass=password');
} catch {
  console.log('Ensure admin password failed.');
}

try {
  wpInherit('mdm', 'capabilities', 'repair');
} catch {
  console.log('Capabilities repair skipped.');
}

try {
  const userCheck = wp('user', 'get', 'mdm_api_test', '--field=ID');
  console.log(`Bruno test user mdm_api_test already exists (ID: ${userCheck.trim()}).`);
} catch {
  console.log('Creating Bruno test user mdm_api_test...');
  wpInherit('user', 'create', 'mdm_api_test', 'mdm_api_test@example.test', '--role=administrator', '--user_pass=testpass');
}

// Re-create app password for mdm_api_test to ensure .env is always synchronized
try {
  try {
    wp('user', 'application-password', 'delete', 'mdm_api_test', 'bruno-test');
  } catch {
    // ignore if password didn't exist
  }
  const appPassOutput = wp('user', 'application-password', 'create', 'mdm_api_test', 'bruno-test');
  const match = appPassOutput.match(/Password:\s*([A-Za-z0-9\s]+)/);
  if (match && match[1]) {
    const cleanPassword = match[1].replace(/\s+/g, '');
    updateDotEnvPassword(cleanPassword);
  }
} catch (err) {
  console.error('Failed to configure application password:', err);
}

console.log('wp-env setup complete.');
