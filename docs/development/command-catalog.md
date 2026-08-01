# Target Command Catalog

These commands become executable as their phases land.

```bash
# Environment
npm run env:start
npm run env:stop
npm run env:reset
npm run env:logs

# Build
npm run start
npm run build

# Static quality
npm run lint
composer lint
composer analyse

# Unit/integration
npm run test:unit
composer test

# REST
npm run test:rest
npm run test:rest:html

# Browser
npm run test:e2e
npm run test:e2e:ui
npm run test:e2e:update

# WordPress operations
npx wp-env run cli wp mdm usage reindex --all
npx wp-env run cli wp mdm validate --all
```

No phase may silently change command meaning. Update this catalog and CI together.
