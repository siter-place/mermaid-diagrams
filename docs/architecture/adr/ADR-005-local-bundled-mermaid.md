# ADR-005: Bundle and Pin Mermaid Locally

- Status: Accepted
- Date: 2026-07-29

## Context

Mermaid is available through npm and public CDNs. A WordPress plugin may run under restrictive CSP, without outbound internet, or in regulated environments. Unversioned CDN loading also creates version drift.

## Decision

Install Mermaid through the JavaScript package manager, pin it with a lockfile, include built assets/chunks in the plugin ZIP, and record the runtime version in diagnostics and cache keys.

No external CDN is required for normal operation.

## Consequences

Positive:

- reproducible and offline-capable releases;
- controlled security updates;
- predictable rendering;
- simpler CSP/privacy posture.

Negative:

- larger plugin ZIP;
- the project owns dependency upgrades and compatibility testing.

## Rejected alternatives

- unversioned jsDelivr URL;
- load latest Mermaid at runtime;
- require administrators to configure a CDN.
