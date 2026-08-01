# WordPress Playground and Blueprints — Adoption for Mermaid Diagrams

## Adopt

- Create a release/demo Blueprint after core features stabilize.
- Use Agent Skills `wp-playground` and `blueprint`.
- Keep fixtures minimal and avoid external secrets.

## Do not adopt blindly

- Do not use Playground as the sole PHP/database/browser integration environment.
- Do not include AI keys or private source.
- Do not let Blueprint setup diverge from release ZIP behavior.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **13 release/preview.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
