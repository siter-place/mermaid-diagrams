# WordPress AI Provider for OpenAI — Adoption for Mermaid Diagrams

## Adopt

- Install in wp-env and configure the key manually in Settings > Connectors.
- Use as initial test provider while keeping Mermaid Diagrams provider-neutral.

## Do not adopt blindly

- Do not make it a mandatory plugin dependency unless product distribution requires it.
- Do not read its key directly.
- Do not write OpenAI-specific prompt code into domain services.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **00 environment, 10 AI, 13 release docs.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
