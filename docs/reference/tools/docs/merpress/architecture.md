# Merpress — Architecture and Technology

## Main architectural characteristics

- Block owns source and preview.
- WordPress build metadata registers editor assets.
- Client rendering handles Mermaid and export behavior.
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
