# Changelog

All notable implementation changes during phased delivery are recorded here. Format follows [Keep a Changelog](https://keepachangelog.com/en/1.4.1/). The initial specification-package delta remains in `CHANGELOG-FROM-INITIAL-PACKAGE.md`.

## [Unreleased]

## [Unreleased]

### Phase 04 — React Diagram Library Shell and Settings UI

#### Added
- React diagram library and settings admin applications with dual webpack entry points under `build/admin/`.
- Shared admin provider stack, hooks, scoped CSS, PHPUnit/JS/Playwright coverage, and ADR-022.

#### Changed
- Settings permission uses `manage_mdm_settings`; admin pages enqueue screen-scoped assets with bootstrap data.

### Phase 01 — Plugin Kernel, Domain Model, Storage, and Capabilities (2026-08-01)

#### Added
- Lightweight DI Container (`WebFalcon\MermaidDiagrams\Bootstrap\Container`) and `ServiceProviderRegistry` wired into `Plugin::on_init()`.
- `DiagramServiceProvider`, `AdminServiceProvider`, and `UpgradeServiceProvider` implementations.
- Domain Value Objects: `DiagramId`, `DiagramSource`, `SourceHash`, `DiagramTitle`, `DiagramDescription`, `DiagramStatus`, `DiagramType`, `DiagramVersion`, `ValidationReceipt`, `RenderConfig`.
- Domain Aggregate `Diagram` and port interface `DiagramRepository`.
- CPT `mdm_diagram` with `show_in_rest => true`, `rest_base => mdm-diagrams`, supports title/excerpt/revisions/author/editor.
- Taxonomies `mdm_diagram_category` (hierarchical) and `mdm_diagram_tag` (flat).
- Protected post meta registration for `_mdm_diagram_type`, `_mdm_render_config`, `_mdm_visual_model`, `_mdm_visual_adapter`, `_mdm_source_hash`, `_mdm_renderer_version`, `_mdm_validation_state`, `_mdm_validation_summary`, `_mdm_last_editor_id`.
- Plugin capabilities matrix (`edit_mdm_diagrams`, `manage_mdm_settings`, etc.) with idempotent role assignment for Administrator and Editor roles.
- `WordPressDiagramRepository` implementing CRUD, native revisions, trash, restore, and duplicate operations.
- `UpgradeRunner` targeting DB version `1.4.1` with `dbDelta` creation of `{$prefix}mdm_usage` and `{$prefix}mdm_usage_dirty` tables.
- `uninstall.php` implementing configurable data retention policy (default `preserve`).
- WP-CLI CLI commands `wp mdm status` and `wp mdm capabilities repair`.
- Top-level Diagrams admin menu page (`mdm-diagrams`) rendering placeholder shell `#mdm-diagram-library-root`.
- Unit, integration, Bruno REST, and Playwright E2E test suites for Phase 01 (`tests/phpunit/unit/Diagram/`, `tests/phpunit/integration/DiagramRepositoryTest.php`, `tests/phpunit/integration/DiagramRegistrationTest.php`, `tests/phpunit/integration/CapabilitiesTest.php`, `tests/phpunit/integration/MigrationTest.php`, `tests/phpunit/integration/UninstallPolicyTest.php`, `tests/e2e/playwright/tests/admin-menu.spec.ts`).
- ADR-019 (Container & Service Provider pattern) and ADR-020 (Usage Index schema & UpgradeRunner).

### Phase 00 — Development Environment and Risk Spikes (2026-08-01)

#### Added
- Executable `wp-env` local development environment (WP 7.0, PHP 8.3) on port 8888 with automated `wp:setup` lifecycle script (`tools/wp-env/after-start.mjs`).
- Toolchain configuration (`@wordpress/scripts`, PHPUnit, PHPCS, PHPStan, Jest, Bruno, Playwright).
- Spike 1–5 evidence and baseline tests for Mermaid pinning (11.4.1), Live Editor SPA build, component compatibility, controlled SVG media upload, and MCP Adapter companion integration.
- ADR-014 through ADR-018.
