# WordPress DataViews — Adoption for Mermaid Diagrams

## Adopt

- Evaluate for the Diagram Library list and bulk-selection behavior during Phase 00/04.
- If adopted, wrap it behind the library adapter and use table/list first.
- Align URL/query state and REST pagination.

## Do not adopt blindly

- Do not depend on experimental/private APIs without an ADR.
- Do not implement grid before thumbnail strategy is proven.
- Do not let DataViews own domain mutation logic.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **00 spike, 04, 05.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
