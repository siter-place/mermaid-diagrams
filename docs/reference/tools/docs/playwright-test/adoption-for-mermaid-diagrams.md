# Playwright Test — Adoption for Mermaid Diagrams

## Adopt

- Use CLI as committed source of truth for browser tests.
- Use Chromium as visual-baseline profile; expand compatibility separately.
- Wait for plugin-owned render markers.
- Store auth state only in ignored local/CI artifacts.
- Capture trace on first retry and publish HTML reports.

## Do not adopt blindly

- No arbitrary waitForTimeout in normal tests.
- No selectors based on generated CSS class hashes.
- No blanket full-page snapshots where focused regions are stable.
- No snapshot updates without review.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **00, 04–13.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
