# 4. Frontend Applications and UX Specification

## 4.1 Shared application principles

The Gutenberg React application, Diagram Library React application, and Mermaid Live Editor Svelte application should look and behave like one plugin even though they have separate entry points.

Shared requirements:

- use `plugin-ui` primitives where they improve consistency;
- use a local design-token layer and a scoped root class such as `.mdm-app`;
- do not leak Tailwind utilities or resets into the WordPress administration screen;
- use `ThemeProvider` at each application root;
- use WordPress notices only for screen-level messages and a consistent toast system for short-lived action feedback;
- preserve URL state for library filters and editor record identity;
- show skeleton/loading, empty, error, permission, and offline/transient states explicitly;
- use WordPress capability data returned by REST rather than checking role names;
- support keyboard navigation and visible focus;
- use the same diagram renderer, export utilities, error mapping, and DTO types across all apps.

## 4.2 UI-library integration strategy

The supplied `plugin-ui` settings integration document is a strong model for:

- installing and mounting the React application;
- using a root provider;
- keeping the server as the settings source of truth;
- section-scoped settings payloads;
- replacing local values with normalized server values after save;
- isolating Tailwind styles under a plugin root;
- resetting WordPress form-control conflicts only inside that root;
- presenting save success/error through a consistent toast.

It should not be copied mechanically into the diagram library. The library is a resource-management application, not a settings form. Reuse the integration principles and components, while giving the library its own query, mutation, selection, and preview state.

Recommended local adapter components:

- `MdmButton`
- `MdmIconButton`
- `MdmSearchInput`
- `MdmModal`
- `MdmConfirmDialog`
- `MdmToast`
- `MdmFilterBar`
- `MdmBadge`
- `MdmEmptyState`
- `MdmErrorState`
- `MdmPagination`
- `MdmTermPicker`

These adapters absorb library upgrades and accessibility corrections.

## 4.3 React application 1: Gutenberg block

### 4.3.1 Entry and registration

Register through `block.json` and `registerBlockType`. Use standard WordPress editor packages and do not bundle React independently.

Suggested source modules:

```text
apps/block-editor/
├── index.ts
├── edit.tsx
├── save.tsx                    # returns null or dynamic-block placeholder
├── attributes.ts
├── transforms.ts
├── components/
│   ├── EmptyChooser.tsx
│   ├── InlineEditor.tsx
│   ├── ReferencePreview.tsx
│   ├── DiagramSelector.tsx
│   ├── SaveToLibraryDialog.tsx
│   ├── BlockToolbar.tsx
│   ├── InspectorControls.tsx
│   └── EditorPreview.tsx
├── hooks/
│   ├── useDiagramSearch.ts
│   ├── useDiagramDetail.ts
│   ├── useCreateDiagram.ts
│   └── useEditorRender.ts
└── state/
    └── blockUiReducer.ts
```

### 4.3.2 Empty state

The empty block must immediately explain the two models:

- **Create inline diagram** — belongs only to this post.
- **Choose from library** — reusable and centrally maintained.

Do not show a blank code textarea with no explanation.

Provide starter templates for common Mermaid types after the user chooses inline mode. Template insertion is explicit and can be undone.

### 4.3.3 Inline editing surface

Recommended block layout:

- title field;
- optional description field;
- source editor;
- validation status;
- preview below or beside source depending on width;
- compact actions for larger editor, download, and save to library.

The in-block source field must remain lighter than the dedicated editor. Use a textarea or small WordPress-compatible source input and do not load the Mermaid Live Editor bundle for every post. A button may open the dedicated editor for advanced work.

### 4.3.4 Library selector

The selector should use an accessible modal with:

- search field focused on open;
- category, tag, type, and status filters;
- paginated results;
- selected-item state;
- preview area;
- confirm and cancel actions.

Search behavior:

- debounce approximately 250–400 ms;
- cancel stale requests;
- retain the previous result list while a subsequent page loads where helpful;
- distinguish “no diagrams exist” from “no match for filters”;
- offer “Create new diagram” if capability permits.

### 4.3.5 Reference preview

The block displays:

- linked diagram title and type;
- rendered preview;
- modified timestamp;
- shared-reference indicator;
- editor-only warning if source is invalid or unavailable;
- link to dedicated editor;
- refresh and replace actions.

The preview refreshes when:

- the block is selected after returning from the dedicated editor;
- the editor data store invalidates the diagram query;
- the user clicks refresh;
- post editor reloads.

Avoid background polling.

### 4.3.6 Save-to-library dialog

Fields:

- title, required;
- description;
- category;
- tags;
- desired status, limited by capability;
- explanation that the block will become a shared reference.

On submit:

1. generate an idempotency key;
2. disable duplicate submit;
3. POST source and metadata;
4. receive normalized detail;
5. set `mode=reference`, `diagramId`, and presentation attributes;
6. clear canonical inline source after the block update is safely committed;
7. show success with edit link.

If step 5 fails locally after the server created a record, retain the returned ID and offer recovery rather than creating another record.

### 4.3.7 Block transformations

Support:

- inline → reference through Save to library;
- reference → inline through Detach copy;
- optionally transform a Mermaid fenced code block or compatible legacy block after a migration spike;
- deprecated attribute migrations between plugin versions.

Every destructive transform has undo support where Gutenberg provides it.

## 4.4 React application 2: Diagram Library

### 4.4.1 Admin route

Recommended WordPress admin URLs:

- `admin.php?page=mdm-diagrams`
- `admin.php?page=mdm-diagram-editor&diagram=123`
- `admin.php?page=mdm-diagram-editor&action=new`
- `admin.php?page=mdm-settings`

The library app owns the page below the standard WordPress admin header. It should not attempt to replace global admin navigation.

### 4.4.2 Page structure

```text
Page header
- Diagrams
- Add diagram
- optional import action
- view switch

Filter/search region
- search
- category
- tag
- type
- status
- author
- sort
- reset filters

Content region
- bulk-action bar when selected
- table/list at launch; grid is a later enhancement
- pagination

Preview side panel / modal
- rendered diagram
- metadata
- usage summary
- edit / duplicate / trash / download
```

### 4.4.3 List view

Recommended columns:

- selection checkbox;
- title with type badge and short description;
- category;
- tags;
- status;
- author;
- modified;
- usage count when efficiently available;
- row actions.

Avoid showing raw Mermaid source in the table.

Default sort: modified descending. Persist user preference locally or through a user setting only after the base flow is stable.

### 4.4.4 Grid view

Grid view is optional for the first release. When implemented, cards show:

- preview thumbnail loaded lazily;
- title;
- type;
- category/tags summary;
- status and modified date;
- selection control and actions.

A future grid must remain paginated and use featured SVG thumbnails; it must not render dozens of live Mermaid instances. Grid work is outside version 1.0.

### 4.4.5 Filter state

Represent state in the URL:

```text
?page=mdm-diagrams&search=auth&category=7&type=flowchart&status=publish&paged=2
```

Benefits:

- browser back/forward works;
- links can be shared among authorized users;
- refresh preserves context;
- Playwright tests are deterministic.

Use a single parser/serializer for query state.

### 4.4.6 Preview panel

Open without changing the current result list. The panel includes:

- loading state;
- invalid-source state;
- diagram viewport with navigation;
- title and description;
- categories and tags;
- type, status, author, dates;
- usage count/link;
- source/SVG downloads according to permission;
- edit, duplicate, trash/restore.

On small screens, use a full-screen modal rather than a narrow side drawer.

### 4.4.7 Bulk operations

Selection behavior:

- select row;
- select page;
- optionally select all matching results only after a later design, because it requires a server-side query token and explicit explanation.

Initial operations should apply only to selected IDs.

Bulk category UI must distinguish:

- Add categories
- Remove categories
- Replace categories

“Move” alone is too ambiguous when multiple categories are permitted.

After completion, show a summary and keep failed items selected for correction.

### 4.4.8 Category and tag management

A focused term-management modal or settings subpage can provide:

- category tree;
- create/rename/delete;
- parent change;
- usage count;
- tag search/create/rename/delete;
- merge tags as a later feature.

Deleting a term follows WordPress relationship behavior and does not delete diagrams.

## 4.5 Svelte application: Dedicated Mermaid Live Editor

### 4.5.1 Application state machine

Use explicit states instead of scattered booleans:

```text
booting
loading
ready.clean
ready.dirty
validating
saving
saved
validation_error
network_error
conflict
not_found
forbidden
```

Parallel substate can track preview rendering, but saving and conflict transitions must remain unambiguous.

### 4.5.2 Editor session model

```ts
interface EditorSession {
  record: DiagramDraft;
  baseline: DiagramDetail | null;
  expectedVersion: string | null;
  dirtyFields: Set<string>;
  validation: ValidationState;
  preview: PreviewState;
  save: SaveState;
  visual: VisualSessionState;
}
```

Server response after save replaces `baseline` and normalized fields. The application should not assume the server stored exactly the submitted labels, terms, or status.

### 4.5.3 Live Editor source surface

The plugin does not select or build an independent CodeMirror/Monaco layer. It retains the source-editing implementation required by the pinned Mermaid Live Editor release and encapsulates it inside the Svelte application. The Phase 00 spike verifies static bundling, CSP, keyboard behavior, large-source responsiveness, and upgradeability.

Acceptance gate:

- lazy load only on the diagram editor screen;
- no separate React runtime inside the Svelte application;
- WordPress admin CSP compatibility;
- accessible labels and keyboard behavior;
- configured maximum source sizes remain responsive;
- Mermaid diagnostics are presented without relying on unstable editor internals.

### 4.5.4 Workspace behavior

- Resizable code/preview split, with a reset layout action.
- Preview updates after debounce, not every keypress synchronously.
- A monotonically increasing generation ID ensures old render results cannot replace new ones.
- When current source is invalid, keep the last valid preview only if it is prominently labeled “Preview of last valid version.”
- Save shortcut uses platform conventions and prevents browser save behavior.
- Unsaved changes trigger WordPress-compatible navigation protection.

### 4.5.5 Metadata panel

Fields:

- title;
- description;
- category multi-select or single-primary policy;
- tags;
- status;
- safe render defaults;
- read-only detected type and source hash in advanced details.

Metadata changes participate in dirty tracking and conflict comparison.

### 4.5.6 Conflict experience

On HTTP 409:

- stop autosave/retry;
- preserve local draft;
- fetch current server version;
- show metadata differences and source diff;
- offer:
  - reload server version;
  - copy local source to clipboard/download;
  - duplicate local draft as new diagram;
  - overwrite only for an explicitly authorized administrator action, if the product owner allows it.

Default behavior must not silently overwrite.

### 4.5.7 Revisions

A revision drawer lists date, author, and status. Selecting a revision shows source diff and preview. Restore calls REST and receives a new current version.

Do not load full source for every revision in the initial list.

## 4.6 Shared preview component

The block editor, library, and dedicated editor need a common `DiagramViewport` component with adapters for context.

Capabilities:

- accepts source and normalized render options;
- emits validation/render diagnostics;
- renders SVG;
- exposes imperative fit/reset/download API through a controlled ref or command interface;
- supports pan/zoom;
- supports fullscreen or delegates it to the host;
- applies accessible title/description;
- isolates errors.

Do not share the entire surrounding toolbar component because Gutenberg, library, editor, and public front end have different host requirements.

## 4.7 Front-end Interactivity API UX

### State

Per block context:

```ts
{
  instanceId,
  status: 'idle' | 'rendering' | 'ready' | 'error',
  zoom: 1,
  panX: 0,
  panY: 0,
  isFullscreen: false,
  errorMessage: '',
  allowedDownloads: ['source', 'svg']
}
```

Shared store state may hold Mermaid initialization and module-loading promises.

### Actions

- `init`
- `zoomIn`
- `zoomOut`
- `fit`
- `reset`
- `toggleFullscreen`
- `downloadSource`
- `downloadSvg`
- pointer and keyboard pan handlers as appropriate

### Interaction boundaries

- Buttons remain native buttons.
- Disabled actions communicate why.
- Toolbar may collapse into an overflow menu on narrow viewports.
- The viewport has an accessible name derived from the title.
- The rendered SVG is not the only explanation; title and description remain in HTML.

## 4.8 Styling and WordPress conflict isolation

Following the `plugin-ui` settings integration guidance:

- compile plugin styles under an explicit root selector;
- avoid global `button`, `input`, `table`, and typography rules;
- avoid a global Tailwind preflight reset in WordPress admin;
- apply targeted resets inside `.mdm-app` when WordPress form styles conflict;
- use CSS custom properties for plugin tokens;
- use logical properties for RTL support;
- keep front-end block styles minimal and theme-friendly;
- support editor iframe contexts through block styles registered in metadata.

## 4.9 Error vocabulary

Map REST codes to consistent user messages:

| Code | User outcome |
|---|---|
| `mdm_invalid_mermaid` | Show diagnostics and focus source editor |
| `mdm_edit_conflict` | Open conflict workflow |
| `mdm_forbidden` | Explain missing permission; remove impossible actions |
| `mdm_diagram_not_found` | Offer replace/recover in editor context |
| `mdm_invalid_request` | Highlight fields when possible |
| network failure | Retain local edits and offer retry |
| export failure | Explain format-specific fallback, usually SVG/source |

Log developer detail to the console only in development mode; production errors should not expose stack traces.

## 4.10 Live Editor WordPress adaptation

The Svelte editor route includes:

- WordPress header/back link, diagram title, status, save state, Save, Save as Copy, and Preview actions;
- source editor and Mermaid preview retained from upstream;
- category/tag and description panels;
- validation-error focus and unsaved local recovery;
- revision browser and conflict dialog;
- AI menu: Generate, Repair, Explain, Simplify, Improve accessibility metadata;
- coordinated featured-SVG save state and retry;
- capability-aware controls.

The Save button is disabled until the current source is valid and changed. Save first renders and client-sanitizes the matching SVG, then sends source, validation receipt, thumbnail, and expected version in one mutation. Success is shown only when the server commits both source and featured image. A failure leaves the prior server version untouched and retains the candidate in local recovery. The REST response is the authoritative normalized state.

## 4.11 Library launch scope

Version 1.0 ships a performant table/list plus preview side panel. Featured SVG images are optional row previews and become the foundation for a later grid. Bulk categories use Add/Remove/Replace labels.

## 4.12 Phase 04 admin bootstrap and test IDs

Implemented in Phase 04 (ADR-022):

- **Bootstrap global:** `window.mdmAdminBootstrap` with keys `screen`, `restRoot`, `nonce`, `locale`, `capabilities`, `routes`, `defaults`, `i18n`.
- **Library test IDs:** `mdm-library-shell`, `mdm-library-loading`, `mdm-library-empty`, `mdm-library-error`, `mdm-diagram-table`, `mdm-library-pagination`.
- **Settings test IDs:** `mdm-settings-shell`, `mdm-settings-loading`, `mdm-settings-error`, `mdm-settings-nav-{section}`, `mdm-settings-save`, `mdm-runtime-diagnostics`, `mdm-settings-permission-denied`.
- **Screenshot baselines:** `tests/e2e/playwright/__screenshots__/chromium/tests/{admin-menu,library-shell,settings}.spec.ts/*.png`.
- **Table markup:** native `widefat striped` HTML table (not `@wordpress/components` Table, which is unavailable in the pinned package).

