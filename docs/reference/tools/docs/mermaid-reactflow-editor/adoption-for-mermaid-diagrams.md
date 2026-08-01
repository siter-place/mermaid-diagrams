# Mermaid React Flow Editor — Adoption for Mermaid Diagrams

## Adopt

- Use ideas for the later flowchart adapter, compatibility report, node/edge editing, and viewport controls.
- Build a supported-subset parser/serializer with a loss report and corpus.
- Keep Mermaid source canonical and require validation after serialization.

## Do not adopt blindly

- Do not enable visual editing for every Mermaid type.
- Do not silently discard directives, subgraphs, styling, comments, or unsupported edge syntax.
- Do not make React Flow metadata canonical.
- Do not include unrelated AI/provider code from the example.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **12 only, with research in 00.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
