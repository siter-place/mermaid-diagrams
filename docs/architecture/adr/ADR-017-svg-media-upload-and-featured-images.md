# ADR-017: Controlled SVG Media Upload and Featured Image Strategy

## Status
Accepted (Phase 00 Architecture Spike)

## Context
Spike 4 tested storing rendered Mermaid diagram SVGs into the WordPress Media Library and assigning them as featured images (`_thumbnail_id`) for diagram custom post types or standard posts.

## Decision
1. **MIME Type Filtering**: Register `upload_mimes` filter for `image/svg+xml` during plugin media operations.
2. **Standard Media Attachment Integration**: Use `wp_upload_bits()` and `wp_insert_attachment()` for storing SVG diagram thumbnails.
3. **Featured Image Support**: Confirmed `set_post_thumbnail()` and `get_post_thumbnail_id()` work natively with SVG attachments in WordPress 7.0.
4. **Mandatory Pre-upload Sanitization**: All SVG strings generated from client or server must pass XML/DOM sanitization (stripping `<script>`, `onload`, `javascript:` URIs) prior to storage.

## Consequences
- Enables diagrams to serve as native WordPress featured images across themes.
- Maintains strict security against stored XSS via SVG sanitization.
