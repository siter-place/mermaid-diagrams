# WordPress MCP Adapter — Adoption for Mermaid Diagrams

## Adopt

- Install standalone release in wp-env for integration testing.
- Choose plugin or Composer packaging for production after Phase 00 spike.
- Expose an explicit allowlist of Mermaid abilities.
- Test through a least-privileged agent user.

## Do not adopt blindly

- Do not expose abilities merely because they exist.
- Do not load both standalone and bundled adapter.
- Do not equate MCP authentication with WordPress authorization.
- Do not permit autonomous destructive actions without clear approval semantics.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **00 spike, 11, 13.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
