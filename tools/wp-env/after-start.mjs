import { execFileSync } from 'node:child_process';

function wp(...args) {
  execFileSync('npx', ['wp-env', 'run', 'cli', 'wp', ...args], {
    stdio: 'inherit',
    shell: process.platform === 'win32',
  });
}

// Keep this script idempotent because wp-env lifecycle hooks run on fresh and existing environments.
wp('option', 'update', 'blogname', 'Mermaid Diagrams Development');
wp('rewrite', 'structure', '/%postname%/', '--hard');
wp('rewrite', 'flush', '--hard');
console.log('wp-env base setup complete. Run the Phase 00 authentication bootstrap before Bruno tests.');
