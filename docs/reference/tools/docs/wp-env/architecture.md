# wp-env — Architecture and Technology

## Main architectural characteristics

- Repository can be mounted as an active plugin with `plugins: ["."]`.
- Core, PHP version, plugins, themes, config constants, mappings, and ports are declarative.
- Lifecycle scripts run after start/reset/cleanup/destroy and must be idempotent.
- Commands execute inside containers via `wp-env run`.
- Current guidance uses separate config files for parallel/test environments.
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
