# Phased Implementation Plan

The implementation is divided into **14 phases**. Execute them in order. Phase 12 is a later visual-editor feature and may be released after the source-editor product while remaining in the same architectural roadmap.

| Phase | Scope | Main release outcome |
|---|---|---|
| [00](./00-development-environment-and-risk-spikes/README.md) | Development Environment and Architecture Risk Spikes | Foundation |
| [01](./01-plugin-kernel-domain-and-storage/README.md) | Plugin Kernel, Domain Model, Storage, and Capabilities | Core foundation |
| [02](./02-rest-api-settings-and-contracts/README.md) | REST API, Settings, Error Model, and Shared Contracts | API foundation |
| [03](./03-mermaid-validation-rendering-and-export/README.md) | Mermaid Validation, Rendering, SVG, and Validation Worker | Rendering foundation |
| [04](./04-diagram-library-shell-and-settings-ui/README.md) | React Diagram Library Shell and Settings UI | Admin UI foundation |
| [05](./05-diagram-library-management-workflows/README.md) | Diagram Library Search, Taxonomy, Bulk Actions, and Preview | Admin feature complete |
| [06](./06-gutenberg-block-and-library-references/README.md) | Gutenberg Block, Inline Diagrams, and Library References | Authoring integration |
| [07](./07-frontend-interactivity-and-downloads/README.md) | Published Frontend Interactivity, Fullscreen, and Downloads | Public experience |
| [08](./08-svelte-mermaid-live-editor-integration/README.md) | Adapted Mermaid Live Editor, Save, Revisions, and Conflicts | Primary editor |
| [09](./09-svg-thumbnails-featured-images-and-usage-index/README.md) | SVG Thumbnails, Featured Images, Usage Index, and WP-Cron | Library scale/operations |
| [10](./10-wordpress-ai-diagram-assistance/README.md) | WordPress AI Client Diagram Assistance | AI-assisted editing |
| [11](./11-abilities-api-and-wordpress-mcp-adapter/README.md) | Abilities API and WordPress MCP Adapter | Agent integration |
| [12](./12-flowchart-visual-editor-adapter/README.md) | Flowchart Visual Editor Adapter | Post-source-editor feature |
| [13](./13-hardening-ci-release-and-documentation/README.md) | Security, Accessibility, Performance, CI, and Release | Release candidate |

## Phase contract

Every phase folder contains four documents:

1. `README.md` — functional scope, prerequisites, deliverables, exclusions, and documentation impact.
2. `technical-spec.md` — implementation boundaries and technical tasks.
3. `tests-and-acceptance.md` — unit/integration/Bruno/Playwright/visual evidence and exit criteria.
4. `master-prompt.md` — standalone Cursor/coding-agent instruction.

A phase is not complete because code exists. It is complete only when tests, evidence, docs, and acceptance outputs are committed.
