# ADR-020: Usage Index Table Schema and Database Upgrade Runner

## Status
Accepted (Phase 01)

## Context
Phase 01 requires database migrations for `mdm_usage` and `mdm_usage_dirty` custom tables to index diagram usage across posts, pages, and blocks. The plugin also requires a versioned, idempotent database migration runner.

## Decision
1. **Option Tracking**: Track database schema version in `mdm_db_version` option (initial target: `1.4.1`).
2. **Upgrade Runner**: Implement `WebFalcon\MermaidDiagrams\Upgrade\UpgradeRunner` executing migrations via `dbDelta()`.
3. **Custom Usage Tables**:
   - `{$prefix}mdm_usage`: maps `diagram_id` to `consumer_id`, `consumer_type`, `block_key`, `consumer_status`, `source_revision`, and `first_seen`/`last_seen` timestamps.
   - `{$prefix}mdm_usage_dirty`: queue table tracking `consumer_id` enqueued for asynchronous reindexing in Phase 09.
4. **Data Retention Policy**: `uninstall.php` defaults to `preserve` (no deletion on uninstall). Optional policies `delete_settings` and `delete_all` clean settings and posts/tables respectively.

## Consequences
- Guarantees custom database tables exist on activation and upgrade without duplicate schema creation.
- Preserves user content by default during deactivation and uninstall.
- Prepares storage schema for Phase 09 usage index scanner.
