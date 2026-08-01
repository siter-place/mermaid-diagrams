# Bruno Agent Skills — Adoption for Mermaid Diagrams

## Adopt

- Use the skills when creating or changing committed .bru files.
- Keep the collection executable in Bruno GUI and bru CLI.
- Generate JSON/JUnit/HTML reports in CI.
- Model workflows, not only isolated happy-path requests.

## Do not adopt blindly

- Never commit Application Passwords or connector keys.
- Do not use developer sandbox unless a reviewed test requires it.
- Do not let generated collections drift from REST schemas and docs.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **00, 02–05, 08–11, 13.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
