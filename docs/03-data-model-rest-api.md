# 3. Data Model and REST API Specification

## 3.1 Canonical storage decision

Use the WordPress post record as the diagram aggregate:

| WordPress field | Meaning |
|---|---|
| `ID` | Diagram identity |
| `post_type` | `mdm_diagram` |
| `post_title` | Diagram title |
| `post_excerpt` | Human-readable description |
| `post_content` | Canonical Mermaid source as plain text |
| `post_status` | Draft/pending/publish/private/trash according to policy |
| `post_author` | Owner/creator |
| `post_date*`, `post_modified*` | Creation and modification timestamps |

Using `post_content` for source provides native revisions, autosave-compatible semantics, standard REST raw-content handling for authorized contexts, and fewer ambiguous storage paths. The custom administration UI does not need to expose the standard WordPress editor.

## 3.2 Post type registration

Recommended arguments:

```php
register_post_type( 'mdm_diagram', [
    'label'           => __( 'Diagrams', 'mermaid-diagrams' ),
    'public'          => false,
    'publicly_queryable' => false,
    'show_ui'         => false, // custom React pages own the UI
    'show_in_menu'    => false,
    'show_in_rest'    => true,
    'rest_base'       => 'mdm-diagrams',
    'supports'        => [ 'title', 'excerpt', 'revisions', 'author' ],
    'map_meta_cap'    => true,
    'capability_type' => [ 'mdm_diagram', 'mdm_diagrams' ],
    'has_archive'     => false,
    'rewrite'         => false,
] );
```

Implementation note: WordPress revision behavior for `post_content` must be verified in integration tests even though the standard editor support is not shown. If needed, include `editor` support internally while removing the standard edit screen and using the custom route.

## 3.3 Taxonomies

### Category

```text
slug: mdm_diagram_category
hierarchical: true
show_in_rest: true
rest_base: mdm-diagram-categories
```

### Tag

```text
slug: mdm_diagram_tag
hierarchical: false
show_in_rest: true
rest_base: mdm-diagram-tags
```

Both taxonomies attach only to `mdm_diagram`. Term permissions use the plugin’s mapped management capabilities.

## 3.4 Registered meta

Recommended post meta:

| Key | Type | Purpose |
|---|---|---|
| `_mdm_diagram_type` | string | Last server-accepted detected Mermaid type |
| `_mdm_render_config` | object | Safe presentation/render configuration |
| `_mdm_visual_model` | object/null | Optional derived visual-editor model |
| `_mdm_visual_adapter` | string/null | Adapter ID and schema version |
| `_mdm_source_hash` | string | SHA-256 or equivalent stable source hash |
| `_mdm_renderer_version` | string | Mermaid/runtime version used for last validation |
| `_mdm_validation_state` | string | valid, invalid, unknown |
| `_mdm_validation_summary` | object | Safe diagnostic summary, no raw stack trace |
| `_mdm_last_editor_id` | integer | Last user who saved through plugin UI |

Meta exposed through REST must have explicit schemas, auth callbacks, sanitizers, and defaults. Meta beginning with `_` remains protected from casual custom-field UI.

Do not store generated SVG as canonical post meta. The controlled featured-image attachment is derived and may be regenerated.

## 3.5 Diagram aggregate

Conceptual domain model:

```text
Diagram
- id: DiagramId
- title: NonEmptyTitle
- description: Description
- source: DiagramSource
- type: DiagramType | Unknown
- status: DiagramStatus
- authorId: UserId
- categoryIds: TermId[]
- tagIds: TermId[]
- renderConfig: SafeRenderConfig
- visualState: VisualState | null
- version: DiagramVersion
- sourceHash: SourceHash
- createdAt
- modifiedAt
```

Invariants:

- library diagrams have a non-empty title;
- source length is below the configured maximum;
- source is plain Mermaid text, not an HTML wrapper;
- published diagrams must pass Mermaid validation according to policy;
- visual state must identify its adapter and source hash;
- visual state whose source hash no longer matches is stale and may not overwrite source;
- statuses and term changes require capabilities;
- current version token changes on every persisted update.

## 3.6 Version token and optimistic concurrency

Use a server-generated opaque token derived from immutable update facts, for example:

```text
base64url(HMAC(post_id | post_modified_gmt | source_hash | revision_id))
```

The token is returned in detail responses. Update requests send `expected_version`.

- Match: proceed and return a new token.
- Missing token on an existing record: reject for the dedicated editor unless explicitly configured for last-write-wins administrative tooling.
- Mismatch: HTTP 409 with `mdm_edit_conflict` and a safe summary of the current server version.

Do not rely only on a second-level timestamp if two writes can occur in the same second.

## 3.7 Block attribute model

Recommended `block.json` attributes:

```json
{
  "mode": { "type": "string", "default": "inline", "enum": ["inline", "reference"] },
  "diagramId": { "type": "number" },
  "source": { "type": "string", "default": "" },
  "title": { "type": "string", "default": "" },
  "description": { "type": "string", "default": "" },
  "showTitle": { "type": "boolean", "default": true },
  "showDescription": { "type": "boolean", "default": true },
  "showToolbar": { "type": "boolean", "default": true },
  "allowSourceDownload": { "type": "boolean", "default": true },
  "allowSvgDownload": { "type": "boolean", "default": true },
  "initialView": { "type": "string", "default": "fit" },
  "height": { "type": "number", "default": 480 },
  "previewSnapshot": { "type": "object" }
}
```

`previewSnapshot` may contain only editor-resilience metadata such as title, description, source hash, detected type, and a tiny placeholder. It is not authoritative on the front end.

### Validation rules

- `mode=inline`: `diagramId` is ignored/cleared; `source` is canonical.
- `mode=reference`: `diagramId` is required; source is resolved server-side; block `source` must be cleared or treated solely as a migration snapshot.
- Download flags can only reduce globally allowed source/SVG formats, not enable a globally disabled format.
- Height is clamped to safe minimum/maximum.
- Inline blocks use the global theme. Reference blocks use the referenced diagram default theme. Version 1.0 has no block-level theme override.

## 3.8 REST strategy

### Core endpoints reused

Where suitable:

- `/wp/v2/mdm-diagrams`
- `/wp/v2/mdm-diagrams/{id}`
- `/wp/v2/mdm-diagram-categories`
- `/wp/v2/mdm-diagram-tags`
- revisions endpoints exposed for the CPT

The application may use core endpoints for simple term autocomplete or standard draft creation. However, the principal React apps should use stable custom DTOs when they need combined data or conflict semantics.

### Custom REST namespace

`/wp-json/mdm/v1`

## 3.9 Proposed custom endpoints

### `GET /mdm/v1/diagrams`

Purpose: optimized library/block selector search.

Query parameters:

- `search`
- `category[]`
- `tag[]`
- `type[]`
- `status[]` where permitted
- `author[]` where permitted
- `modified_after`, `modified_before`
- `order`, `orderby`
- `page`, `per_page`
- `view=summary|selector`

Response:

```json
{
  "items": [
    {
      "id": 123,
      "title": "Authentication flow",
      "description": "High-level sign-in sequence",
      "type": "flowchart",
      "status": "publish",
      "categories": [{ "id": 7, "name": "Architecture" }],
      "tags": [{ "id": 12, "name": "OIDC" }],
      "author": { "id": 4, "name": "Editor" },
      "modifiedGmt": "2026-07-29T18:10:00Z",
      "sourceHash": "...",
      "can": { "edit": true, "delete": true, "publish": true },
      "preview": { "state": "available", "url": "/wp-json/mdm/v1/diagrams/123/preview" }
    }
  ],
  "pagination": { "page": 1, "perPage": 20, "totalItems": 84, "totalPages": 5 },
  "facets": { "types": [], "statuses": [] }
}
```

The response must not expose raw source unless the requester has permission and the requested view explicitly needs it.

### `POST /mdm/v1/diagrams`

Purpose: create a diagram from the dedicated editor or Gutenberg save-to-library action.

Request:

```json
{
  "title": "Authentication flow",
  "description": "...",
  "source": "flowchart TD\n A-->B",
  "status": "draft",
  "categoryIds": [7],
  "tagIds": [12],
  "renderConfig": { "theme": "inherit" },
  "idempotencyKey": "uuid-from-client"
}
```

The idempotency key is stored temporarily or mapped to the created ID for a bounded time so a retry does not create duplicates.

### `GET /mdm/v1/diagrams/{id}`

Purpose: editor detail.

Returns raw source only when the user can read/edit that record. Includes normalized fields, capabilities, version token, adapter state, revision summary, usage summary, and safe settings.

### `PUT|PATCH /mdm/v1/diagrams/{id}`

Purpose: conflict-aware update.

Request includes `expectedVersion`. PATCH semantics must be documented; absent fields remain unchanged. Server response is a full normalized detail DTO.

Errors:

- `400 mdm_invalid_request`
- `403 mdm_forbidden`
- `404 mdm_diagram_not_found`
- `409 mdm_edit_conflict`
- `422 mdm_invalid_mermaid` for publication-blocking validation
- `429 mdm_rate_limited` only if rate controls are introduced

### `POST /mdm/v1/diagrams/{id}/duplicate`

Creates an owned draft copy. Accepts optional title and term-retention choices.

### `POST /mdm/v1/diagrams/bulk`

Request:

```json
{
  "ids": [12, 13, 14],
  "operation": "replace_categories",
  "payload": { "categoryIds": [7] }
}
```

Allowed operations:

- `add_categories`
- `remove_categories`
- `replace_categories`
- `add_tags`
- `remove_tags`
- `set_status`
- `trash`
- `restore`

Response reports each item separately:

```json
{
  "results": [
    { "id": 12, "ok": true },
    { "id": 13, "ok": false, "error": { "code": "mdm_forbidden", "message": "..." } }
  ],
  "summary": { "requested": 2, "succeeded": 1, "failed": 1 }
}
```

### `GET /mdm/v1/diagrams/{id}/preview`

Returns render payload or a short-lived rendered preview representation according to the chosen implementation. It must not be a permanent public URL for private source.

### `GET /mdm/v1/diagrams/{id}/usage`

Returns references discovered in posts the user may inspect. It can include counts by status and a paginated list of post IDs/titles/edit links. Public REST must not reveal private post titles.

Usage indexing choices:

1. Initial release: query block content when needed and cache results.
2. Scale upgrade: maintain a reverse-reference table or post meta index on post save.

The second option should be introduced only after measuring library size and query cost.

### `GET /mdm/v1/diagrams/{id}/revisions`

Returns revision summaries.

### `GET /mdm/v1/diagrams/{id}/revisions/{revisionId}`

Returns source/metadata for comparison when authorized.

### `POST /mdm/v1/diagrams/{id}/restore-revision/{revisionId}`

Restores a revision as a new current state and returns a new version token.

### `GET /mdm/v1/settings`

Returns:

```json
{
  "schema": { "sections": [] },
  "values": {},
  "capabilities": {},
  "runtime": { "pluginVersion": "...", "mermaidVersion": "..." }
}
```

### `PATCH /mdm/v1/settings/{section}`

Updates one section, merges with existing settings, validates, stores, and returns the complete normalized section. This follows the `plugin-ui` settings integration’s server-source-of-truth principle.

## 3.10 Response schema principles

- Dates use RFC 3339 UTC strings.
- IDs are integers in JSON.
- User-controlled strings are returned as plain values, not prebuilt HTML.
- Capability decisions are included as `can` flags to simplify UI while still being enforced server-side.
- Error codes are stable and machine-readable.
- Pagination uses response body metadata, with optional standard REST headers.
- DTOs are versioned and tested with schema snapshots.

## 3.11 Permission matrix

| Operation | Required decision |
|---|---|
| Read published source for public render | Public render policy; raw source download may be separately disabled |
| Read draft/private diagram | `read_post` mapped capability |
| Insert reference in a post | ability to edit the post plus ability to read the diagram |
| Create diagram | `edit_mdm_diagrams` |
| Edit own diagram | mapped `edit_post` |
| Edit another user’s diagram | mapped `edit_others_mdm_diagrams` |
| Publish | `publish_mdm_diagrams` |
| Delete | mapped delete capability and reference warning |
| Manage terms | `manage_mdm_diagram_terms` |
| Manage settings | `manage_mdm_settings` |
| Download raw source | record read permission plus global/public download policy |

Every custom route implements a `permission_callback`. UI `can` flags are convenience only.

## 3.12 Input validation

### Source

- type string;
- normalize line endings to LF;
- reject null bytes;
- enforce configured byte/character maximum;
- retain meaningful whitespace;
- do not use `wp_kses_post` because Mermaid source is not HTML;
- validate Mermaid syntax in the browser and, if server publication validation is required, through a controlled validation mechanism rather than executing arbitrary user JavaScript in PHP.

Because PHP cannot natively invoke Mermaid, the initial authoritative save policy may combine browser validation with source constraints and “validation state” metadata. A hardened server-side validation worker using Mermaid CLI can be an optional enterprise deployment, but it must not make normal WordPress saves depend on an unreliable external service.

### Render configuration

Allowlist every key. Reject author control over:

- `securityLevel`;
- callback or click behavior;
- arbitrary HTML labels when unsafe;
- external resource loaders;
- global DOM selectors;
- theme CSS containing unsafe constructs.

### Terms and IDs

- validate existence and taxonomy;
- apply object-level capabilities;
- deduplicate IDs;
- impose a reasonable maximum count per request.

## 3.13 Idempotency

Actions vulnerable to retry duplication should accept an idempotency key:

- create from Gutenberg;
- duplicate;
- selected bulk operations if the client retries automatically.

The server stores a short-lived key-to-result mapping scoped to user and operation. The same key with different content returns an error.

## 3.14 Usage references and deletion behavior

A reference-mode block stores only the diagram ID in post content. Before permanent deletion:

1. calculate or retrieve usage count;
2. show published and draft reference counts that the user may see;
3. require explicit confirmation;
4. leave blocks intact so restore can repair them;
5. render a public-safe fallback while missing;
6. never automatically convert all references to inline source without an explicit migration action.

An optional **Archive** status can be added later if organizations want diagrams to remain renderable but hidden from normal insertion searches.

## 3.15 Settings storage

Store one versioned option, for example `mdm_settings`, containing sectioned values. Keep schema in PHP. Suggested sections:

- `rendering`
- `downloads`
- `editor`
- `visual_editor`
- `permissions`
- `data_retention`
- `diagnostics`

Do not store each checkbox as an unrelated option unless WordPress autoload and migration behavior has been intentionally designed.

## 3.16 Uninstall policy

The settings page offers a clear choice:

- preserve all diagrams and settings on uninstall, default;
- delete plugin settings only;
- delete diagrams, taxonomies, revisions, and settings only after explicit opt-in.

`uninstall.php` reads the stored policy and performs capability-checked, multisite-aware cleanup. It must not delete content merely on deactivation.

## 3.18 Final validation contract

Every mutation that changes Mermaid source requires:

```json
{
  "source": "flowchart LR\nA-->B",
  "validation": {
    "sourceHash": "sha256:...",
    "mermaidVersion": "<pinned version>",
    "diagramType": "flowchart-v2",
    "validatedAt": "2026-08-01T12:00:00Z",
    "profile": "browser|worker"
  }
}
```

The server rejects missing, mismatched, stale-beyond-policy, unsupported-version, or forged worker receipts. A browser receipt is accepted only for normal authenticated admin UI routes and is not sufficient for autonomous MCP persistence when the worker profile is required.

Invalid Mermaid source has no persistence state. WordPress post status does not relax validation.

## 3.19 Usage index tables

Recommended schema:

- `{$wpdb->prefix}mdm_usage`: diagram ID, consumer object ID/type, block key, first/last seen, consumer status, source revision.
- aggregated `mdm_usage_count` registered post meta for fast list display.
- `{$wpdb->prefix}mdm_usage_dirty`: consumer IDs awaiting reindex, or an equivalent durable queue.

Post-save hooks enqueue; WP-Cron processes bounded batches; a daily reconciliation repairs drift; WP-CLI supports targeted and full reindex.

## 3.20 Coordinated diagram and featured-SVG mutation

Normal `POST /mdm/v1/diagrams` and `PUT|PATCH /mdm/v1/diagrams/{id}` mutations include a required `thumbnail` envelope after Phase 09:

```json
{
  "source": "flowchart LR\nA-->B",
  "validation": {
    "sourceHash": "sha256:...",
    "mermaidVersion": "<pinned version>",
    "diagramType": "flowchart-v2",
    "validatedAt": "2026-08-01T12:00:00Z",
    "profile": "browser"
  },
  "thumbnail": {
    "svg": "<svg ...>...</svg>",
    "sourceHash": "sha256:...",
    "width": 1200,
    "height": 630,
    "rendererVersion": "<pinned version>"
  },
  "expectedVersion": "..."
}
```

The command verifies that source, validation receipt, and thumbnail all share the same hash and Mermaid version. It validates capabilities and schemas, sanitizes the SVG again, strips scripts/events/external resources/unsafe URLs/`foreignObject`, and applies strict size and dimension limits.

The application service implements a coordinated save with compensating actions:

1. validate all input before changing persistent state;
2. stage or create the controlled SVG attachment;
3. create/update the diagram and assign `_thumbnail_id`;
4. remove the previous derived attachment only after success;
5. on any failure, remove staged artifacts and preserve the previous diagram version.

A save response is successful only when both source and featured SVG are committed. Browser local recovery preserves the candidate after failure.

`POST /mdm/v1/diagrams/{id}/thumbnail:regenerate` is a repair-only endpoint. It accepts a generated SVG, source hash, dimensions, and expected version, and may replace the featured image only when the hash matches the already persisted canonical source. It never accepts arbitrary SVG for unrelated posts and is not used by the normal editor save path.

## 3.21 Abilities mapping

Abilities are not a second business API. Each maps to an application command/query also used by REST. Initial names:

- `mdm/list-diagrams`
- `mdm/get-diagram`
- `mdm/generate-diagram-candidate`
- `mdm/validate-diagram`
- `mdm/create-diagram`
- `mdm/update-diagram`
- `mdm/duplicate-diagram`
- `mdm/get-diagram-usage`

Mutation abilities use exact JSON Schemas, permission callbacks, optimistic version tokens, and worker validation where required.
