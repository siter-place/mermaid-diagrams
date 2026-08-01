# Mermaid JS — Adoption for Mermaid Diagrams

## Adopt

- Pin and bundle locally; never depend on CDN at runtime.
- Use the same version in browser and validation worker.
- Call parse before persistence and render after validation.
- Lock security configuration and limit source/config size/complexity.
- Generate accessible SVG title/description and deterministic wrappers.

## Do not adopt blindly

- Do not let diagram source override security level.
- Do not use AI output as validation.
- Do not silently upgrade Mermaid without corpus tests.
- Do not assume parse/render diagnostics are stable across versions.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **00, 03, 06–08, 10–13.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
