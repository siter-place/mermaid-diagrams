# @wordpress/scripts and @wordpress/create-block — Adoption for Mermaid Diagrams

## Adopt

- Use block.json API version 3 and server registration from metadata.
- Use WordPress dependency extraction for React admin/block packages.
- Evaluate a hybrid build: wp-scripts for WordPress surfaces and Vite for adapted Svelte editor.

## Do not adopt blindly

- Do not force Svelte Live Editor through a build tool that makes upstream maintenance harder.
- Do not duplicate React in the plugin bundle when WordPress provides it.
- Do not hand-maintain asset dependency files.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **00, 01, 06–08, 13.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
