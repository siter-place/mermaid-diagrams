# Agent Instructions — Mermaid Diagrams

Before any change:

1. Read `docs/00-product-charter-and-decisions.md`.
2. Read the active folder under `docs/plans/` and its `master-prompt.md`.
3. Run WordPress router/project triage skills.
4. Load every skill named by the phase.
5. Do not implement later phases early.

Never persist invalid Mermaid source. Never bypass WordPress capabilities. Never call AI providers directly. Never expose an Ability to MCP unless its schema, permission callback, annotations, and audit tests are complete.

Use REST for browser application data/mutations. Use the Interactivity API for published-page controls. Keep React and Svelte state framework-local and share only contracts/services that are truly framework neutral.

After a plan phase is finalized:

6. Run the post-phase documentation and automated versioning closeout (`.cursor/rules/post-phase-documentation.mdc`): trigger the `versioning` skill (`.cursor/skills/versioning/SKILL.md`), increment version via `npm run update-version`, write implementation report to `docs/implementation-logs/`, and update `docs/decision-log.md`, `CHANGELOG.md`, and affected docs before ending the session.
