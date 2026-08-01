# ADR-015: Mermaid Live Editor Static Build and Adaptation Strategy

## Status
Accepted (Phase 00 Architecture Spike)

## Context
Spike 2 investigated embedding the official Svelte-based Mermaid Live Editor as a dedicated source editor inside WordPress Admin.

## Decision
1. **Static Bundle Adapter**: Build Mermaid Live Editor as a static Single Page Application (SPA) asset bundle using Vite with SSR disabled.
2. **Mount Contract**: Embed a container `<div id="mdm-live-editor-root"></div>` in custom WordPress admin pages (`admin.php?page=mermaid-diagrams-editor`).
3. **Data Sync**: Communicate between the WordPress Admin wrapper and the static editor bundle via structured JSON state contracts passed over `window.postMessage` or initial page state objects (`window.mdmEditorState`).
4. **Content Security Policy (CSP)**: Ensure administrative pages permit local script execution without inline eval restrictions where possible, or bundle workers into blob URLs.

## Consequences
- Keeps the full power of Mermaid Live Editor intact without maintaining a heavy fork.
- Separates editor visual state from WordPress REST API persistence boundaries.
