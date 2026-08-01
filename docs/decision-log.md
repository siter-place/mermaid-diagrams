# Implementation Decisions Log

## REL-1.0.0 — Version 1.0.0

- Date: 2026-08-01
- Version: `1.0.0`
- Previous version: `0.0.0-development`
- Status: Applied
- Command: `npm run update-version`
- Changelog: [1.0.0](../CHANGELOG.md#100---2026-08-01)

**Decision:** Baseline version 1.0.0 release approved following completion of Phase 00 and Phase 01.

Chronological record of decisions made while executing phased implementation plans. This log supplements — but does not override — the authoritative charter in `00-product-charter-and-decisions.md`.

## How to use

After each plan phase is finalized, append an entry with the phase ID, date, decisions, spike outcomes, deferrals, and ADR references. Cursor applies this via the `.cursor/rules/post-phase-documentation.mdc` rule.

## Entries

<!-- Newest entries go at the top of this section. -->

### 2026-08-01 — Phase 01 — Plugin Kernel, Domain Model, Storage, and Capabilities

- **Lightweight DI Container & Service Providers**: Implemented custom `Container` and `ServiceProviderRegistry` wired into `Plugin::on_init()` to support modular bounded contexts without third-party framework overhead (ADR-019).
- **Domain Layer & Value Objects**: Built domain aggregates and immutable value objects (`DiagramId`, `DiagramSource`, `SourceHash`, `DiagramTitle`, `DiagramDescription`, `DiagramStatus`, `DiagramType`, `DiagramVersion`, `ValidationReceipt`, `RenderConfig`) enforcing line-ending normalization (LF), size limits (500 KB), and non-empty title rules.
- **CPT, Taxonomies, and Meta Registration**: Registered `mdm_diagram` CPT (`show_in_rest` => true, `rest_base` => `mdm-diagrams`), hierarchical categories (`mdm_diagram_category`), flat tags (`mdm_diagram_tag`), and 9 protected post meta fields.
- **Capabilities & Role Assignment**: Defined full capability set (`edit_mdm_diagrams`, `manage_mdm_settings`, etc.) and mapped defaults idempotently to Administrator and Editor roles.
- **Persistence & Revisions**: Implemented `WordPressDiagramRepository` supporting CRUD, native revisions on source updates, trash, restore, and duplicate. Preserves raw Mermaid syntax (`<`, `>`) in `post_content` without KSES entity encoding.
- **Database Migrations & Usage Schema**: Implemented `UpgradeRunner` targeting DB version `1.0.0` with `dbDelta()` creation of `{$prefix}mdm_usage` and `{$prefix}mdm_usage_dirty` tables (ADR-020).
- **Uninstall Data Retention Policy**: `uninstall.php` defaults to `preserve` (no deletion on uninstall). Supports explicit `delete_settings` and `delete_all` policies.
- **WP-CLI Diagnostics**: Registered `wp mdm status` and `wp mdm capabilities repair` commands.
- **Admin Shell Placeholder**: Registered top-level Diagrams admin menu (`mdm-diagrams`) rendering `#mdm-diagram-library-root` shell template for Phase 04 React mount.
- **ADRs Added**: ADR-019, ADR-020.

### 2026-08-01 — Phase 00 — Development Environment, Toolchain, and Risk Spikes

- **Mermaid Pinning**: Pinned Mermaid JS version to `11.4.1` (ADR-014).
- **Live Editor Static Build**: Selected static SPA bundle strategy for embedded editor (ADR-015).
- **Component System Compatibility**: Confirmed `@wordpress/components` and `@wordpress/element` usage over `plugin-ui` (ADR-016).
- **Controlled SVG Upload**: Implemented controlled SVG media upload handling as featured image thumbnail (ADR-017).
- **MCP Adapter Integration**: Defined companion plugin pattern and Abilities API lifecycle integration (ADR-018).
- **Toolchain & wp-env**: Configured WSL2 + `wp-env` (WP 7.0, PHP 8.3) with automated Bruno REST user bootstrap and Playwright visual regression baseline.
- **ADRs Added**: ADR-014, ADR-015, ADR-016, ADR-017, ADR-018.
