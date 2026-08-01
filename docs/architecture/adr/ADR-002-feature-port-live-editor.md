# ADR-002: Feature-Port Mermaid Live Editor Instead of Embedding It

- Status: Superseded by ADR-008
- Date: 2026-07-29
- Superseded: 2026-08-01

## Original decision

The earlier plan proposed rebuilding Live Editor features in React.

## Superseding decision

The product owner selected an adapted, pinned Mermaid Live Editor Svelte application as the default dedicated editor. See `ADR-008-live-editor-svelte-integration.md`. The hosted editor is still not embedded in an iframe and no separate SvelteKit server is required at runtime.

## Retained lesson

WordPress persistence, capabilities, REST, settings, revisions, AI, and conflict handling remain owned by Mermaid Diagrams through an explicit adapter boundary.
