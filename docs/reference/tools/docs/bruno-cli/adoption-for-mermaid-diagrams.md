# Bruno and bru CLI — Adoption for Mermaid Diagrams

## Adopt

- Use `bruno/` as authoritative black-box REST contract.
- Authenticate with a dedicated WordPress Application Password.
- Cover permissions, schemas, conflicts, validation receipts, thumbnail, usage, and abilities.
- Run Safe Mode unless reviewed functionality requires developer mode.

## Do not adopt blindly

- Do not test browser UI through Bruno.
- Do not duplicate all PHP unit cases; focus on HTTP contract and workflows.
- Do not place secrets in collection files.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **00, 02–05, 08–11, 13.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
