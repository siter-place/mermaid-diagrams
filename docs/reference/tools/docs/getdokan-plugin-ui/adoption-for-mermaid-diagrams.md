# getdokan/plugin-ui — Adoption for Mermaid Diagrams

## Adopt

- Use as the starting pattern for the Diagram Library and settings experience.
- Create an adapter boundary so UI library upgrades do not leak into domain/application code.
- Use section-level settings saves and normalized server responses.
- Use WordPress-compatible UI terms and accessible loading/error/notice patterns.

## Do not adopt blindly

- Do not copy the repository wholesale.
- Do not force its project build or domain model onto this plugin.
- Verify React peer compatibility with WordPress 7.0 before adoption.
- Avoid global Tailwind/preflight leakage into wp-admin.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **00 spike, 04, 05, 13.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
