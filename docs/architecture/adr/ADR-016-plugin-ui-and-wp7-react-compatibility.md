# ADR-016: UI Component Library and WordPress 7.0 React Runtime Compatibility

## Status
Accepted (Phase 00 Architecture Spike)

## Context
Spike 3 evaluated UI component libraries for WordPress Admin screens and block controls. We investigated `@getdokan/plugin-ui` vs native `@wordpress/components` + `@wordpress/element` under WordPress 7.0 (React 18).

## Decision
1. **Use Native `@wordpress/components` and `@wordpress/element`**: Use native WordPress packages (`wp.components` and `wp.element`) which are shipped with WordPress 7.0 core.
2. **Single React 18 Instance**: Utilize React 18 `createRoot` via `@wordpress/element`. This guarantees zero duplicate React runtime instances and prevents bundle conflicts.
3. **WPDS Alignment**: All admin components adhere to WordPress Design System (WPDS) standards and accessibility guidelines.

## Consequences
- Eliminates external unmaintained or non-public UI package dependencies.
- Minimizes admin JS bundle size by sharing core-provided libraries.
