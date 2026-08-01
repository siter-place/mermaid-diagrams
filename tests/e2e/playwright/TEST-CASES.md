# Playwright Test Cases

## Library

- Create diagram and verify it appears in the list.
- Search by title.
- Filter by category, tag, type, and status.
- Reload and preserve URL filter state.
- Open preview and verify SVG appears.
- Duplicate and verify draft copy.
- Bulk add tag.
- Bulk replace category.
- Trash and restore.
- Simulate partial bulk failure and verify failed selection remains.

## Editor

- Create valid flowchart and save.
- Enter invalid syntax and verify diagnostics.
- Verify last-valid preview label.
- Download `.mmd` and SVG.
- Use two browser contexts to cause HTTP 409 conflict.
- Restore revision.
- Abort network during save and verify local draft remains.
- Verify unsaved-change navigation protection.

## Gutenberg

- Insert inline block, enter source, save post, and view front end.
- Save inline source to library and verify one diagram created.
- Select existing diagram.
- Edit shared diagram and verify reference output changes.
- Detach and verify later shared update does not change inline copy.
- Replace missing reference.
- Verify author without shared-edit permission cannot open shared editor.

## Front end

- Render one and multiple diagrams.
- Zoom, pan, fit, reset.
- Fullscreen and focus restoration.
- Keyboard controls.
- Download filenames and non-empty files.
- Narrow viewport toolbar.
- Invalid/missing diagram isolation.
- Verify no Mermaid asset request on a page without the block.

## Visual editor Beta

- Open supported flowchart in editable mode.
- Move node, add node/edge, Apply, save, reopen.
- Verify no-edit mode switch does not modify source.
- Unsupported syntax produces read-only or blocked mode.
- Code edit invalidates stale visual layout/history.

## Permissions

- Subscriber cannot open library/editor.
- Author can insert only readable diagrams.
- Direct REST update blocked for unauthorized user.
- Private source omitted from search/preview/download.
- Settings available only with `manage_mdm_settings`.

## Accessibility

- Run axe on library, selector, editor, and public block.
- Test modal focus trap and return.
- Complete primary flows with keyboard.
- Verify toolbar buttons have names and status announcements.
