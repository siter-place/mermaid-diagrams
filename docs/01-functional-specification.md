# 1. Functional Specification

## 1.1 Product goal

The plugin gives WordPress users a consistent way to create, organize, reuse, edit, display, interact with, and download Mermaid diagrams.

It solves four connected problems:

1. Authors need to place diagrams in Gutenberg without manually managing scripts or HTML.
2. Organizations need a reusable central diagram library instead of duplicated code in many posts.
3. Diagram maintainers need a focused editing experience with immediate validation and preview.
4. Site visitors need usable diagram controls, including zoom, pan, fullscreen, and download.

## 1.2 User roles

### Diagram viewer

A public or authenticated site visitor who views a published diagram and uses its navigation and download controls.

### Content author

A WordPress user who can edit posts and insert or configure the Mermaid block. Depending on capabilities, the author may select library diagrams but may not necessarily create or modify shared diagrams.

### Diagram editor

A user who can create and edit diagram library records, assign categories and tags, and publish diagrams.

### Diagram manager

A user who can edit other users’ diagrams, manage diagram terms, perform bulk operations, restore or delete diagrams, and configure plugin defaults.

### Administrator

A user who can configure global plugin settings, capability assignments, data retention, and optional experimental features.

## 1.3 Primary user journeys

### Journey A — Create an inline diagram in Gutenberg

1. The author inserts the Mermaid Diagram block.
2. The empty block presents two clear choices: **Create inline diagram** or **Choose from library**.
3. The author chooses inline mode.
4. The author enters Mermaid source, a title, and an optional description.
5. The block validates the source and shows a live preview.
6. The author chooses display settings such as toolbar visibility, default height, theme, and caption visibility.
7. The post is saved and the diagram renders on the front end.
8. The visitor can zoom, pan, fit, reset, enter fullscreen, and download allowed formats.

### Journey B — Save an inline diagram to the library

1. While editing an inline block, the author clicks **Save to diagram library**.
2. The plugin requests a library title if one is not already available.
3. The author can choose category, tags, and status if permitted.
4. The plugin creates one diagram record through REST.
5. On success, the block changes to reference mode and stores the new diagram ID.
6. The author is told that future edits to the shared diagram affect every live-reference block that uses it.
7. A link opens the dedicated diagram editor in a new admin route.

Creation is explicit and idempotent. Repeated clicks must not create duplicate records after a successful response.

### Journey C — Insert an existing diagram

1. The author chooses **Choose from library**.
2. A searchable selector opens.
3. The author can search by title or source text and filter by category, tag, diagram type, author, or status according to permissions.
4. Results show title, preview thumbnail or lightweight preview, category, tags, type, status, and last modified date.
5. The author selects a diagram and sees a larger preview before confirming.
6. The block stores the diagram ID in reference mode.
7. The block can override presentation only: title visibility, description visibility, toolbar, alignment, dimensions, and theme where permitted. It does not edit the shared source from inside normal block controls.

### Journey D — Edit a referenced diagram

1. The author selects a referenced Mermaid block.
2. The block toolbar or inspector includes **Edit shared diagram** when the user has permission.
3. The action opens the dedicated editor for that diagram.
4. The editor warns that changes may update multiple posts.
5. The user edits, validates, previews, and saves.
6. Returning to Gutenberg refreshes the preview when the block regains focus or when the user clicks refresh.

### Journey E — Detach a shared diagram

1. A referenced block offers **Detach as inline copy**.
2. The plugin fetches the current canonical source and presentation metadata.
3. The user confirms that the block will stop receiving library updates.
4. The block changes to inline mode and stores the source locally.
5. The original library diagram is unchanged.

### Journey F — Manage the diagram library

1. The user opens **Diagrams** in WordPress administration.
2. The custom React application loads a paginated summary view.
3. The user searches, filters, and sorts in the launch table/list view; a grid view is deferred.
4. The user opens a preview side panel or accessible modal without leaving the list.
5. The user selects one or more diagrams and applies bulk actions: move category, add tags, remove tags, change status where allowed, duplicate, trash, or restore.
6. The user can open a diagram in the dedicated editor.
7. URL query state preserves filters and pagination so the view can be bookmarked and restored.

### Journey G — Create and edit in the dedicated editor

1. The user opens **Add diagram** or edits an existing diagram.
2. The adapted Mermaid Live Editor displays its source-editing surface and live preview, with WordPress metadata controls integrated around it.
3. Validation runs after a short debounce and on explicit save.
4. Syntax errors show a readable message and, when available, line and column information.
5. The user can set title, description, category, tags, status, and allowed presentation defaults.
6. The user can download source or sanitized SVG without saving.
7. The user saves or publishes based on capability only after the exact source passes Mermaid JS validation. Invalid source remains unsaved local recovery state.
8. The editor displays clean, dirty, saving, saved, validation-error, network-error, and conflict states.
9. If another user changed the diagram, the editor prevents silent overwrite and offers reload, compare, or save as a copy.

### Journey H — Use visual editing

1. The user opens the **Visual** tab.
2. The system checks whether the diagram type and syntax are supported losslessly enough for visual editing.
3. Supported flowcharts open as draggable nodes and edges with pan, zoom, selection, alignment, and basic property editing.
4. Changes are serialized back to Mermaid source and validated before becoming canonical.
5. If conversion would lose unsupported syntax or semantics, the editor blocks destructive save and shows an exact loss report.
6. Unsupported diagrams retain full code editing and preview; visual mode is disabled or read-only with an explanation.

## 1.4 Diagram library requirements

### Record fields

Every diagram must support:

- title, required for library records;
- description, optional;
- canonical Mermaid source, required;
- detected diagram type;
- status: draft, pending review when enabled, published, private where appropriate, trash;
- author and last editor;
- created and modified timestamps;
- one or more categories, with uncategorized as an explicit fallback if desired;
- zero or more tags;
- revisions;
- presentation defaults such as theme, toolbar, and dimensions;
- optional visual-editor metadata for supported diagram types;
- source hash and render-version metadata for caching and conflict diagnosis.

### Categories

- Categories are hierarchical.
- Users with term-management capability can create, rename, reorder where supported, and delete categories.
- A diagram can belong to one or more categories unless the product owner chooses a single-primary-category policy.
- Bulk move means replace or add categories according to an explicit action label; it must not ambiguously change terms.

### Tags

- Tags are non-hierarchical.
- Tags can be added or removed individually or in bulk.
- Autocomplete should prevent accidental near-duplicate tags where possible.

### Search

The library must support:

- title search;
- description search;
- Mermaid source search where database scale allows it;
- exact diagram ID lookup;
- category and tag filters;
- diagram type filter;
- status and author filters according to capability;
- modified-date sorting;
- deterministic pagination.

For large libraries, source-text search may be moved to a dedicated indexed strategy without changing the UI contract.

### Preview

- Preview is fetched on demand, not included as full SVG in every list row.
- The preview panel displays title, description, categories, tags, status, author, modified date, detected type, rendered diagram, and available actions.
- The preview must handle invalid legacy source without crashing the library application.

### Duplicate

Duplicating a diagram creates a draft owned by the current user, adds a localized “Copy” suffix, retains source and presentation defaults, and optionally retains categories and tags.

### Trash and deletion

- Normal deletion moves the diagram to Trash.
- Permanent deletion requires the appropriate capability and confirmation.
- The plugin warns when a diagram is referenced by published content.
- Permanent deletion must not corrupt posts. Referenced blocks show a clear missing-diagram fallback with the ID and an editor-only recovery action.

## 1.5 Gutenberg block requirements

### Block states

The block has these user-visible states:

- empty chooser;
- inline editing;
- inline valid preview;
- inline syntax error;
- library search;
- referenced loading;
- referenced preview;
- referenced missing or inaccessible;
- save-to-library in progress;
- permission denied;
- network error with retry.

### Inline mode

Inline mode stores:

- Mermaid source;
- optional local title and description;
- presentation options;
- no diagram post ID.

The source is editable in the block. Autosave and post revisions naturally preserve block content.

### Reference mode

Reference mode stores:

- diagram post ID;
- presentation overrides only;
- an optional non-authoritative preview snapshot for editor resilience, never used as the front-end source when the record is available.

The source is edited through the dedicated editor, not through an ambiguous duplicate field in the block.

### Inspector controls

The block inspector should include:

- source mode and reference information;
- display title and description toggles;
- toolbar visibility;
- allowed download formats;
- initial viewport mode: fit, actual size, or custom zoom where supported;
- height or aspect behavior;
- theme or theme inheritance;
- alignment and standard block supports;
- responsive behavior;
- advanced accessibility label override if needed.

Security-critical Mermaid settings must not be exposed as author options.

### Block toolbar

Contextual actions include:

- choose or replace library diagram;
- edit shared diagram;
- refresh reference;
- save inline diagram to library;
- detach as inline copy;
- download source and SVG according to policy;
- open larger preview.

Actions appear only when valid for the current mode and capability.

### Rendering

The block is dynamic. PHP resolves reference-mode records and emits semantic fallback markup plus serialized render data. The browser renders Mermaid and activates controls. Pages without the block do not load Mermaid assets.

### Missing references

On the public front end, a missing or unauthorized referenced diagram shows no sensitive details. It may show a localized generic fallback. In the editor, authorized users see the diagram ID, reason when known, and repair actions.

## 1.6 Dedicated editor requirements

### Layout

The default desktop layout contains:

- top action bar: back, save, status, downloads, revisions, more actions;
- metadata region: title, description, categories, tags, publication state;
- workspace tabs: Code, Visual when supported, Preview-only if needed;
- code editor and live preview in resizable panes;
- diagnostics panel that can be collapsed;
- settings panel for safe Mermaid and presentation options.

On narrow screens, panes stack or switch through tabs.

### Code editor

- Mermaid-aware syntax highlighting where available;
- line numbers;
- find and replace;
- undo and redo;
- indentation support;
- keyboard shortcuts that do not conflict with WordPress conventions;
- debounced syntax validation;
- sample templates for supported diagram types;
- optional formatting only when it preserves semantics;
- no automatic rewriting of valid user source without explicit action.

### Live preview

- Renders only the latest successful validation result.
- Keeps the last valid preview visible while clearly marking current source as invalid, unless that would mislead the user.
- Includes fit, zoom, pan, reset, and fullscreen.
- Cancels or ignores stale asynchronous render results.
- Displays render duration in development diagnostics, not necessarily to end users.

### Save behavior

- New records start as unsaved drafts.
- Save is disabled only when required metadata is missing or a policy forbids invalid source.
- Invalid source is never persisted, including as a WordPress draft. The editor preserves invalid unsaved work in local recovery storage until corrected or discarded.
- Save includes a version token. A stale token returns HTTP 409 and never silently overwrites newer work.
- Successful saves refresh server-normalized fields and the version token.

### Revisions

- Users can open a revision list.
- A revision preview shows metadata and source differences.
- Restore creates a new current revision rather than erasing history.
- Visual metadata is restored only when compatible with the restored source and adapter version.

## 1.7 Front-end interaction requirements

Each rendered diagram may expose an accessible toolbar containing:

- zoom in;
- zoom out;
- fit to viewport;
- reset view;
- fullscreen;
- download source;
- download SVG;
- download SVG; PNG is deferred beyond the core release;
- optional copy source.

### Navigation behavior

- Mouse wheel zoom must be deliberate and must not trap normal page scrolling. A modifier key or focused interaction region is recommended.
- Drag-to-pan activates only inside the diagram viewport.
- Touch pinch and drag should work where the selected pan/zoom implementation supports them.
- Keyboard users can focus the viewport and use documented keys for zoom, reset, and movement.
- The current zoom level is announced through an unobtrusive live region.
- Reset returns to the configured initial viewport.

### Fullscreen

- Uses the Fullscreen API when available or an accessible dialog fallback.
- Focus moves into the fullscreen container and returns to the invoking control on close.
- Escape closes the experience unless native fullscreen behavior handles it.

## 1.8 Download requirements

### Mermaid source

- File extension: `.mmd` by default; `.mermaid` may be offered as an alternative.
- Filename uses a sanitized diagram title and stable identifier where helpful.
- Source download is always based on the canonical source currently displayed.

### SVG

- SVG is generated from the current validated source.
- Export inserts or preserves accessibility title and description where possible.
- The exported file must not include plugin UI chrome.
- The plugin sanitizes the generated SVG before download or storage.

### PNG (deferred)

PNG export is not part of the core release. SVG is the required high-quality image format. A future PNG feature must define browser/canvas limits and add its own tests before being enabled.

### Administrative downloads

The block editor, dedicated editor, library preview, and public toolbar should call one shared export service so filename rules and error behavior stay consistent.

## 1.9 Settings requirements

A settings page should use the `plugin-ui` settings pattern as a starting point and include:

- default Mermaid theme;
- default toolbar visibility;
- enabled download formats;
- maximum source length;
- maximum rendering complexity thresholds where available;
- default diagram dimensions;
- whether source download is public;
- whether visual editing is enabled and whether it is Beta;
- allowed roles/capability guidance;
- data behavior on uninstall;
- diagnostics such as bundled Mermaid version and cache reset.

The server is the source of truth. The client fetches the settings schema and normalized values, sends section-scoped changes, and replaces local state with the server response after save.

## 1.10 Permissions and capability outcomes

The UI must be capability-driven, not role-name-driven.

At minimum, separate capabilities should cover:

- read published diagrams;
- edit own diagrams;
- edit others’ diagrams;
- publish diagrams;
- delete own diagrams;
- delete others’ diagrams;
- manage diagram categories and tags;
- manage plugin settings;
- use shared diagrams in posts if a separate policy is desired.

The plugin may map these to Administrator and Editor on activation, but must not hard-code role names in authorization checks.

## 1.11 Notifications and errors

- Success messages confirm the completed action and identify the diagram.
- Destructive actions use explicit confirmation and describe reference impact.
- Validation errors are shown beside the editor and summarized for screen readers.
- REST errors preserve actionable server messages but do not expose stack traces or sensitive data.
- Retry is available for transient network failures.
- Bulk actions report partial success item by item rather than pretending the whole operation succeeded.

## 1.12 Internationalization

All PHP and JavaScript user-facing strings are translatable. Dates, numbers, plural forms, and list formatting use WordPress internationalization utilities. Diagram source is never translated automatically.

## 1.13 Auditability

The plugin should preserve:

- WordPress revisions;
- author and modified-by data;
- source and settings version metadata;
- optional application-level action logs only if the organization requires them.

Do not create a custom audit log in the first release unless there is a concrete retention and privacy requirement.

## 1.14 Functional acceptance criteria

The release is functionally acceptable when all of the following are true:

- An authorized user can create, validate, save, categorize, tag, find, preview, edit, revise, duplicate, trash, and restore a diagram.
- A content author can insert inline source or choose an existing diagram without leaving Gutenberg.
- Saving an inline diagram to the library creates exactly one record and switches the block to reference mode.
- A referenced block reflects a saved library update without editing every post.
- Detaching creates a stable inline copy that no longer follows library updates.
- Public diagrams render with JavaScript enabled and provide semantic fallback content without it.
- Zoom, pan, fit, reset, fullscreen, and allowed downloads work with mouse, keyboard, and touch at the supported level.
- Invalid Mermaid source never breaks the entire editor or page.
- Unauthorized users cannot create, update, delete, export restricted source, or manage terms through direct REST calls.
- Pages without diagrams do not load Mermaid runtime assets.
- A stale editor save produces a conflict workflow rather than overwriting another user’s changes.
- Automated tests cover the critical journeys described in `09-testing-strategy.md`.

## 1.14 Finalized integrations

- The dedicated editor is the adapted Mermaid Live Editor Svelte application.
- AI actions use WordPress 7.0 AI Client/Connectors and produce untrusted candidates.
- Abilities provide machine-readable list/get/generate/create/update operations with normal capability checks.
- The official MCP Adapter may expose approved abilities to external chat clients.
- Browser-generated SVG is validated against the source hash, sanitized again on the server, and assigned as the featured image within the same logical save command.
- A normal save succeeds only when canonical source and featured SVG are both committed. On failure, the previous persisted version remains active and the candidate remains in local recovery for retry.
- Usage counts and reverse references are reconciled through WP-Cron.
