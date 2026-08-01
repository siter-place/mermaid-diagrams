# WordPress Interactivity API — Adoption for Mermaid Diagrams

## Adopt

- Use for frontend toolbar, viewport state, fullscreen fallback, download actions, and render lifecycle.
- Keep public bundle small and load only when the block exists.
- Use local context per block instance and accessible state announcements.

## Do not adopt blindly

- Do not mount React on published pages for simple controls.
- Do not place secrets/nonces unnecessarily in public config.
- Do not use one global mutable diagram state for all blocks.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **06, 07, 13.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
