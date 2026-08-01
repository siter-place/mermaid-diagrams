# WordPress 7 AI Client and Connectors Integration

## Product rule

Mermaid Diagrams consumes WordPress's provider-neutral AI client. It does not store an OpenAI key, implement provider HTTP calls, or bind domain/application services to an OpenAI model name.

For local development, wp-env installs the official OpenAI provider plugin. The key is configured through the WordPress connector/provider mechanism or a local environment/constant. Production administrators choose and configure the provider outside this plugin.

## Supported actions

- **Generate candidate** from a textual description.
- **Repair candidate** after a syntax error, while preserving intent.
- **Explain** an existing diagram.
- **Simplify** source without changing meaning.
- **Review/check** readability, structure, labels, and likely accessibility problems.
- **Propose title and description**.

AI is advisory. Mermaid JS is the syntax authority.

## Application flow

1. The editor sends task, current source when relevant, user instruction, diagram type preference, and non-secret context to a plugin REST endpoint.
2. PHP validates capability, rate/budget policy, input length, and consent/notice requirements.
3. An application service creates a bounded prompt and calls the WordPress AI Client.
4. The provider response is parsed into a candidate DTO; it is never persisted automatically.
5. Browser Mermaid validation runs and displays a diff/preview.
6. The user explicitly applies the candidate.
7. Normal coordinated save persists only after validation and featured-SVG generation.

## Privacy and observability

- Display that selected content will be sent to the configured provider.
- Do not include unrelated post/site content.
- Do not log raw source/prompts/responses by default.
- Log operation type, duration, provider identifier when safe, token/usage metadata when available, error class, user/site IDs, and correlation ID.
- Add configurable rate/budget limits and timeout/cancellation handling.
- Provider outage must not impair manual editing.

## Prompt safety

Treat source and descriptions as untrusted data, not instructions. Prompts must delimit user content, reject requests to reveal secrets/system instructions, and request only Mermaid/source-related output. Output size and format are validated before display.

## Tests

Use a fake WordPress AI client/provider in PHP integration tests. Bruno covers authorization and response contracts without real billable calls. Playwright uses deterministic mocked candidates. A separate opt-in manual smoke test may use the configured OpenAI provider locally; it is excluded from CI and never records the key.
