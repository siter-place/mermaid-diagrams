# Mermaid Live Editor Adaptation Boundary

## Purpose

The dedicated editor is not a new editor inspired by Mermaid Live Editor. It is a pinned, adapted build of the upstream Svelte/SvelteKit application, compiled to static assets and mounted on a WordPress administration page.

## Runtime topology

```text
WordPress admin page
  └─ PHP bootstrap data (REST root, nonce, user/capabilities, route, locale)
      └─ Svelte application shell
          ├─ upstream-like source editor
          ├─ Mermaid preview/runtime adapter
          ├─ WordPress metadata/taxonomy/status panels
          ├─ coordinated save adapter
          ├─ revisions/conflict/local-recovery adapter
          ├─ AI action adapter
          └─ later visual-editor adapter slot
```

No SvelteKit server runs in production. Build-time routing, stores, and server assumptions from upstream must be removed or replaced by WordPress adapters.

## Upstream preservation policy

Preserve upstream behavior where it reduces maintenance:

- source editing, diagnostics, preview, Mermaid configuration model, keyboard behavior, and export logic;
- the editor dependency selected by the pinned upstream release;
- internal component boundaries that can remain framework-local.

Replace or isolate:

- URL/share-state persistence;
- local-only document model;
- SvelteKit server endpoints and deployment assumptions;
- unrestricted configuration that conflicts with plugin security policy;
- direct network integrations;
- generic save behavior.

## WordPress adapter contract

The page bootstrap provides only serializable configuration:

- `restRoot`, `restNonce`, `diagramId`, `mode` (`create|edit|duplicate`);
- current user ID, capability flags, locale/timezone;
- maximum source/SVG sizes and allowed Mermaid configuration;
- pinned Mermaid/runtime versions;
- feature flags for AI and later visual editing;
- return URLs.

The Svelte app talks to a framework-neutral TypeScript REST client. It never calls WordPress PHP globals, manipulates posts directly, or stores provider credentials.

## Save pipeline

1. Mermaid `parse()` validates source.
2. The pinned renderer generates SVG.
3. Client sanitizer applies the project SVG allowlist.
4. SHA-256 hashes bind source, validation receipt, and SVG envelope.
5. The Svelte app sends metadata, terms, status, source, receipt, SVG, dimensions, expected version, and idempotency key in one create/update mutation.
6. Server authorization/schema/security validation runs.
7. The application command stages media, saves the diagram/terms/status, assigns featured image, and cleans old derived media.
8. The UI adopts only the normalized response as its new baseline.

Failure retains local recovery data and the previous server version. The UI must never show a completed save while featured-media persistence failed.

## Upgrade workflow

Phase 00 pins the upstream commit, license, Node requirement, Mermaid version, build inputs, and patch series. Every upgrade must:

1. rebase/reapply the adaptation patch set;
2. rebuild static WordPress assets;
3. run upstream-relevant unit tests;
4. run plugin component, Playwright, accessibility, visual, REST, and save-conflict tests;
5. review bundle/license changes;
6. record the result in the source lock and changelog.

A fork is acceptable only when the patch set cannot remain maintainable. The plugin must still document upstream provenance.
