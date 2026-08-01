# Bruno REST Contract Collection

This folder is committed inside the plugin repository and becomes the authoritative black-box REST workflow suite.

## Secrets

Copy `.env.example` to ignored `.env` and provide:

```dotenv
BRUNO_BASE_URL=http://localhost:8888
BRUNO_USERNAME=mdm_api_test
BRUNO_APPLICATION_PASSWORD=xxxx xxxx xxxx xxxx xxxx xxxx
```

The `Local` environment reads process environment variables. Create a dedicated least-privileged WordPress user/Application Password in the Phase 00 bootstrap. Never reuse an administrator password in CI.

## Run

```bash
npx bru run bruno --env Local \
  --reporter-json bruno/reports/results.json \
  --reporter-junit bruno/reports/results.xml
```

Bruno CLI 3.x Safe Mode is the default and sufficient for this collection. Do not add `--sandbox=developer` unless a reviewed request script genuinely needs external packages/filesystem access.

## Folder activation

The REST Index smoke is usable in Phase 00. Later folders contain contract scaffolds and are completed/activated by their phase. Keep sequence independent where possible; workflow folders store generated IDs/tokens as collection variables and run cleanup.
