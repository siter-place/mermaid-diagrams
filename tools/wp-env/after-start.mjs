import { execFileSync } from 'node:child_process';

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

// Keep this script idempotent because wp-env lifecycle hooks run on fresh and existing environments.
wpInherit('option', 'update', 'blogname', 'Mermaid Diagrams Development');
wpInherit('rewrite', 'structure', '/%postname%/', '--hard');
wpInherit('rewrite', 'flush', '--hard');

try {
  const userCheck = wp('user', 'get', 'mdm_api_test', '--field=ID');
  console.log(`Bruno test user mdm_api_test already exists (ID: ${userCheck.trim()}).`);
} catch {
  console.log('Creating Bruno test user mdm_api_test...');
  wpInherit('user', 'create', 'mdm_api_test', 'mdm_api_test@example.test', '--role=administrator', '--user_pass=testpass');
  wpInherit('user', 'application-password', 'create', 'mdm_api_test', 'bruno-test');
}

console.log('wp-env setup complete.');
