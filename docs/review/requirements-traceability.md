# Requirements Traceability Matrix

| Requirement | Authoritative specification | Architecture/API | Delivery phase | Primary test evidence |
|---|---|---|---|---|
| Gutenberg inline/reference block, title/description, create/select diagram | `01-functional-specification.md` | `03-data-model-rest-api.md`, `04-frontend-applications-and-ux.md` | 06 | JS component/unit, REST/Bruno create/search, Playwright editor workflows |
| Diagram CPT, multiple hierarchical categories, tags | `00-product-charter-and-decisions.md`, `01-functional-specification.md` | `02-enterprise-architecture.md`, `03-data-model-rest-api.md` | 01, 05 | PHPUnit repository/taxonomy, Bruno terms/bulk, Playwright library |
| React library with list/table, filters, preview, bulk Add/Remove/Replace | `01-functional-specification.md` | `04-frontend-applications-and-ux.md` | 04–05 | Component/a11y, Bruno bulk, Playwright/visual snapshots |
| Adapted Mermaid Live Editor Svelte page | `00-product-charter-and-decisions.md` | `integrations/mermaid-live-editor-adaptation.md`, ADR-008 | 00 spike, 08 | Svelte unit, REST conflicts, Playwright editor/visual |
| Persist only valid Mermaid source | `00-product-charter-and-decisions.md` | `03-data-model-rest-api.md`, ADR-009 | 03 and all writers | Mermaid parity/unit, forged receipt Bruno cases, Playwright invalid-save denial |
| Browser SVG created during save and assigned as featured image | `00-product-charter-and-decisions.md` | `03-data-model-rest-api.md` §3.20, ADR-011/013 | 09 | PHP compensation tests, Bruno source+SVG failures, Playwright retry/local recovery |
| Public zoom/pan/fit/reset/fullscreen/download | `01-functional-specification.md` | ADR-004, `04-frontend-applications-and-ux.md` | 07 | Interactivity unit/e2e, keyboard/a11y, visual snapshots |
| `.mmd` globally enabled but reducible; SVG supported; PNG deferred | `00-product-charter-and-decisions.md` | block attributes/API settings | 02, 06–07 | Settings/API permission tests, Playwright controls |
| Latest accessible reference; private/draft does not render publicly | `00-product-charter-and-decisions.md` | dynamic block/query policy | 06–07 | PHPUnit policy, Bruno permissions, publish/frontend Playwright |
| Usage counts stored in DB and updated/reconciled by WP-Cron | `00-product-charter-and-decisions.md` | `03-data-model-rest-api.md` §3.19 | 09 | Cron/repository tests, WP-CLI, Bruno usage, deletion warning UI |
| AI generation/checking through WordPress 7 AI Client/Connectors | `00-product-charter-and-decisions.md` | `integrations/wordpress-ai-client-connectors.md`, ADR-010 | 10 | Fake provider integration, mocked Bruno/Playwright, privacy/rate tests |
| Abilities API and official MCP Adapter for chat clients | `00-product-charter-and-decisions.md` | `integrations/abilities-api-and-mcp.md`, ADR-010 | 11 | Ability audit/verify, PHPUnit schemas, Bruno, actual MCP discovery smoke |
| Direct agent writes only with trusted Mermaid worker | validity invariant | `integrations/abilities-api-and-mcp.md`, `03-data-model-rest-api.md` | 03, 11 | worker parity/signing, candidate-only fallback, mutation denial tests |
| Flowchart visual editor later, loss-aware | `00-product-charter-and-decisions.md` | `06-visual-editor-strategy.md`, ADR-003 | 12 | parser/serializer property tests, loss reports, Playwright round trips |
| WSL2/Docker/Cursor/wp-env first step | user decision | `development/wp-env-wsl2-setup.md`, ADR-012 | 00 | clean bootstrap, smoke screenshot, second clean rebuild |
| Playwright visual regression and MCP-assisted exploration | user decision | `testing/playwright-visual-regression.md` | 00, 04–13 | committed tests/snapshots/reports; MCP not standalone evidence |
| Bruno REST E2E and Agent Skills | user decision | `testing/bruno-rest-e2e.md`, `development/agent-skills-workflow.md` | 00, 02–13 | Safe Mode collection reports and recorded skill workflow |
