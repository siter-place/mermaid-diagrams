# ADR-006: REST for Client Mutations, PHP for WordPress Lifecycle

- Status: Accepted
- Date: 2026-07-29

## Context

The product asks to use REST “100%” and includes two React applications and one Svelte editor application. WordPress registration, block rendering, capabilities, migrations, and activation are not client/server mutations and cannot be usefully replaced with REST.

## Decision

All mutations and data queries initiated by JavaScript applications use authenticated REST. PHP handles WordPress lifecycle, registration, dynamic rendering, permissions, migrations, and server-side policies. Core REST endpoints are reused where sufficient; custom `mdm/v1` routes supply optimized contracts and conflict/bulk behavior.

## Consequences

Positive:

- decoupled React applications;
- testable contracts;
- native WordPress authentication;
- no misuse of REST for server lifecycle.

Negative:

- both core and custom REST behavior must be documented;
- additional controller/schema tests are required.
