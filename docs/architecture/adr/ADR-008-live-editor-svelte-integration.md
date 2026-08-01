# ADR-008: Integrate Mermaid Live Editor as a Svelte Admin Application

**Status:** Accepted

## Context

The product owner requires Mermaid Live Editor as the default source-editing experience. The current upstream application is Svelte/SvelteKit and has its own editor, preview, configuration, persistence, and test architecture.

## Decision

Build a pinned, adapted Svelte application for `admin.php?page=mdm-editor`. Compile it to static WordPress-admin assets; do not require a separate SvelteKit server at runtime. WordPress-specific code enters through an adapter that supplies REST root, nonce, diagram ID, capabilities, locale, and navigation callbacks.

The plugin owns persistence, permissions, taxonomies, revisions, conflict handling, AI operations, and save-state UI. Upstream source/editor behavior is kept as close as practical to simplify updates.

## Consequences

- The plugin has two React applications and one Svelte application.
- Shared contracts must be framework-neutral TypeScript.
- Upstream version/commit, patches, and license must be recorded.
- A build spike must prove CSP, asset loading, localization, and WordPress admin compatibility.
- The visual editor remains a later adapter/module and does not rewrite this decision.
