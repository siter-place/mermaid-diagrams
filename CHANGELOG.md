# Changelog

All notable changes to this project are documented in this file.

## [Unreleased]

## [1.4.1] - 2026-08-01

### Changed

- Redesigned admin UI to WordPress DataViews pattern with Table/Grid view toggle, Appearance settings popover, compact FilterBar, structured modal layouts (title/content/footer), and custom admin menu SVG icon.

## [1.4.0] - 2026-08-01

### Changed

- Phase 05 diagram library search, filters, bulk actions, preview panel, quick-create, preview/duplicate REST endpoints, and shared DiagramViewport.

## [1.3.1] - 2026-08-01

### Changed

- Overhauled Settings navigation (vertical tab layout with icons), WPDS Card panels, field help text, status badges, pagination summaries, and persistent UI/UX quality rules.

## [1.3.0] - 2026-08-01

### Added
- React diagram library admin app with paginated table shell, loading/empty/error states, and URL pagination sync.
- React settings admin app with six section forms, dirty/save flow, runtime diagnostics, and permission gate.
- PHP admin bootstrap (`AdminRoute`, `ScreenBootstrapData`, `AdminAssets`) and `window.mdmAdminBootstrap` contract.
- ADR-022 React admin application architecture.

### Changed
- Settings REST permission aligned to `manage_mdm_settings` capability.
- Admin library page now mounts React shell instead of Phase 01 placeholder notice.

## [1.2.0] - 2026-08-01

### Changed

- Implemented packages/mermaid-runtime, server-side validation receipts enforcement (15min TTL), Node.js validation worker, shared source constraints policy, source/SVG download routes, corpus parity tests, and Playwright render harness.

## [1.1.0] - 2026-08-01

### Changed

- Implemented mdm/v1 REST API endpoints, optimistic concurrency control (_mdm_version_token), idempotency handling, settings management, shared TypeScript contracts, and expanded Bruno REST test collection.

## [1.0.0] - 2026-08-01

### Changed

- Initial baseline release 1.0.0 establishing plugin kernel, domain model, storage, REST contracts, and toolchain.
