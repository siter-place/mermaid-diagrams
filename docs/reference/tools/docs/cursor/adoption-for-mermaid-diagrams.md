# Cursor — Adoption for Mermaid Diagrams

## Adopt

- Open the Linux filesystem project via WSL.
- Use project Agent Skills and the phase master prompt.
- Use terminal commands inside WSL.
- Use Playwright MCP for exploration, then commit deterministic tests.

## Do not adopt blindly

- Do not let the agent implement all phases in one prompt.
- Do not accept unverified dependency/API guesses.
- Do not store secrets in project rules or chat transcripts.
- Do not treat generated code as complete before tests/docs.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **All phases.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
