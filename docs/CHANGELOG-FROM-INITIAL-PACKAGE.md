# Changes from the Initial Specification Package

This rebuild preserves useful research from the original ZIP and applies the product owner's final decisions.

## Product and platform

- Renamed product to **Mermaid Diagrams**.
- Set namespace `WebFalcon\MermaidDiagrams`, technical prefix `mdm`, WordPress 7.0, PHP 8.3.
- Finalized category, download, validity, theme, revision, private-reference, uninstall, multisite, and collaboration policies.

## Architecture

- Replaced the earlier “three React apps” model with two React apps, one adapted Svelte Mermaid Live Editor, and one Interactivity API frontend module.
- Made Mermaid Live Editor the primary dedicated editor; visual editing is a later flowchart-first adapter phase.
- Added coordinated canonical-source plus featured-SVG save semantics.
- Added server/headless validation worker constraints for autonomous agent writes.
- Added WordPress AI Client/Connectors, Abilities API, and official MCP Adapter integration.

## Development and testing

- Added WSL2/Docker/Cursor/wp-env setup and bootstrap checklist.
- Added Playwright CLI, Playwright MCP, and deterministic visual-regression policy.
- Added a Bruno collection scaffold and REST E2E strategy using Safe Mode and CI reports.
- Added WordPress and Bruno Agent Skills workflow documentation.

## Delivery planning

- Replaced the monolithic plan with 14 sequential phases.
- Every phase has functional scope, technical tasks, tests/acceptance, documentation expectations, handoff, and a standalone master prompt.

## Reference research

- Added 24 tool/reference folders with overview, architecture, and project-adoption documents, plus a catalog, source lock, and cross-tool synthesis.
- No third-party source repositories or Agent Skill source are included.
