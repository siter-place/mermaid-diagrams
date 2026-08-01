# Implementation Report — Phase 03: Mermaid Validation, Rendering, SVG, and Validation Worker

**Date:** 2026-08-01  
**Phase ID:** Phase 03  
**Phase Title:** Mermaid Validation, Rendering, SVG, and Validation Worker  
**Status:** Completed  
**Version Bump:** 1.1.0 → 1.4.1  

---

## 1. Pre-Implementation Checklist

- [x] Read `AGENTS.md`, `docs/00-product-charter-and-decisions.md`, and active phase spec `docs/plans/03-mermaid-validation-rendering-and-export/`
- [x] Run WordPress project triage detector script (`detect_wp_project.mjs`)
- [x] Verify baseline regression suites pass (`composer test`, `npm run test:unit`, `npm run test:rest`, `npm run test:e2e`)
- [x] Implement `packages/mermaid-runtime` package with initialization, parsing, rendering, receipt generation, SVG sanitization, accessibility, export, and constraints
- [x] Implement PHP `SourceConstraintsPolicy`, `ValidationReceiptVerifier`, and `DownloadPolicyService`
- [x] Wire `DiagramApplicationService`, `DiagramCollectionController`, and `DiagramItemController` to enforce validation receipts and source constraints
- [x] Implement Node validation worker CLI script `tools/validation-worker/validate.mjs`, PHP adapter `NodeValidationWorker`, `wp mdm validate` command, and Bruno receipt helper `tools/bruno/compute-receipt.mjs`
- [x] Create `tests/fixtures/corpus/` with versioned Mermaid corpus and `npm run test:corpus` runner proving browser/worker parity
- [x] Implement unit, REST (Bruno `03 Validation/`), and Playwright render-harness tests per `tests-and-acceptance.md`
- [x] Add `ADR-021-validation-worker-local-subprocess.md` and update architecture/REST/security documentation
- [x] Run post-phase automated versioning closeout (`1.1.0` → `1.4.1`) and sync decision log, changelog, and manifest

---

## 2. Key Decisions & Architectures

1. **`packages/mermaid-runtime` Shared Package:** Built a specialized workspace package exporting locked initialization (`securityLevel: 'strict'`, `startOnLoad: false`), parsing, rendering with async token cancellation, receipt calculation (`sha256:` matching PHP), SVG sanitization, accessibility injection, and source constraint checks.
2. **Strict Server-Side Validation Receipts:** PHP `ValidationReceiptVerifier` enforces that every diagram creation or source mutation includes a valid `ValidationReceipt` matching the source hash, pinned Mermaid version (`11.4.1`), writer profile policy (`browser` vs `worker`), 15-minute max TTL, and source constraint policies. Unvalidated source returns `422 mdm_invalid_mermaid`.
3. **ADR-021 Local Subprocess Validation Worker:** Created `tools/validation-worker/validate.mjs` running `mermaid.parse()` via Node.js JSON stdin/stdout transport, wrapped by `NodeValidationWorker` PHP adapter, powering `wp mdm validate` CLI command and autonomous writer validation.
4. **Download Policy & REST Endpoints:** Added `DownloadPolicyService` and REST endpoints `GET /mdm/v1/diagrams/{id}/source` and `GET /mdm/v1/diagrams/{id}/svg` respecting capabilities and setting flags.
5. **Browser/Worker Corpus Parity:** Created a 13-item diagram corpus in `tests/fixtures/corpus/corpus.json` and `npm run test:corpus` suite asserting identical parsing outcomes in browser runtime and Node worker environments.

---

## 3. Files Created and Modified

### Created Files
- `packages/mermaid-runtime/package.json`
- `packages/mermaid-runtime/src/types.ts`
- `packages/mermaid-runtime/src/init.ts`
- `packages/mermaid-runtime/src/parse.ts`
- `packages/mermaid-runtime/src/render.ts`
- `packages/mermaid-runtime/src/receipt.ts`
- `packages/mermaid-runtime/src/sanitize-svg.ts`
- `packages/mermaid-runtime/src/accessibility.ts`
- `packages/mermaid-runtime/src/export.ts`
- `packages/mermaid-runtime/src/constraints.ts`
- `packages/mermaid-runtime/src/index.ts`
- `src/Diagram/Domain/SourceConstraintsPolicy.php`
- `src/Diagram/Application/Exception/MissingValidationReceiptException.php`
- `src/Diagram/Application/Exception/InvalidValidationReceiptException.php`
- `src/Diagram/Application/Service/ValidationReceiptVerifier.php`
- `src/Diagram/Application/Service/DownloadPolicyService.php`
- `src/Infrastructure/Validation/NodeValidationWorker.php`
- `tools/validation-worker/validate.mjs`
- `tools/bruno/compute-receipt.mjs`
- `tests/fixtures/corpus/corpus.json`
- `tests/js/corpus-parity.test.ts`
- `tests/phpunit/unit/Diagram/ValidationReceiptVerifierTest.php`
- `tests/phpunit/unit/Diagram/SourceConstraintsPolicyTest.php`
- `tests/phpunit/unit/Diagram/DownloadPolicyServiceTest.php`
- `tests/phpunit/integration/ValidationEnforcementTest.php`
- `tests/js/mermaid-runtime/parse.test.ts`
- `tests/js/mermaid-runtime/init.test.ts`
- `tests/js/mermaid-runtime/render-stale.test.ts`
- `tests/js/mermaid-runtime/svg-a11y-sanitize.test.ts`
- `tests/js/mermaid-runtime/export.test.ts`
- `bruno/03 Validation/create-with-worker-receipt.bru`
- `bruno/03 Validation/reject-mismatched-hash.bru`
- `bruno/03 Validation/reject-stale-receipt.bru`
- `bruno/03 Validation/reject-wrong-version.bru`
- `bruno/03 Validation/download-source-policy.bru`
- `bruno/03 Validation/private-source-not-exposed.bru`
- `tests/e2e/playwright/tests/render-harness.spec.ts`
- `docs/architecture/adr/ADR-021-validation-worker-local-subprocess.md`

### Modified Files
- `package.json`
- `src/Support/WordPressErrorMapper.php`
- `src/Diagram/Application/Command/CreateDiagramCommand.php`
- `src/Diagram/Application/Command/UpdateDiagramCommand.php`
- `src/Diagram/Application/Service/DiagramApplicationService.php`
- `src/Rest/Controller/DiagramCollectionController.php`
- `src/Rest/Controller/DiagramItemController.php`
- `src/Admin/Cli/MdmCliCommand.php`
- `src/Settings/Infrastructure/SettingsRepository.php`
- `tests/phpunit/integration/RestApiEndpointsTest.php`
- `bruno/03 Validation/reject-missing-receipt.bru`
- `bruno/02 Diagrams/create-valid-diagram.bru`
- `docs/03-data-model-rest-api.md`
- `docs/07-security-performance-accessibility.md`
- `docs/11-open-decisions.md`
- `docs/MANIFEST.md`
- `CHANGELOG.md`
- `docs/decision-log.md`

---

## 4. Verification Results & Test Output

All verification commands executed cleanly:

1. **PHPUnit Suite (`composer test`):** 52 tests, 212 assertions, 0 failures.
2. **JS Unit Suite (`npm run test:unit`):** 11 test suites, 33 tests passed.
3. **Corpus Parity Suite (`npm run test:corpus`):** 13 test cases passed with 100% browser/worker parity across flowchart, sequence, class, state, er, gantt, pie, mindmap, edge, and security edge cases.
4. **Bruno REST API Suite (`npm run test:rest`):** 22 requests executed, 22 passed, 42/42 assertions passed.
5. **Playwright E2E Suite (`npm run test:e2e`):** 4 passed, 7 skipped contract placeholders.
6. **Versioning Suite (`npm run test:versioning`):** 6 tests passed, version incremented `1.1.0` → `1.4.1`.

---

## 5. Specification Comparison

| Requirement | Specification | Implementation Status |
|---|---|---|
| `packages/mermaid-runtime` JS package | init, parse, render, receipt, sanitize, export, constraints | **Complete** |
| Validation Receipt Enforcement | Reject unvalidated mutations with 422 `mdm_invalid_mermaid` | **Complete** |
| Node.js Validation Worker | Subprocess `validate.mjs` + PHP `NodeValidationWorker` adapter | **Complete** |
| `wp mdm validate` | CLI command wrapping Node worker | **Complete** |
| Corpus Parity | Browser/worker parity on 13 corpus diagrams | **Complete** |
| Download Endpoints & Policy | `/source` and `/svg` routes enforcing permissions and settings | **Complete** |
| ADR-021 Record | Document worker subprocess architecture and trust model | **Complete** |
| Post-Phase Version Bump | 1.1.0 → 1.4.1 via automated versioning skill | **Complete** |
