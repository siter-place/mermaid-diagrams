# Cross-Tool Synthesis and Responsibility Map

## 1. Development and agent workflow

Cursor is the working environment. WordPress Agent Skills route and constrain WordPress changes; Bruno Agent Skills constrain API collection work. wp-env supplies the local WordPress/Docker runtime. Playwright MCP is exploratory; Playwright Test is committed evidence. Bruno is the executable REST contract.

No one of these replaces another:

- Agent Skills do not execute tests automatically.
- Playwright MCP does not replace Playwright Test.
- Bruno does not replace PHP/JS unit tests or browser UI tests.
- wp-env does not define production deployment architecture.

## 2. Product runtime

WordPress REST is the shared HTTP boundary for React and Svelte applications. The Interactivity API is the public-page behavior system. The Abilities API is a machine-readable adapter to the same application services. MCP Adapter exposes an allowlisted subset of abilities. AI Client/Connectors provides provider-neutral generation/repair. OpenAI provider is configuration, not domain code.

## 3. UI composition

- Gutenberg block: WordPress React packages and block.json.
- Diagram Library: React; plugin-ui and DataViews are evaluated as UI architecture references.
- Dedicated editor: adapted Mermaid Live Editor/Svelte.
- Published page: Interactivity API.
- Later visual mode: React Flow-inspired adapter, not a replacement canonical model.

Share contracts, tokens and Mermaid runtime behavior. Do not force shared framework components between React and Svelte.

## 4. Mermaid lifecycle

Mermaid JS is the parser/renderer and validity authority. Live Editor provides the source editing experience. Merpress/WP Mermaid provide WordPress lessons. React Flow editor provides visual-adapter lessons. jsDelivr is not used at runtime; the dependency is locally bundled and pinned.

## 5. Validation conflict resolved

Browser-only validation is enough for first-party UI workflows but cannot guarantee autonomous MCP writes. The final architecture therefore has three modes:

1. Browser validation and persistence.
2. Validation-worker-backed direct Ability/MCP persistence.
3. Candidate-only Ability/MCP behavior when the worker is absent.

This preserves the “always valid persisted source” invariant without pretending PHP can execute Mermaid JS.

## 6. High-value adoption summary

Adopt:

- wp-env declarative, idempotent local setup;
- plugin-ui provider/settings/CSS isolation patterns;
- REST controller schemas and permissions;
- Live Editor source/preview workflow through a WordPress adapter;
- Interactivity API for small frontend controls;
- Bruno workflow collections and reports;
- Playwright visual baselines with deterministic render markers;
- Abilities/MCP as adapters to shared use cases;
- WordPress AI provider abstraction;
- React Flow-style visual graph only behind loss-aware adapters.

Reject:

- CDN runtime dependencies;
- unrestricted SVG upload;
- direct provider key storage;
- duplicated REST/Ability business logic;
- visual editing for unsupported Mermaid syntax;
- generic shortcodes/legacy migration in 1.0;
- one giant frontend bundle or framework crossing.
