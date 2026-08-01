# WordPress Abilities API — Adoption for Mermaid Diagrams

## Adopt

- Register narrow diagram queries and commands.
- Call the same application services as REST.
- Use strict schemas and output normalization.
- Audit and verify registrations before MCP exposure.

## Do not adopt blindly

- Do not mirror every internal method as an ability.
- Do not put business rules only in the ability callback.
- Do not mark mutating operations read-only or omit permission checks.
- Do not expose raw private diagrams broadly.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **11, with service preparation earlier.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
