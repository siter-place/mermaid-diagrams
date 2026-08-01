# ADR-014: Mermaid Version Pinning and Validation Worker Viability

## Status
Accepted (Phase 00 Architecture Spike)

## Context
Mermaid JS is the primary parsing, syntax validation, and SVG rendering engine for the plugin. Unpinned dependencies can introduce breaking syntax changes or altered error formats. We conducted Spike 1 to evaluate:
1. Exact npm version pinning.
2. `mermaid.parse()` API behavior, success payloads, and error exception structures in Node environments (for headless/worker validation) vs browser environments.
3. `mermaid.render()` requirements and DOM dependencies.

## Decision
1. **Pin Mermaid to exact version `11.4.1`** in `package.json` and lockfile.
2. **Use `mermaid.parse()` for pre-flight validation**: `mermaid.parse()` operates statelessly in Node.js and browser environments without requiring SVG layout methods (`getBBox()`).
3. **Isolate `mermaid.render()` to browser environments**: Rendering requires DOM layout capabilities (`SVGElement.prototype.getBBox()`). In headless unit tests (jsdom), `getBBox` must be polyfilled/mocked or executed in real Playwright browser instances.
4. **Validation Worker Pattern**: The validation worker concept is confirmed viable using `mermaid.parse()` on both server/Node worker and client thread.

## Consequences
- Guarantees predictable diagram parsing and error messages across WP Admin and front-end rendering.
- Prevents runtime breaking changes from upstream Mermaid updates.
