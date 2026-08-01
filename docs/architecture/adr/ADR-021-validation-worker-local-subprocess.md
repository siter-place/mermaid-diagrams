# ADR-021: Node.js Validation Worker as Local Subprocess

- **Status:** Accepted
- **Date:** 2026-08-01
- **Deciders:** Mermaid Diagrams Core Architecture Team
- **Consulted:** Security Team, REST API Working Group

---

## Context and Problem Statement

When creating or modifying Mermaid diagrams via autonomous channels (such as WP-CLI, background sync tasks, or third-party API integrations without a browser DOM available), the system requires server-side validation of Mermaid source syntax and structure without trusting unverified input.

In browser environments, `@mermaid-diagrams/runtime` runs `mermaid.parse()` directly in the client DOM and generates a browser-profiled `ValidationReceipt`. However, server-side PHP lacks native JavaScript execution capabilities for full AST parsing and rendering of Mermaid.js.

## Decision Drivers

1. **Autonomous Validation Integrity:** Non-browser writes must validate source code against `mermaid.parse()` and source security policies before persisting to database.
2. **Version Parity:** Validation on the server must produce identical results to browser runtime validation, using the pinned `mermaid@11.4.1` engine.
3. **Operational Simplicity:** Avoid requiring long-running microservices or external HTTP daemon infrastructure in standard WordPress web hosting environments.
4. **Fail-Closed Security:** Mismatched, stale, unvalidated, or forbidden Mermaid syntax must be rejected with `422 mdm_invalid_mermaid`.

## Considered Options

- **Option 1:** Local Node.js Subprocess (`node tools/validation-worker/validate.mjs`) via PHP `proc_open` with JSON stdin/stdout.
- **Option 2:** Long-running HTTP/gRPC Node.js microservice daemon.
- **Option 3:** V8js / V8 PHP extension embedded in web server.
- **Option 4:** PHP regex/lexer fallback (incomplete parsing and high drift risk).

## Decision Outcome

Chosen Option: **Option 1 (Local Node.js Subprocess)**.

### Subprocess Protocol & Transport

1. **Binary & Entry:** Executed via Node.js (`node tools/validation-worker/validate.mjs`) using the project's root lockfile dependency `mermaid@11.4.1`.
2. **Transport:** Synchronous JSON via `stdin` and `stdout`.
3. **Payload:**
   - **Input (stdin):** `{"source": "flowchart LR\n  A --> B", "profile": "worker"}`
   - **Output (stdout):** `{"valid": true, "diagramType": "flowchart", "sourceHash": "sha256:...", "mermaidVersion": "11.4.1", "validatedAt": "...", "diagnostics": []}`

### Trust Model & Validation Receipt Rules

- **Browser Profile (`profile: "browser"`):** Valid for authenticated user sessions in web UI.
- **Worker Profile (`profile: "worker"`):** Required when mutations occur via autonomous or server-side writers (`X-MDM-Writer-Profile: autonomous` / `worker`).
- **PHP Verification:** `ValidationReceiptVerifier` enforces:
  - Source hash matching (`sha256:` prefix).
  - Version matching (`MDM_MERMAID_VERSION`).
  - Staleness window checks (15-minute maximum TTL).
  - Source constraint checks (denying `click`, `callback`, `securityLevel` overrides, null bytes, `<script>` tags).

### Fallback & Failure Behavior

If Node.js or `tools/validation-worker/validate.mjs` is unavailable:
- **Autonomous/Worker Writes:** Fail closed with `422 mdm_invalid_mermaid`.
- **First-Party Browser Writes:** Continue to function seamlessly using browser-generated receipts (`profile: "browser"`).

## Consequences

- **Positive:** Guarantees 100% parsing parity between browser and server validation workers.
- **Positive:** Zero additional long-running process overhead on web servers.
- **Negative:** Subprocess execution overhead (~100–300ms) for CLI/autonomous validation tasks.
