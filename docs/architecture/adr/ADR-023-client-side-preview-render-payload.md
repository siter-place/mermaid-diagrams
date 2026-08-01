# ADR-023: Client-Side Preview Rendering via Authorized REST Payload

## Status

Accepted (Phase 05)

## Context

Phase 05 requires an accessible library preview panel that renders diagrams on demand without bloating list payloads. The API contract in `docs/03-data-model-rest-api.md` defines `GET /mdm/v1/diagrams/{id}/preview`, and list summaries already advertise a preview URL. Phase 03 established `packages/mermaid-runtime` for parse/render/sanitize in the browser with strict security settings.

Options considered:

1. **Server-rendered SVG URL** — Simple for clients but conflicts with the spec requirement that preview must not be a permanent public URL for private source, and duplicates Phase 03 sanitization on the server for admin-only surfaces.
2. **Authorized render payload + client `DiagramViewport`** — REST returns capability-gated source and metadata; the admin app renders SVG with pinned Mermaid runtime.
3. **Reuse `GET /diagrams/{id}` detail** — Works but over-fetches editor fields and blurs the preview contract used by Phase 06 selector flows.

## Decision

1. Implement **`GET /mdm/v1/diagrams/{id}/preview`** returning `DiagramPreviewDTO`: title, description, type, status, renderConfig, validation state, thumbnail status, `can` flags, and **source only when the current user may read the post**.
2. Add shared React component **`DiagramViewport`** that lazy-renders SVG via `@mdm/mermaid-runtime` with webpack browser aliases (`init-browser.ts`) to avoid bundling Node/jsdom shims.
3. Quick-create and preview both use the same validation/render path; create mutations still require validation receipts per product charter.
4. **`POST /mdm/v1/diagrams/{id}/duplicate`** exposes the existing application service for single-item duplicate from row/preview actions (not bulk duplicate).

## Consequences

- Preview panel fetches once per diagram ID and caches payload in `useDiagramPreview`.
- Admin library bundle includes Mermaid (~500 KiB entry); acceptable for admin-only surface; code-split chunks emitted by webpack.
- PHPUnit and Playwright cover preview authorization and workflow.
- Phase 06 block/selector can reuse `DiagramViewport` and preview REST contract.

## References

- `docs/03-data-model-rest-api.md` § preview/duplicate
- `docs/04-frontend-applications-and-ux.md` §4.4.6, §4.6
- ADR-014 (Mermaid pin), ADR-022 (React admin architecture)
