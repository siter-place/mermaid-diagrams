# Mermaid Diagrams — project scaffold

This repository is the implementation starting point for **Mermaid Diagrams**, a WordPress 7.0+ plugin by WebFalcon.

The package currently contains architecture, product specifications, environment configuration, test scaffolds, REST collection scaffolds, and phased coding-agent prompts. It intentionally does **not** contain a finished plugin implementation or vendored copies of reference projects.

## Product identity

- Plugin name: **Mermaid Diagrams**
- PHP namespace: `WebFalcon\MermaidDiagrams`
- Technical prefix: `mdm`
- Block name: `mdm/diagram`
- REST namespace: `mdm/v1`
- Minimum WordPress: `7.0`
- Minimum PHP: `8.3`

## Start here

1. Read `docs/00-product-charter-and-decisions.md`.
2. Follow `docs/development/wp-env-wsl2-setup.md`.
3. Install the project-scoped Agent Skills described in `docs/development/agent-skills-workflow.md`.
4. Configure Playwright MCP from `.cursor/mcp.example.json` and validate Bruno from `bruno/README.md`.
5. Execute the phases under `docs/plans/` in numeric order.

## Runtime surfaces

1. Gutenberg block application — React/WordPress packages.
2. Diagram Library administration application — React, informed by `getdokan/plugin-ui` patterns.
3. Dedicated Diagram Editor — an adapted, pinned Mermaid Live Editor Svelte build.
4. Published-page controls — WordPress Interactivity API store and directives.
5. Later visual editor — adapter-based flowchart editor, delivered after the source editor is stable.

## Local commands

The scripts are contracts for Phase 00. Dependencies and lockfiles must be created before they become executable.

```bash
npm install
npm run env:start
npm run build
npm run test
```

See `docs/development/command-catalog.md` for the complete target command set.


## Package outputs

The final delivery provides both a full project scaffold ZIP and a docs-only ZIP. Third-party tool source and Agent Skill repositories are intentionally excluded; install them locally using the documented commands.
