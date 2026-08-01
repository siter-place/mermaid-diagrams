# WordPress Design System and wpds Skill — Adoption for Mermaid Diagrams

## Adopt

- Use for Library, block inspector, dialogs, notices and Live Editor WordPress chrome.
- Create shared tokens that can be consumed by both React and Svelte.
- Verify high contrast, RTL, keyboard and reduced motion.

## Do not adopt blindly

- Do not globally restyle wp-admin.
- Do not use inaccessible custom controls where a WordPress component exists.
- Do not copy unstable experimental APIs without a wrapper.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **04–08, 10, 13.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
