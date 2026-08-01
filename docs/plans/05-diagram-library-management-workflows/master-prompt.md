# Master Prompt — Phase 05

You are implementing **Phase 05: Diagram Library Search, Taxonomy, Bulk Actions, and Preview** in the Mermaid Diagrams WordPress plugin.

## Repository and product context

- Product: Mermaid Diagrams
- Namespace: `WebFalcon\MermaidDiagrams`
- Prefix: `mdm`
- Minimum WordPress/PHP: 7.0/8.3
- Active plan folder: `docs/plans/05-diagram-library-management-workflows/`
- Prior work expected: Phase 04 shell and Phases 02–03 APIs.

## Required reading

1. `AGENTS.md`
2. `docs/00-product-charter-and-decisions.md`
3. Every file in this phase folder
4. `docs/02-enterprise-architecture.md`
5. `docs/03-data-model-rest-api.md`
6. `docs/07-security-performance-accessibility.md`
7. `docs/09-testing-strategy.md`
8. Relevant ADRs and tool research cited by the phase

## Skills to load and follow

- `wordpress-router`
- `wp-project-triage`
- `wp-plugin-development`
- `wp-rest-api`
- `wpds`
- `wp-performance`
- `bruno-test-writer`

Run the WordPress router and project-triage deterministic scripts before changing files. Inspect current code, package scripts, schemas, and tests; do not guess.

## Mission

Implement only this phase's functional and technical specification as a complete vertical slice. Preserve the final decisions, reuse existing application services/contracts, and do not implement later-phase features early.

## Mandatory quality rules

- Never persist invalid Mermaid source.
- Never bypass WordPress capabilities/nonces/authentication.
- REST/Abilities/UI adapters must not duplicate domain rules.
- AI provider keys remain in WordPress Connectors.
- Keep source code, tests, schemas, and documentation synchronized.
- Add deterministic tests before declaring success.
- Do not weaken Mermaid security or SVG sanitization.
- No secrets, generated reports, node_modules, vendor, browser profiles, or local overrides in Git.

## Execution

1. State the current phase prerequisites you verified.
2. Run triage and existing tests.
3. Implement the smallest coherent sequence of changes.
4. Add the PHP, JS/Svelte, Bruno, and Playwright coverage required by `tests-and-acceptance.md`.
5. Run all relevant checks, including regression suites from previous phases.
6. Update docs and ADRs.
7. Produce a completion report containing changed files, architecture decisions, migrations, commands/results, test artifacts, risks, and exact next-phase handoff.

## Expected output

Complete the phase as a reviewable change set and provide:

- implemented functional behavior and stable contracts;
- production code/build artifacts appropriate to the phase;
- PHPUnit/JavaScript/Svelte/Bruno/Playwright coverage required by the phase;
- updated documentation, schemas, fixtures, ADRs, and source/version locks;
- exact commands and pass/fail results;
- screenshots/reports where required;
- known limitations and the verified handoff assumptions for the next phase.

## Stop and report instead of improvising

Stop when a dependency/version/license is incompatible, a required API is unavailable, validity/security cannot be guaranteed, or the requested behavior contradicts an authoritative decision. Record the evidence and recommended resolution; do not hide the problem behind mocks or undocumented fallbacks.
