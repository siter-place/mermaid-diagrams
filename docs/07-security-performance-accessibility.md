# 7. Security, Performance, Accessibility, and Operational Quality

## 7.1 Threat model

The plugin processes author-controlled diagram source and converts it to SVG in privileged administration screens and public pages. Relevant threats include:

- stored cross-site scripting through labels, HTML, links, directives, SVG, or configuration;
- unauthorized REST access to private/draft source;
- privilege escalation through weak capability mapping;
- cross-site request forgery for mutations;
- denial of service through extremely large or complex diagrams;
- browser memory exhaustion during very large SVG rendering/export;
- external-resource tracking or cross-origin failures;
- stale-write data loss;
- dependency compromise or unreviewed CDN drift;
- CSS leakage in WordPress admin;
- accidental disclosure through preview or usage endpoints.

## 7.2 Mermaid security policy

Required defaults:

- `securityLevel: 'strict'`;
- `startOnLoad: false`;
- lock security-critical configuration on the server/client normalization layer;
- disable author-defined click callbacks and JavaScript behavior;
- reject or neutralize unsafe HTML configuration;
- avoid external image/resource loading by default;
- do not invoke `bindFunctions` unless a reviewed feature specifically requires safe link behavior;
- sanitize SVG before export or persistence;
- keep Mermaid pinned to an audited version and update through controlled dependency PRs.

A future relaxed mode must be an administrator-level security feature with an explicit risk assessment, not a per-block toggle.

## 7.3 Source handling

- Store Mermaid as plain text.
- Escape it when rendered into HTML.
- Serialize render payload with a trusted JSON encoder and hex-escape HTML-sensitive characters.
- Never concatenate source into script code.
- Avoid putting large source in HTML attributes.
- Do not accept arbitrary uploaded SVG as a substitute for source.
- Do not treat generated SVG as inherently safe just because Mermaid produced it.
- Source download headers must prevent MIME confusion and use a safe filename.

## 7.4 REST security

Every custom route must include:

- `permission_callback`;
- object-level capability checks;
- nonce-authenticated admin requests;
- argument schemas;
- sanitization and validation;
- bounded pagination and array sizes;
- stable error responses without stack traces;
- privacy-aware projections;
- tests for direct unauthorized calls.

Use capabilities rather than checking `is_user_logged_in()` or role names.

Sensitive endpoints:

- raw source detail;
- private preview;
- usage references;
- revisions;
- bulk status/delete;
- settings;
- export of non-public source.

## 7.5 Capabilities

Recommended capability set:

```text
edit_mdm_diagram
read_mdm_diagram
delete_mdm_diagram
edit_mdm_diagrams
edit_others_mdm_diagrams
publish_mdm_diagrams
read_private_mdm_diagrams
delete_mdm_diagrams
delete_private_mdm_diagrams
delete_published_mdm_diagrams
delete_others_mdm_diagrams
edit_private_mdm_diagrams
edit_published_mdm_diagrams
manage_mdm_diagram_terms
manage_mdm_settings
```

Map meta capabilities through the CPT. Assign defaults on activation but never overwrite administrator-customized role mappings on each request.

## 7.6 Complexity limits

Configurable limits should include:

- maximum source bytes/characters;
- maximum diagrams rendered concurrently in admin previews;
- maximum bulk operation IDs;
- maximum pagination size;
- maximum SVG source/render complexity and dimensions;
- Mermaid-supported complexity controls such as text or edge limits where the selected version exposes them;
- debounce and request cancellation;
- optional preview timeout.

A single failed or expensive diagram must not prevent other blocks on the page from initializing.

## 7.7 Dependency security

- Pin exact dependency versions through a lockfile.
- Use a dependency update bot with grouped, reviewed updates.
- Run npm and Composer vulnerability audits in CI, while triaging false positives.
- Generate a software bill of materials for enterprise releases when required.
- Record third-party licenses.
- Do not load unversioned runtime CDN assets.
- Verify the `plugin-ui` commit/tag and Mermaid version in build metadata.
- Test a clean reproducible build.

## 7.8 Performance budgets

Set measurable budgets during the architecture spike. Proposed starting targets:

- no Mermaid JS on pages without the block;
- library initial response contains summaries only, not full source/SVG;
- editor-only code editor and visual-editor chunks load only on the editor screen/tab;
- search requests debounce and cancel stale requests;
- list page renders at most the configured page size;
- full preview renders on demand;
- multiple front-end blocks share one Mermaid runtime promise;
- no synchronous server-side scan of the entire site during normal block rendering;
- avoid long activation work;
- maintain responsive typing for source at the configured maximum.

Record actual bundle sizes and key timings in CI or release notes.

## 7.9 Asset-loading strategy

### Front end

- block metadata conditionally enqueues styles and `viewScriptModule` only when the block appears;
- Mermaid is loaded once, preferably in the same built module graph or a dynamically imported chunk;
- IntersectionObserver may delay rendering off-screen diagrams;
- do not delay a diagram in the initial viewport enough to cause confusing blank content.

### Gutenberg

- load the compact block editor bundle with the block;
- lazy-load advanced code editor and full Mermaid renderer when the block is inserted or selected;
- do not load the visual editor in Gutenberg.

### Library

- load list shell immediately;
- load Mermaid/preview code only when a preview opens or a card enters the viewport;
- never render every diagram in a large table.

### Dedicated editor

- load code editor and Mermaid renderer on this screen;
- lazy-load React Flow and flowchart adapter only after Visual tab selection.

## 7.10 Caching and invalidation

Safe cache candidates:

- detected diagram type;
- source hash;
- validation summary;
- generated sanitized thumbnail, if implemented;
- usage counts with a TTL;
- settings schema.

Cache keys include plugin, Mermaid, adapter, and schema versions. Invalidate on:

- source or render-config update;
- status change;
- relevant settings change;
- Mermaid dependency update;
- visual-adapter update;
- trash/restore/delete.

Do not cache authorization-sensitive detail under a public key.

## 7.11 Accessibility goals

Target WCAG 2.2 AA for plugin UI and controls.

### Diagram semantics

- Render each block inside `figure` where appropriate.
- Title/description remain as real HTML and are associated with the viewport.
- Add SVG `<title>` and `<desc>` where possible.
- Authors should be encouraged to write a useful description because complex diagrams are not fully accessible from SVG structure alone.
- Provide source download only as a supplement, not as the sole accessible alternative.
- Optionally support a structured text alternative in a later release.

### Keyboard

- Every toolbar action is a native button.
- No keyboard trap in code editor, canvas, preview, or fullscreen.
- Modals and fullscreen views trap focus only while open and restore it on close.
- Visual canvas selection and core node operations need keyboard alternatives before the feature leaves Beta.
- Shortcuts are documented and avoid browser/WordPress conflicts.

### Focus and announcements

- Visible focus indicator.
- Syntax errors summarized in an `aria-live` region without announcing on every keystroke.
- Save, conflict, and export status announced.
- Zoom percentage announced after user action.
- Loading states use appropriate busy semantics.

### Color and motion

- Controls meet contrast requirements in supported WordPress admin schemes.
- Diagram meaning should not rely solely on plugin-added color.
- Respect `prefers-reduced-motion` for transitions and canvas animation.

## 7.12 Responsive behavior

- Block viewport works from narrow mobile content widths to wide layouts.
- Toolbar wraps or uses an overflow menu.
- Admin library switches preview drawer to full-screen modal on narrow screens.
- Dedicated editor switches from split panes to tabs/stacking.
- Touch targets meet minimum sizing guidance.
- Very wide diagrams remain pannable without expanding the entire page horizontally.

## 7.13 Internationalization and RTL

- Use `@wordpress/i18n` in JavaScript and WordPress localization functions in PHP.
- Do not build sentences through string concatenation.
- Use plural APIs.
- Apply logical CSS properties.
- Test admin UI in an RTL locale.
- Diagram source direction is a Mermaid concern and must not be automatically rewritten because the WordPress locale is RTL.

## 7.14 Privacy

The base plugin should not send source, titles, usage, or telemetry to external services.

- No CDN requests by default.
- No AI generation by default.
- No analytics without a separate documented opt-in.
- Export occurs locally in the browser unless an explicit enterprise rendering service is later configured.
- Usage reports are visible only to authorized users.

Document data stored in the WordPress privacy policy helper only if it constitutes user-related data beyond normal authored content.

## 7.15 Operational diagnostics

Settings diagnostics may show:

- plugin version;
- database schema version;
- bundled Mermaid version;
- `plugin-ui` version/commit;
- WordPress/PHP compatibility status;
- cache clear action;
- count of diagrams by status;
- visual adapter versions;
- REST route availability self-check;
- last migration result.

Do not expose server paths, secrets, nonces, source content, or stack traces in a downloadable diagnostic report by default.

## 7.16 Failure isolation

- One diagram render error affects only that instance.
- One failed bulk item does not roll back successful unrelated items unless an atomic operation is explicitly required.
- Network failure preserves editor source in memory and optionally local recovery storage with clear privacy behavior.
- Invalid stored legacy source remains editable.
- Missing categories/tags do not make a diagram unreadable.
- If the visual adapter fails, code mode remains fully usable.
- SVG/source are the supported export formats; any future PNG feature must fail independently.

## 7.17 Compatibility and React transition risk

WordPress development in July 2026 is actively asking plugin authors to test React 19 compatibility. `plugin-ui` currently needs a dedicated compatibility check against the WordPress-provided React environment.

Required spike tests:

- mount all required `plugin-ui` components under current WordPress stable;
- enable the React 19 experimental/runtime path available in the current Gutenberg cycle;
- verify no bundled duplicate React;
- inspect console warnings and deprecated WordPress component props;
- run component and Playwright tests under both conditions;
- either pin a known-good `plugin-ui` revision, patch through the local adapter layer, or replace incompatible primitives.

This is a release-blocking dependency gate.

## 7.18 SVG featured-image security

SVG media support is scoped to coordinated diagram mutations and the repair-only thumbnail regeneration endpoint. The endpoint requires the appropriate diagram-edit and media-upload capabilities, verifies source-hash provenance, parses/sanitizes SVG with a maintained allowlist, removes scripts/events/external resources/unsafe URLs/foreign objects, and stores dimensions. It does not enable arbitrary SVG upload site-wide.

## 7.19 AI and agent security

- Provider credentials stay in WordPress Connectors.
- Prompts and diagram source are sent only after explicit user action and policy checks.
- AI output is untrusted text and passes identical validation/security rules.
- Ability permission callbacks are mandatory and tested independently of MCP.
- MCP exposure is allowlisted; read/write/destructive annotations are accurate.
- Logs avoid raw source by default and never include connector secrets.

## 7.20 Validation trust boundary

Client validation protects normal UX but is not an adversarial server proof. Autonomous writes require the Mermaid-JS worker profile. If unavailable, fail closed and return a candidate that a human can open and validate in the Live Editor.
