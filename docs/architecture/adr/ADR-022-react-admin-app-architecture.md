# ADR-022: React Admin Application Architecture

## Status

Accepted (Phase 04)

## Context

Phase 04 delivers the Diagram Library shell and Settings UI as React applications mounted on WordPress admin pages. Phase 00 (ADR-016) selected native `@wordpress/components` and `@wordpress/element` over `getdokan/plugin-ui`. The architecture must follow plugin-ui-*inspired* patterns (provider stack, section-scoped settings saves, scoped CSS) without adding external UI dependencies.

## Decision

1. **Dual webpack entry points** under `assets/src/apps/`:
   - `diagram-library/index.tsx` → `#mdm-diagram-library-root`
   - `settings/index.tsx` → `#mdm-settings-root`
   - Built artifacts: `build/admin/library/index.{js,css}` and `build/admin/settings/index.{js,css}`

2. **PHP bootstrap contract** via `ScreenBootstrapData` exposed as `window.mdmAdminBootstrap` before script execution. Includes REST root, nonce, capabilities, routes, defaults, and i18n strings. No diagram source or secrets.

3. **Provider composition** in `AppProviders`: `BootstrapProvider`, `NoticesProvider` (SnackbarList), `AppErrorBoundary`, scoped `mdm-app.css`.

4. **REST client reuse**: Admin apps consume the existing `assets/src/shared/api/client.ts` via `@wordpress/api-fetch` with WordPress-enqueued dependencies.

5. **List table markup**: Use native HTML `<table class="widefat striped">` inside `.mdm-app-root` because `@wordpress/components` does not ship Table primitives in the pinned version.

6. **Settings permission**: REST and bootstrap use `manage_mdm_settings` (not `manage_options`) per product capability matrix.

7. **URL state**: Library pagination syncs `paged` and `per_page` query args; filter params are parsed but deferred to Phase 05.

## Consequences

- Admin screens load only on matching hook suffix and capability checks (`AdminAssets`).
- Phase 05 can extend URL parser, table columns, and row actions without changing bootstrap shape.
- Visual regression baselines live under `tests/e2e/playwright/__screenshots__/`.
- Settings UI remains server-authoritative with section-level PATCH normalization.

## References

- ADR-016 (WordPress components adoption)
- `docs/04-frontend-applications-and-ux.md` §4.1–4.4
- `docs/plans/04-diagram-library-shell-and-settings-ui/`
