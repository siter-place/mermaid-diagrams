---
name: wp-admin-ui-ux
description:
  Use when building, changing, or reviewing WordPress plugin admin interfaces,
  especially settings pages, grid/table/DataViews screens, create/edit/detail
  pages, React admin apps, shared admin components, admin CSS, Playwright E2E
  screenshots, visual baselines, or any UI/UX polish task. Triggers for admin
  screen bootstraps, settings REST/UI contracts, list/grid code, edit forms,
  data mutations, and screenshot-driven visual review.
---

# WP Admin UI UX

## Overview

Build WordPress-native plugin admin screens that are clear, dense, accessible,
trustworthy, and visually verified. Use this skill to turn general frontend
design judgment into WordPress admin patterns using WPDS,
`@wordpress/components`, `@wordpress/dataviews`, and `getdokan/plugin-ui` as a
bundled architectural and UX reference.

## Start Here

1. Read the local repo instructions, active plan/task context, and any
   phase-specific "do not implement later phases" exclusions before editing.
2. Run WordPress repo triage when available. Prefer local scripts and tooling
   paths from the current project instead of hard-coded commands.
3. Load adjacent skills as needed:
   - `wpds` for WordPress Design System component/token choices.
   - `wp-plugin-development` for settings, capabilities, nonces, sanitization,
     and admin bootstraps.
   - `wp-rest-api` for UI data contracts and permission callbacks.
   - `wp-performance` when DataViews or list screens add queries, pagination,
     filtering, or large payloads.
   - `bruno-test-writer` when REST contracts need API regression coverage.
4. If the WPDS MCP server is available, read relevant WPDS component and token
   docs before choosing UI components. If unavailable, inspect the installed
   `@wordpress/*` packages and existing local UI patterns; do not browse for
   canonical WPDS docs unless the user explicitly asks.
5. Read `references/plugin-ui-architecture-ux.md` before using plugin-ui ideas.
   Treat plugin-ui as a bundled reference unless the current project explicitly
   approves adding it as a dependency.

## Reference Loading

Load only the reference files needed for the current task:

- Read `references/admin-page-patterns.md` when creating or changing settings
  pages, grid/list/table/DataViews screens, filters, bulk actions, pagination,
  create/edit/detail pages, preview panels, or data-heavy admin views.
- Read `references/plugin-ui-architecture-ux.md` when using plugin-ui-inspired
  provider composition, settings schemas, DataViews wrappers, theme tokens, CSS
  scoping, field patterns, or WordPress hook extensibility.
- Read `references/wp-admin-ux-rules.md` before any visual design or UX review
  of a WordPress admin screen.
- Read `references/screenshot-visual-loop.md` for every UI change that can be
  visually inspected, including small CSS/component changes.

## Workflow

1. Identify the admin job.
   - Name the user role, screen, primary task, and decision the screen supports.
   - Prefer operational clarity over marketing-style flourish. Admin screens
     should feel calm, scannable, and fast to repeat.
2. Inspect the current implementation.
   - Read the React component, CSS, REST client/schema, tests, and existing
     screenshots for the target screen.
   - Find the existing route, capability gate, nonce/bootstrap data, i18n text
     domain, and loading/error/empty states.
3. Choose WordPress-native components and page patterns.
   - Prefer `@wordpress/components`, `@wordpress/icons`, `@wordpress/dataviews`,
     and established local adapters.
   - Use plugin-ui patterns for schema-driven settings, scoped theme variables,
     field descriptions, DataViews namespace/hooks, destructive confirmations,
     and server-authoritative saves.
   - For create/edit pages, use explicit form sections, dirty-state handling,
     validation near fields, preview/side panels only when useful, and
     capability-aware primary actions.
4. Implement incrementally.
   - Keep contracts explicit: settings fields map to REST schema keys, view
     state maps to query params, and row actions map to capability-checked
     REST/application services.
   - Add or update focused tests before declaring a UI behavior complete.
5. Run the screenshot loop.
   - For visual UI changes, complete at least three analyze -> improve ->
     recapture cycles.
   - Use Playwright screenshots as evidence. Do not claim visual quality from
     code inspection alone.
6. Close with evidence.
   - Report commands run, screenshot paths inspected, remaining UX risks, and
     any deferred items mapped to a later phase.

## Hard Rules

- Never bypass WordPress capabilities, REST nonces, permission callbacks, or
  server-side validation to make a UI flow work.
- Never bypass the product's domain invariants or make browser state the only
  source of truth for persisted data.
- Never call AI, payment, email, or other external providers directly from admin
  UI code unless the current product architecture explicitly requires it.
- Never add plugin-ui, Tailwind, or any third-party UI dependency only because
  this skill mentions it. Treat dependency changes as architecture/license
  decisions.
- Always provide descriptive help text for settings fields unless the field is
  self-evident and non-destructive.
- Always internationalize visible strings in source code with the current
  plugin's text domain.
- Always include loading, empty, error, success, disabled, saving, dirty, and
  permission-denied states when the workflow can reach them.
- Always use screenshot evidence for admin UI visual changes. If Playwright
  cannot run, state the exact blocker and do not mark visual review complete.

## Validation Commands

Prefer existing project scripts and target the smallest relevant surface. Common
examples:

```bash
npm run lint:js
npm run lint:css
npm run build
npm run test:unit
npm run test:e2e
npx playwright test
```

Use whatever Playwright screenshot directory the current project configures.
Inspect the actual files produced by the run before deciding the UI is
acceptable.

## Output Expectations

When this skill is used, final work should include the UI intent, files changed,
screenshot cycles completed, screenshot artifacts inspected, tests run, and any
remaining visual or accessibility risk. For review-only tasks, lead with
findings and cite file/line/screenshot evidence.
