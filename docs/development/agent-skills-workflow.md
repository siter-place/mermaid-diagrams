# Agent Skills Workflow

The user installs the skill repositories locally; this project does not vendor them.

## WordPress skills installation

```bash
npx skills add WordPress/agent-skills --list
npx skills add WordPress/agent-skills \
  --skill wordpress-router wp-project-triage wp-block-development wp-block-themes \
  wp-plugin-development wp-rest-api wp-interactivity-api wp-abilities-api \
  wp-abilities-audit wp-abilities-verify wp-wpcli-and-ops wp-performance \
  wp-phpstan wp-playground wpds wp-plugin-directory-guidelines blueprint
```

Install at project scope for Cursor so the exact workflow is shared by the repository. Re-run installation/update deliberately and review changes before committing skill files if the team chooses to commit them.

## Bruno skills installation

Install the Bruno agent skill repository and make these available to Cursor:

- `bruno-collection-generator` — collection/environment/request structure;
- `bruno-test-writer` — request assertions, scripts, chaining, negative cases;
- `bruno-ci-setup` — CLI commands, reports, secret injection, CI integration.

Use the repository's current installation instructions; do not copy code snippets from an old version without checking its skill metadata.

## Mandatory skill routing by work type

| Work | Required skills |
|---|---|
| Any phase start | `wordpress-router`, `wp-project-triage` |
| Plugin kernel/lifecycle/settings | `wp-plugin-development` |
| Gutenberg block | `wp-block-development`, `wp-interactivity-api` |
| REST controllers | `wp-rest-api`, `wp-plugin-development` |
| Abilities/MCP | `wp-abilities-api`, then `wp-abilities-audit`, `wp-abilities-verify` |
| Admin UI/design | `wpds`; consult `wp-block-themes` for theme/editor compatibility |
| Cron/fixtures/release ops | `wp-wpcli-and-ops` |
| Performance pass | `wp-performance` |
| PHP static analysis | `wp-phpstan` |
| Portable preview | `wp-playground`, `blueprint` |
| Plugin release | `wp-plugin-directory-guidelines` |
| Bruno collection | all applicable Bruno skills |

## Required phase evidence

Each phase completion report records:

- skill names loaded;
- triage commands run;
- deterministic skill scripts run and results;
- deviations from a skill recommendation and their ADR;
- verification commands and test reports.

Agent Skills guide implementation; project decisions in `docs/00-product-charter-and-decisions.md` remain authoritative where a generic recommendation permits several options.
