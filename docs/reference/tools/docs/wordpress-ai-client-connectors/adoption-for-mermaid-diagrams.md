# WordPress 7 AI Client and Connectors — Adoption for Mermaid Diagrams

## Adopt

- Use for generate, repair, explain, simplify and accessibility metadata suggestions.
- Keep prompt templates in an AI application service.
- Check provider/model capability and return actionable configuration errors.
- Use fake transports/providers for most automated tests.

## Do not adopt blindly

- No direct OpenAI HTTP client.
- No API key in plugin settings or JavaScript.
- No automatic persistence of AI output.
- No assumption every provider supports the same model/capability.
## Acceptance evidence

- The relevant phase cites this research and records the selected version/API.
- Automated tests protect every adopted behavior that affects persistence, permissions, rendering, or public contracts.
- Documentation names the fallback when the tool/dependency is missing or incompatible.
- No unreviewed source copy is introduced.

## Decision state

Planned use: **10, 11, 13.**. Final dependency selection is confirmed in Phase 00 or the first phase that implements the integration.
