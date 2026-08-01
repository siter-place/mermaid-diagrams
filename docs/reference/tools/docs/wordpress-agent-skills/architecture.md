# WordPress Agent Skills — Architecture and Technology

## Main architectural characteristics

- A router classifies the repository and requested task.
- Project triage detects WordPress project type, entry points, versions, and existing tooling.
- Specialist skills provide procedures, guardrails, verification steps, and deterministic scripts.
- Project-scoped installation lets the repository pin team guidance; global installation can coexist.
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
