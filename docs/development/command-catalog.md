# Target Command Catalog

These commands are verified and executable as Phase 01 lands.

```bash
# Environment
npm run env:start
npm run env:stop
npm run env:reset
npm run env:logs
npm run wp:setup

# Build
npm run start
npm run build

# Static quality
npm run lint
npm run lint:js
npm run lint:css
npm run lint:md
composer lint
composer lint:fix
composer analyse

# Unit/integration
npm run test:unit
composer test
npx wp-env run cli --env-cwd=wp-content/plugins/mermaid-diagrams vendor/bin/phpunit

# REST (Bruno)
npm run test:rest
npm run test:rest:html

# Browser (Playwright)
npm run test:e2e
npm run test:e2e:ui
npm run test:e2e:update

# WordPress CLI operations (Phase 01+)
npx wp-env run cli wp mdm status
npx wp-env run cli wp mdm capabilities repair
npx wp-env run cli wp mdm usage reindex (stub — Phase 09)
npx wp-env run cli wp mdm validate (stub — Phase 03)
```

No phase may silently change command meaning. Update this catalog and CI together.
