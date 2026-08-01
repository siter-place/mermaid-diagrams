# Mermaid Live Editor — Architecture and Technology

## Main architectural characteristics

- Svelte application owns source editor and live preview.
- Upstream includes editor integrations and rendering/export utilities.
- SvelteKit application infrastructure must be reduced/adapted to static WordPress admin assets.
- Local persistence/share features are replaced or wrapped by WordPress REST persistence.
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
