# Cursor — Architecture and Technology

## Main architectural characteristics

- Runs against WSL workspace and its toolchain.
- Reads repository instructions such as AGENTS.md.
- Can invoke MCP servers such as Playwright.
- Agent work should be constrained by phase prompts and tests.
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
