# Abilities API and WordPress MCP Adapter Integration

## Responsibility split

- **Application services** own business rules and persistence orchestration.
- **REST controllers** adapt human/browser HTTP requests.
- **Abilities** expose schema-defined machine actions over the same services.
- **Official MCP Adapter** converts an allowlisted subset of abilities to MCP tools/resources/prompts and provides transport.

No Ability writes posts/meta/terms directly and no REST-only business logic is duplicated.

## Initial ability catalog

| Ability | Kind | Persisting | MCP default |
|---|---|---:|---:|
| `mdm/list-diagrams` | query | No | Expose |
| `mdm/get-diagram` | query | No | Expose with source capability rules |
| `mdm/get-diagram-usage` | query | No | Expose |
| `mdm/generate-diagram-candidate` | command/query | No | Expose |
| `mdm/validate-diagram` | validation | No | Expose when validation worker exists |
| `mdm/create-diagram` | command | Yes | Conditional |
| `mdm/update-diagram` | command | Yes | Conditional |
| `mdm/duplicate-diagram` | command | Yes | Conditional |

## Always-valid constraint

An autonomous agent cannot create/update canonical source with a browser validation receipt. Direct mutating abilities are registered/exposed only when the server/headless Mermaid worker uses the same pinned runtime and can issue a trusted worker receipt and derived SVG.

When the worker is unavailable, MCP tools produce candidates and metadata only. A human opens the WordPress editor, validates, previews, and performs the coordinated save. This is a deliberate product state, not degraded security.

## Schema and permissions

Every ability has:

- stable name, label, description, category, input/output JSON Schema;
- strict additional-property policy;
- permission callback using normal WordPress capabilities and object-level access;
- read/write/destructive/idempotent annotations;
- deterministic error codes safe for agents;
- rate/size limits;
- audit and verification tests from WordPress Ability Agent Skills.

Mutation inputs require expected version, idempotency key, validation profile/receipt, and featured SVG envelope where applicable. Agents never receive elevated permissions by virtue of MCP.

## MCP transport and local development

The official adapter may be installed as a standalone WordPress plugin in wp-env. Use one production integration method—standalone plugin or Composer package—not both.

Document and test the selected server name, endpoint/transport, WordPress Application Password/user, and exposed ability list. Keep client credentials outside Git. For local CLI clients, prefer STDIO when supported; for remote clients, use the adapter's authenticated HTTP transport and TLS.

## Approval and audit

Mutating operations are marked as state-changing and require client-side confirmation where the MCP client supports it. WordPress permission callbacks remain the final authority. Record correlation ID, ability, user, target object, result/error, and duration without logging raw diagram source by default.

## Verification gate

Before exposing a new ability:

1. run `wp-abilities-api` guidance;
2. run the Ability audit skill;
3. run the Ability verification skill;
4. execute PHPUnit/integration and Bruno cases;
5. inspect the actual MCP tool schema;
6. test least-privileged denial and version conflicts;
7. update the ability catalog and release notes.
