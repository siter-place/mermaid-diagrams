# 5. Analysis of Supplied Sources and Examples

## 5.1 Evaluation method

Each source was evaluated for:

- functional relevance;
- architecture and maintainability;
- compatibility with WordPress and React;
- security posture;
- test maturity;
- dependency and license implications;
- suitability for direct reuse, adaptation, or inspiration only.

The recommendation is not to combine repositories wholesale. The plugin needs a coherent architecture and should selectively reuse compatible ideas or licensed code behind explicit internal interfaces.

## 5.2 `getdokan/plugin-ui`

Source:

- <https://github.com/getdokan/plugin-ui>
- <https://github.com/getdokan/plugin-ui/blob/main/.claude/commands/settings-integration.md>

### What it provides

`plugin-ui` is a reusable React component collection intended for WordPress plugin administration interfaces. Its component set includes common controls such as buttons, form elements, search, filters, list/presentation helpers, modals, notices, and provider-level theming. The supplied settings-integration guide documents a complete WordPress pattern: PHP-defined settings schema, REST controller, React mount point, provider setup, scoped Tailwind integration, section-based save, and PHPUnit REST tests.

### Strong ideas to adopt

- Root `ThemeProvider` and a consistent plugin application shell.
- Reusable search, filter, modal, badge, button, list, and toast components.
- Server-defined settings schema and server-source-of-truth values.
- Section-scoped settings updates rather than replacing unrelated settings.
- Re-fetch or accept normalized server response after save.
- CSS scoping under a plugin root.
- Targeted form-control resets to counter WordPress admin styles.
- REST controller tests based on `WP_Test_REST_TestCase`.
- Clear loading and error states.

### Risks and cautions

- It is a dependency sourced from GitHub rather than an assumed stable WordPress standard library. Pin a tag or commit and keep the lockfile.
- Its current package metadata and peer-dependency assumptions must be tested against the WordPress React runtime, especially during WordPress’s React 19 transition.
- Tailwind must not leak globally into WordPress admin.
- The settings command is a pattern for settings, not a complete data-management architecture for the diagram library.
- Avoid coupling the domain model directly to component-specific data structures.

### Reuse decision

**Use as the preferred admin UI foundation, behind a small local component adapter layer.** Run a compatibility spike before committing the full plugin to it.

## 5.3 `n3f/merpress`

Source: <https://github.com/n3f/merpress>

### What it provides

Merpress is a Gutenberg-oriented Mermaid plugin. It demonstrates a block-centric source editor and preview, Mermaid initialization in WordPress, block build tooling, and image export concerns. Its repository includes modern block files and uses WordPress scripts. Project history mentions updates to Mermaid and fixes related to PNG/canvas export.

### Strong ideas to adopt

- Simple block insertion and editing flow.
- Immediate preview close to the source editor.
- Conditional block assets instead of globally loading Mermaid.
- Practical lessons from SVG/PNG export and canvas limitations.
- Awareness that a Mermaid upgrade can require plugin-level rendering fixes.
- Gutenberg-native packaging and build approach.

### Weaknesses relative to this goal

- The core model is block-local rather than a reusable diagram library.
- It does not provide the required CPT, category/tag management application, shared references, conflict handling, or dedicated editor.
- The source tree includes legacy handling and is optimized for a much smaller scope.
- The package file includes Playwright as a dependency, but the visible scripts do not constitute the comprehensive test strategy required here.
- Copying its architecture would lead to block code becoming the center of the product, which is wrong for a shared diagram domain.

### Reuse decision

**Use as a behavioral reference and inspect selected rendering/export fixes. Do not use it as the plugin architecture.** Any copied GPLv3 code would affect distribution obligations and must be reviewed explicitly.

## 5.4 `terrylinooo/wp-mermaid`

Source: <https://github.com/terrylinooo/wp-mermaid>

### What it provides

This plugin demonstrates traditional WordPress integration for Mermaid through shortcode and block support. A valuable behavior is conditional asset loading only when Mermaid output is required.

### Strong ideas to adopt

- Do not enqueue Mermaid on pages that have no Mermaid diagram.
- Consider an optional shortcode migration/compatibility path if existing content needs it.
- Keep shortcode rendering and block rendering routed through the same underlying renderer if compatibility is added.

### Weaknesses relative to this goal

- The implementation is closer to a conventional procedural WordPress plugin with global state and includes, not a modular enterprise application.
- The referenced Mermaid version in repository documentation is old compared with the current Mermaid 11 line.
- It does not solve central library management, React administration, visual editing, optimistic concurrency, or rich front-end navigation.
- A global “load script” flag does not scale well to the planned component boundaries and script-module architecture.

### Reuse decision

**Adopt only the conditional-loading principle. Do not copy the architecture.** Shortcode compatibility should be a separately approved migration feature, not a default requirement.

## 5.5 `mermaid-js/mermaid-live-editor`

Source: <https://github.com/mermaid-js/mermaid-live-editor>

### What it provides

Mermaid Live Editor is the best product reference for code-first Mermaid authoring. It offers a mature source editor, live preview, configuration, sharing/export workflows, and broad diagram support. Its current project is a Svelte/SvelteKit application with its own build, runtime, routing, and dependencies rather than a reusable React editor component.

### Strong ideas to adopt

- Code and preview as the primary editing experience.
- Immediate syntax validation and diagnostics.
- Resizable or switchable code/preview workspace.
- Export from the current source and rendered SVG.
- Useful editor keyboard behavior.
- Mermaid configuration and theme preview.
- Clear separation between source and render result.
- Mature handling of the full Mermaid diagram family.

### Why direct embedding is not recommended

Embedding the whole SvelteKit application inside a React-based WordPress plugin would introduce:

- two UI frameworks and duplicate runtime concepts;
- a separate router/build/deployment model;
- more difficult WordPress nonce and capability integration;
- style and accessibility inconsistencies;
- cross-window communication if isolated in an iframe;
- larger bundles and slower editor load;
- complicated upgrade and license-notice maintenance;
- no solution for the requested visual editor.

A static iframe is technically possible, but should be treated as a rejected fallback unless time constraints override product quality.

### Recommended adaptation

Feature-port the required product behavior into the plugin’s React editor:

- use Mermaid’s public `parse` and `render` APIs;
- select a React-compatible code editor;
- implement WordPress REST persistence and permissions natively;
- share export and preview code with the block and library;
- retain attribution for any MIT-licensed code that is actually adapted.

### Visual editor finding

Mermaid Live Editor is fundamentally code-first. It does not supply the general bidirectional visual editor required by this plugin. Visual mode needs a separate adapter architecture.

### Reuse decision

**Use as the main UX and behavior reference. Feature-port, do not embed the complete application.**

## 5.6 `albingcj/mermaid-reactflow-editor`

Source: <https://github.com/albingcj/mermaid-reactflow-editor>

### What it provides

This project combines a Mermaid code editor with React Flow. Its README and structure demonstrate conversion of flowchart-oriented Mermaid constructs into nodes and edges, graphical editing operations, automatic layout, and export. The codebase uses React, TypeScript, Vite, Mermaid, React Flow, Dagre, Monaco, and image-export tooling.

Notable capabilities include:

- code-first source;
- flowchart parsing into React Flow structures;
- drag and position nodes;
- pan and zoom;
- multi-selection;
- alignment/distribution;
- duplicate/delete/lock/z-order actions;
- subgraph/group handling;
- automatic layout;
- PNG export with documented browser limitations.

### Strong ideas to adopt

- React Flow is a suitable visual canvas for the flowchart pilot.
- Keep Mermaid source as canonical rather than inventing a proprietary-only diagram format.
- Isolate parsing/conversion utilities from UI components.
- Model nodes, edges, and groups through an intermediate representation.
- Expose explicit visual editing capabilities rather than pretending every Mermaid feature is editable.
- Document export limits such as cross-origin resources and memory use.
- Use an automatic layout engine as an action, not as an uncontrolled rewrite on every change.

### Major limitations

- Flowchart conversion does not imply support for sequence, class, ER, state, Gantt, mindmap, timeline, or other Mermaid syntaxes.
- Mermaid is a text language; visual positions are not always represented in canonical source. A graphical layout can therefore diverge from generated Mermaid layout.
- Unknown directives, styling, class definitions, links, comments, and newer syntax can be lost by a naïve parser/serializer.
- The application contains capabilities beyond current product scope, including AI-related features.
- Directly adopting an entire demo application would couple the plugin to its assumptions and dependencies.

### Recommended adaptation

Create a `VisualEditorAdapter` interface and use the project as a research/reference implementation for a **flowchart adapter only**. Selectively adapt MIT-licensed parsing or editing utilities only after tests establish round-trip behavior and license notices are added.

### Reuse decision

**Use for the visual-editor proof of concept and selective MIT-licensed adaptation. Never present it as universal Mermaid visual editing.**

## 5.7 Mermaid official usage and API documentation

Sources:

- <https://mermaid.ai/open-source/config/usage.html>
- <https://mermaid.js.org/config/setup/mermaid/interfaces/Mermaid.html>
- <https://mermaid.js.org/config/schema-docs/config-properties-securitylevel.html>

### Relevant official behavior

Mermaid provides programmatic initialization, parsing, and rendering. The current API documentation recommends public `parse` and `render` methods rather than relying on deprecated internal APIs. `render` returns SVG and optional binding behavior. Mermaid’s default `securityLevel` is `strict`, which encodes HTML and disables click functionality; looser levels allow behavior that is inappropriate for untrusted WordPress authors by default.

### Recommended use

- `startOnLoad: false`;
- initialize once in a controlled service;
- call `parse` for validation and diagram-type detection;
- call `render` with a unique ID;
- preserve `strict` security level;
- allowlist configuration;
- suppress Mermaid’s direct error rendering when the plugin wants to present structured diagnostics;
- treat the SVG as generated output, not trusted author HTML;
- sanitize before export or persistence;
- pin Mermaid and test upgrades.

### Reuse decision

**This is the authoritative runtime API and security reference.**

## 5.8 jsDelivr Mermaid package links

Sources:

- <https://www.jsdelivr.com/package/npm/mermaid>
- <https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js>

### What they demonstrate

jsDelivr provides versioned Mermaid distribution files and can be convenient for prototypes or examples.

### Why runtime CDN loading is not the default

- production availability becomes dependent on a third party;
- CSP may block it;
- privacy and governance policies may prohibit it;
- version drift can break output;
- WordPress installations without outbound internet would fail;
- subresource integrity and module chunking complicate upgrades;
- test and release artifacts become less reproducible.

### Recommended use

- Use npm to pin Mermaid and bundle the required assets.
- Record the Mermaid version in diagnostics and cache keys.
- A CDN mode could be an advanced opt-in only if a real requirement appears, with an exact version and integrity controls.

### Reuse decision

**Do not use the unversioned CDN URL in production. Bundle Mermaid.**

## 5.9 WordPress Interactivity API

Source: <https://developer.wordpress.org/block-editor/reference-guides/interactivity-api/>

### Relevant capabilities

The Interactivity API is in WordPress core from 6.5 and uses script modules, block support metadata, stores, contexts, and directives. It is appropriate for front-end block interactions without shipping a React application to the public page.

### Recommended use here

- register `supports.interactivity` in `block.json`;
- load the front-end module through `viewScriptModule`;
- keep each block’s zoom/pan/fullscreen state in context;
- keep Mermaid module initialization shared in the store;
- use actions for toolbar operations;
- use server-rendered semantic markup and JSON payload;
- load only when the block is rendered.

### Limits

The API does not replace:

- PHP block registration and dynamic rendering;
- REST for persistence;
- React in Gutenberg or custom administration apps;
- Mermaid’s own rendering engine.

### Reuse decision

**Use for all published-page interaction.**

## 5.10 WordPress REST API and block metadata

Sources:

- <https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/>
- <https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/>

### Recommended use

- register blocks server-side from `block.json`;
- rely on conditional block asset enqueueing;
- use `render.php` for a dynamic block;
- use `viewScriptModule` for Interactivity API code;
- set `show_in_rest` for CPT and taxonomies;
- namespace custom routes;
- define schemas, sanitization, validation, and mandatory permission callbacks;
- use object capabilities through `current_user_can`.

### Clarification about “REST 100%”

Every mutable action initiated by a JavaScript client should use REST. WordPress registration, server rendering, activation, migrations, capability mapping, and lifecycle hooks cannot and should not be replaced with REST. This architecture fulfills the intent—decoupled client/server applications—without misusing the API.

## 5.11 WordPress `agent-skills`

Source: <https://github.com/WordPress/agent-skills>

### Relevant skills

The repository provides WordPress-specific guidance for coding agents. The exact list should be verified at implementation time, but the relevant areas include:

- project triage;
- plugin development;
- block development;
- REST API;
- Interactivity API;
- performance;
- PHPStan/static analysis;
- Playground/local environments;
- WP-CLI and operations;
- Abilities API/auditing where useful.

### Recommended development workflow

At the start of each implementation phase, the coding agent should load the relevant skill files and produce a short compliance checklist. Examples:

- bootstrap/data phase: project triage + plugin development + REST API;
- block phase: block development + Interactivity API;
- hardening phase: performance + PHPStan + security/audit skills;
- E2E phase: Playground/wp-env and testing guidance.

Skills guide implementation; they do not replace this specification or tests.

### Reuse decision

**Make agent-skills part of the coding-agent operating procedure and CI review checklist.**

## 5.12 Comparative decision table

| Source | Best contribution | Do not adopt | Decision |
|---|---|---|---|
| `plugin-ui` | Admin components, settings pattern, CSS isolation | Direct dependency without compatibility spike; settings architecture for all data | Use behind adapters |
| Merpress | Block UX, preview/export lessons | Block-centric product architecture | Behavioral reference |
| WP Mermaid | Conditional asset loading, possible legacy migration idea | Procedural/global architecture, old Mermaid baseline | Principle only |
| Mermaid Live Editor | Code-editor UX, live preview, broad Mermaid behavior | Whole SvelteKit app embedded in React plugin | Feature-port |
| Mermaid React Flow Editor | Flowchart visual editing, IR/conversion ideas | Claim of all-diagram visual support; wholesale demo app | Flowchart adapter research |
| Mermaid official docs | Parse/render/security contract | Internal/deprecated API dependence | Authoritative runtime source |
| jsDelivr | Distribution reference/prototyping | Unversioned runtime CDN | Bundle npm dependency |
| WP Interactivity API | Front-end controls and state | Admin persistence or Mermaid replacement | Use on front end |
| WP REST API | Decoupled CRUD and custom operations | Replacing PHP lifecycle/rendering | Use for all JS mutations |
| WP agent-skills | Agent-specific WordPress guardrails | Treating skills as executable product requirements | Use during implementation |

## 5.13 License considerations

Before copying code, record the exact source file, commit, license, and modifications.

- Mermaid and Mermaid Live Editor are generally MIT-licensed projects; confirm included file headers and repository license at the pinned commit.
- `mermaid-reactflow-editor` is presented as MIT; confirm before adaptation.
- `plugin-ui` package metadata indicates GPL-compatible licensing; confirm repository license and distribution obligations at the pinned version.
- Merpress and WP Mermaid use GPL-family licensing. Copying code may impose GPL compatibility requirements on the combined work, which is normally compatible with a WordPress plugin but must still be documented.
- Dependencies such as Monaco, CodeMirror, React Flow, Dagre, sanitizers, and export libraries require a generated third-party notices inventory.

Use automated license scanning in CI and include a `THIRD-PARTY-NOTICES.md` file in the source repository and, where required, the release ZIP.

## 5.12 Updated synthesis after final decisions

- Mermaid Live Editor changes from “feature inspiration” to a pinned, adapted Svelte application. Preserve upstream behavior where possible and isolate WordPress REST/persistence through an adapter.
- `getdokan/plugin-ui` remains a design/architecture starting point for the React Library, especially providers, settings integration, REST synchronization, and scoped CSS. It is not allowed to dictate domain architecture.
- Mermaid React Flow Editor remains the strongest visual-editor reference, but its AST/subset and round-trip limitations require a flowchart-only adapter and loss report.
- Merpress and WP Mermaid remain implementation examples only. Their shortcode/static-script or single-block assumptions do not cover a central library, REST workflow, abilities, usage index, or enterprise permissions.
- jsDelivr is a distribution reference, not the runtime delivery strategy. Mermaid is bundled and version-pinned locally.
- WordPress Agent Skills and Bruno Agent Skills define the coding-agent workflow; they are installed separately and not vendored.
