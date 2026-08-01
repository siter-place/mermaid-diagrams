# Phase 10 Technical Specification

## Architecture boundary

Implement this phase through the existing modular-monolith layers. Presentation adapters (REST, React, Svelte, block, Interactivity, Ability, WP-CLI) call application services. Application services enforce use-case rules and use repository/service interfaces. WordPress persistence and external integrations live in Infrastructure. Domain code does not depend on WordPress globals.

## Implementation work

- [ ] Create provider-neutral AI application service using WordPress 7 AI Client and Connectors.
- [ ] Define prompt templates, structured output expectations, model capability checks, timeouts, cancellation, rate/cost guardrails, and error mapping.
- [ ] Add Live Editor AI actions with diff/candidate preview and explicit Apply.
- [ ] Optionally add Gutenberg inline repair/generate entry points only if they reuse the same service.
- [ ] Never expose connector keys to JavaScript; call server REST route/application service.
- [ ] Apply privacy policy: explicit action, explain data sent, minimal logging, no raw source in logs by default.
- [ ] Use fake provider/transport for automated tests; external OpenAI smoke is manual/protected.

## Cross-cutting requirements

- Use `WebFalcon\MermaidDiagrams` and `mdm` identifiers consistently.
- Keep WordPress/PHP minimums at 7.0/8.3.
- Validate and sanitize input early; escape output late; always check capabilities.
- Use stable error codes and schemas.
- Add observability without secrets/raw source by default.
- Keep migrations and background work idempotent.
- Preserve backward compatibility of already released block/API/data contracts or add explicit migration/deprecation.
- Update docs/tests in the same change.

## Completion artifacts

- Production code and built assets for this slice.
- Unit/integration/API/browser tests listed in `tests-and-acceptance.md`.
- Updated contracts/schemas and generated artifacts.
- ADR for any significant choice made during implementation.
- Phase completion report with commands and evidence.
