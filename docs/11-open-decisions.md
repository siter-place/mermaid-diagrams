# 11. Final Decisions and Remaining Delivery Spikes

All product decisions supplied on 2026-08-01 are accepted. This file records the decision state and the few technical spikes that choose an implementation without changing product behavior.

| Area | Final decision |
|---|---|
| Product identity | Mermaid Diagrams; `WebFalcon\MermaidDiagrams`; prefix `mdm` |
| Categories | Multiple hierarchical categories; explicit Add/Remove/Replace bulk actions |
| Public source download | Enabled globally by default; block may reduce; administrator may disable control |
| Diagram validity | Invalid source is never persisted; Mermaid JS validation is mandatory |
| Dedicated editor | Adapted Mermaid Live Editor Svelte application |
| Visual editor | Required later phase; flowchart-first; not a prerequisite for source-editor release |
| Library launch view | Table/list and preview panel; grid deferred |
| Thumbnail | Browser-generated, dual-sanitized SVG committed with source in one coordinated save |
| Usage data | Database-backed reverse index/count, maintained and reconciled by WP-Cron |
| Legacy migration | Out of scope for 1.0 |
| Private diagram reference | No public render; editor and publication warnings |
| Reference version | Latest accessible current version |
| Theme | Global default then diagram default; no block theme override |
| Fullscreen | Native API with accessible dialog fallback |
| Export | SVG default/required; `.mmd` according to policy; PNG deferred |
| Uninstall | Preserve by default; opt-in destructive cleanup |
| Multisite | Per-site data/settings |
| Collaboration | No real-time collaboration |
| AI | WordPress 7 AI Client/Connectors; initial OpenAI connector; no direct key storage |
| Abilities/MCP | Abilities API plus official WordPress MCP Adapter |
| Minimum platform | WordPress 7.0 and PHP 8.3 |

## Required technical spikes in Phase 00

These are implementation choices, not open product questions:

1. Pin the exact Mermaid and Mermaid Live Editor commits/versions and record licenses.
2. Verify the selected Live Editor build can be emitted as a self-contained admin asset without a standalone SvelteKit server.
3. Decide whether the project carries a maintained patch set, a fork, or a build-time adapter package; upgrades must be repeatable.
4. Verify `getdokan/plugin-ui` peer dependencies against WordPress 7.0’s React runtime and isolate it through an adapter layer.
5. Implement a proof-of-concept validation worker using the same Mermaid package version. Direct MCP persistence is disabled if this profile is not deployed.
6. Prove narrowly scoped, sanitized SVG media upload and featured-image display in WordPress 7.0.
7. Establish deterministic Playwright visual baselines under WSL2/Docker.
8. Validate the standalone MCP Adapter plugin and Composer-package approaches; ship only one in production to avoid duplicate loading.
