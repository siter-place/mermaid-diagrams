# WordPress REST API — Architecture and Technology

## Main architectural characteristics

- Routes are registered on `rest_api_init` under a unique versioned namespace.
- Controllers shape requests/responses and delegate to application services.
- Permission callbacks enforce capabilities per operation.
- Core post/term/media endpoints can be reused selectively; domain workflows may require custom controllers.
## Boundary to Mermaid Diagrams

The reference must stay behind a deliberate boundary. Domain rules, WordPress capability checks, persistence, validation invariants, and public API contracts remain owned by Mermaid Diagrams. A reference tool may provide UI, build, parser, test, or adapter behavior, but it must not become an accidental source of business rules.

## Evaluation checklist

- Verify current runtime and peer dependencies.
- Verify public/stable APIs versus private or experimental internals.
- Record bundle/runtime impact.
- Review license and redistribution terms.
- Identify security and data-flow implications.
- Build a minimal spike before committing to irreversible architecture.
- Define upgrade and fallback behavior.
