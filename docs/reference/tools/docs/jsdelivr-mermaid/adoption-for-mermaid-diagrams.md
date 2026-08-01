# jsDelivr Mermaid Distribution — Adoption for Mermaid Diagrams

## Adopt

- Use only to understand distributable Mermaid artifacts and for non-production research.
- Pin npm Mermaid locally in package lockfiles and bundle assets into the plugin.

## Do not adopt blindly

- No production runtime CDN dependency.
- No unpinned `latest` script URL.
- No CSP/privacy/performance dependency on third-party delivery.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **00 dependency decision only.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
