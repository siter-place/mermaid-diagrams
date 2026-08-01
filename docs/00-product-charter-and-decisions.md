# Product Charter and Final Architecture Decisions

Status: **authoritative**  
Last reviewed: 2026-08-01

This document resolves the earlier open questions. When another document conflicts with it, this document wins and the conflicting text must be corrected in the same change set.

## 1. Identity and compatibility

- Product name: **Mermaid Diagrams**.
- Vendor namespace: `WebFalcon\MermaidDiagrams`.
- Technical prefix: `mdm`.
- Block namespace/name: `mdm/diagram`.
- REST namespace: `mdm/v1`.
- Minimum WordPress version: **7.0**.
- Minimum PHP version: **8.3**.
- WordPress 7.0 is required so the plugin can use the core AI Client, Connectors API, current Abilities APIs, block API v3, and current admin packages without maintaining legacy compatibility branches.

## 2. Canonical content and validity

- Canonical Mermaid source belongs to the diagram record for library diagrams and to block attributes for inline diagrams.
- A persisted diagram must always be syntactically valid for the pinned Mermaid version.
- First-party browser surfaces must call Mermaid `parse()` before every create/update request and must block submission on failure.
- The save payload carries a validation receipt containing source hash, Mermaid version, detected type, and validation timestamp.
- PHP still validates authorization, schema, source length, denied directives/configuration, and that the receipt hash matches the submitted source.
- Browser validation alone cannot prove syntax for non-browser writers. Therefore direct MCP/AI persistence is enabled only when the shared Mermaid-JS validation worker profile is available. Without it, agents may generate candidates but persistence requires first-party browser validation. This is a consequence of the “always valid” invariant, not an optional enhancement.
- Invalid drafts are not stored. Unsaved invalid work remains in local editor recovery storage until corrected or deliberately discarded.

## 3. Editor strategy

- The default dedicated editor is an adapted, pinned build of `mermaid-js/mermaid-live-editor`.
- It runs as a separate Svelte application mounted on a WordPress admin page and communicates with WordPress only through the plugin REST client/adapter.
- The project does not independently choose or build a CodeMirror/Monaco abstraction. The integration spike retains whichever editor implementation the selected Live Editor release requires and encapsulates it behind the Svelte application boundary.
- WordPress-specific additions include Save, Save as Copy, metadata/taxonomy controls, post status, conflict handling, revisions, AI actions, REST error handling, and WordPress navigation.
- Visual editing is required as a product direction but is a separate later phase after the source editor and persistence workflow are stable. Initial visual support is flowchart-only and must be loss-aware.

## 4. Diagram organization

- Categories are hierarchical and a diagram may have multiple categories.
- Bulk category operations are always explicit: **Add**, **Remove**, and **Replace**.
- The word **Move** is used only if a later UI introduces one primary folder/category as an additional concept.
- Tags are non-hierarchical and many-to-many.
- Version 1.0 uses table/list plus an on-demand preview panel. A visual card grid is deferred until thumbnail performance is proven.

## 5. Downloads, themes, and frontend behavior

- Public `.mmd` download is globally enabled by default and can be reduced at block level.
- Administrators can disable the source-download control while retaining SVG download. Because browser rendering receives source in browser-rendered configurations, this setting removes the download affordance; it is not a secrecy boundary.
- SVG is the default and required image/export format. PNG is not a core release requirement.
- Theme resolution is **global default → diagram default**. There is no block-level theme override in version 1.0.
- Frontend controls include zoom in/out, pan, fit, reset, fullscreen, and permitted downloads.
- Fullscreen uses the native Fullscreen API where available and an accessible full-viewport dialog fallback elsewhere.
- Reference blocks resolve the latest published/current accessible version. Revision pinning is deferred.
- A private, draft, trashed, or inaccessible library diagram never renders publicly. Editors receive a clear warning, and post publication surfaces a validation warning.

## 6. Thumbnail and media policy

- Every normal diagram save follows one coordinated workflow: validate Mermaid source, render the matching SVG in the browser, sanitize it client-side, then submit source, validation receipt, SVG thumbnail, dimensions, and optimistic version token in one mutation.
- The application service treats source persistence and featured-image replacement as one logical command. The server validates and sanitizes the SVG again, verifies the source hash, creates or replaces the controlled attachment, assigns `_thumbnail_id`, and only then acknowledges the diagram save.
- If media persistence fails, the save is not reported as successful. Unsaved valid source and SVG remain in local recovery state so the author can retry without data loss. For a newly created record, partial post/media work is compensatingly deleted; for an update, the previous source and featured image remain active.
- A separate thumbnail regeneration endpoint exists for repair and operational recovery, but it is not the normal editor save path.
- WordPress does not provide a blanket safe-SVG guarantee. The plugin narrowly enables SVG only inside this controlled workflow, sanitizes it twice, verifies provenance, and never enables unrestricted site-wide SVG upload.

## 7. Usage index and cron

- Usage data is derived, not canonical content.
- The plugin stores a reverse usage index and aggregated count in the WordPress database.
- Post-save hooks mark affected posts dirty. WP-Cron incrementally reindexes dirty posts and performs periodic reconciliation.
- All cron work is idempotent and has a WP-CLI/manual run path.

## 8. AI, Abilities, and MCP

- AI features use the WordPress 7.0 core AI Client and Connectors architecture. The plugin never stores a provider key and never calls OpenAI directly.
- OpenAI is the initial configured provider, but provider-specific behavior is forbidden in domain/application code.
- AI actions include generate candidate, repair syntax, explain, simplify, and propose accessible title/description. AI output is always untrusted until Mermaid validation succeeds.
- The plugin registers narrow, schema-driven Abilities that call the same application services as REST controllers.
- The official WordPress MCP Adapter exposes only deliberately approved abilities to clients such as Cursor, Codex, Copilot, or other MCP-compatible chat interfaces.
- Mutating abilities require normal WordPress capabilities and explicit confirmation/approval metadata. No capability is granted because a caller is an AI agent.

## 9. Lifecycle and scope

- No shortcode, fenced-code, Merpress, or WP Mermaid migration is included in core 1.0.
- Real-time collaboration is out of scope. Use WordPress locking, revisions, version tokens, and conflict handling.
- Data is preserved on uninstall by default. Destructive removal requires a pre-enabled setting and explicit documentation.
- Content and settings are per-site on multisite. Network-wide libraries are out of scope.
