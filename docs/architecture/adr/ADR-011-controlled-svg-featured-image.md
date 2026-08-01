# ADR-011: Controlled SVG Featured Images

**Status:** Accepted

## Decision

Use browser-generated SVG as a controlled derived featured image, but commit it through the same logical application command as the canonical source. The browser validates source, renders SVG with the pinned Mermaid runtime, applies client sanitization, and submits source, validation receipt, SVG, dimensions, source hash, and expected version together. The server sanitizes again, validates provenance and limits, stages/reuses the attachment, updates the diagram and `_thumbnail_id`, and acknowledges success only after both are committed.

The implementation uses compensating actions rather than claiming that the WordPress database and filesystem provide a true distributed transaction. New partial records/attachments are removed on failure; updates leave the previous source and featured image unchanged. A separate regeneration endpoint exists only for repair of an already persisted matching source.

General SVG upload remains disabled.

## Consequences

- A normal save cannot retry media independently while claiming source success; retry replays the coordinated command from local recovery. The repair endpoint is independent only for an already persisted matching source.
- The library list can use featured images without rendering every diagram.
- Media permissions, MIME handling, sanitization, dimensions, and orphan cleanup need dedicated tests.
