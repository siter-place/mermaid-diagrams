# wp-env — Adoption for Mermaid Diagrams

## Adopt

- Reference environment: WSL2 Ubuntu + Docker Desktop WSL backend.
- Pin WordPress 7.0 and PHP 8.3 for minimum profile.
- Use a separate trunk config for forward compatibility.
- Bootstrap fixtures and test accounts with idempotent scripts/WP-CLI.

## Do not adopt blindly

- Do not store the repository under /mnt/c when file watching/performance matters.
- Do not put secrets in .wp-env.json.
- Do not depend on manually created content.
- Do not use deprecated combined tests-environment assumptions.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **00 and every test/release phase.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
