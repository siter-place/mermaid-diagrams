# ADR-010: WordPress AI Client, Abilities API, and MCP Adapter

**Status:** Accepted

## Decision

Use WordPress 7.0 AI Client and Connectors for all provider calls. Register narrow Mermaid Diagrams abilities that delegate to application services. Expose only approved abilities through the official WordPress MCP Adapter.

## Consequences

- Provider keys live in WordPress Connectors, never plugin options.
- OpenAI is an initial connector, not a hard dependency in domain code.
- REST and abilities share authorization and business rules.
- AI output is candidate content until Mermaid validation passes.
- Ability schemas, annotations, permissions, and MCP exposure are audited and verified before release.
