# 10. Coding Agent Operating Brief

## Mission

Build **Mermaid Diagrams** as an enterprise-grade WordPress 7.0 plugin using the phase plans in `docs/plans/`. Work on one phase at a time and do not collapse the architecture into a collection of global functions or one large frontend bundle.

## Mandatory workflow

1. Read `AGENTS.md`, `docs/00-product-charter-and-decisions.md`, the current phase, and every referenced ADR.
2. Run WordPress project triage before editing.
3. Load and follow the phase's required WordPress Agent Skills and Bruno Agent Skills.
4. Inspect existing code and tests; never infer an API that can be discovered.
5. Implement the smallest complete vertical slice for the phase.
6. Add/adjust tests before declaring completion.
7. Run static checks, unit/integration tests, Bruno REST tests, and applicable Playwright tests.
8. Update architecture/API/testing docs in the same change set.
9. Report commands, results, changed files, decisions, and remaining risks.

## Non-negotiable boundaries

- Name: Mermaid Diagrams.
- PHP namespace: `WebFalcon\MermaidDiagrams`.
- Prefix: `mdm`.
- Minimum WordPress/PHP: 7.0/8.3.
- Dynamic block registered from `block.json`, API version 3.
- REST is the primary boundary for browser applications.
- Published interactions use the Interactivity API, not a public React root.
- Library is React; dedicated source editor is adapted Mermaid Live Editor/Svelte.
- Mermaid source is canonical. Persisted source is always valid.
- Security level and dangerous configuration are server-controlled and cannot be weakened by diagram source or client payload.
- AI uses WordPress AI Client/Connectors only.
- Abilities and REST call shared application services.
- Visual editing is adapter-based and cannot silently discard unsupported syntax.
- No direct edits to WordPress Core, Gutenberg, Mermaid, or reference repositories.

## Stop conditions

Stop the phase and record an ADR/risk when:

- a dependency requires React/Svelte/PHP/WordPress versions outside the declared platform;
- a save route can persist unvalidated Mermaid source;
- a visual conversion cannot prove a safe round trip;
- an ability exposes data or mutations beyond the current user's capabilities;
- SVG cannot pass both client and server sanitization;
- a change requires vendoring an unreviewed dependency or violates its license;
- tests cannot be made deterministic.

Do not hide these conditions with mocks in production code.
