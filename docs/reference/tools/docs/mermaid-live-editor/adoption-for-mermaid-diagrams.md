# Mermaid Live Editor — Adoption for Mermaid Diagrams

## Adopt

- Pin a release/commit and maintain an explicit patch/adapter set.
- Compile as a static admin app mounted on `admin.php?page=mdm-editor`.
- Add WordPress Save, metadata, status, revisions, conflict, AI, and thumbnail workflows.
- Keep upstream changes isolated from shared domain contracts.

## Do not adopt blindly

- Do not iframe a hosted public editor.
- Do not require a SvelteKit server in production.
- Do not copy provider/storage assumptions that bypass WordPress.
- Do not promise painless upgrades without a patch inventory.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **00 spike, 08, 10, 12, 13.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
