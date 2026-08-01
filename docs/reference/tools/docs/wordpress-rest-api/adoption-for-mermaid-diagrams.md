# WordPress REST API — Adoption for Mermaid Diagrams

## Adopt

- Use `mdm/v1` for diagram workflows, settings, validation, thumbnail, usage, and bulk operations.
- Return stable machine-readable error codes and version tokens.
- Use Application Passwords for Bruno/MCP HTTP tests and nonce/cookie auth in wp-admin.

## Do not adopt blindly

- No database writes directly from controllers.
- No permission callback that simply returns true for private operations.
- No leaking raw source in list responses unless explicitly requested/authorized.
- No undocumented ad-hoc endpoints.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **02 onward.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
