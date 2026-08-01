# ADR-007: Use Plugin UI Behind a Local Adapter Layer

- Status: Accepted
- Date: 2026-07-29

## Context

`getdokan/plugin-ui` is a useful WordPress plugin UI foundation, but it is an external GitHub dependency with changing component and React compatibility assumptions.

## Decision

Use selected `plugin-ui` components through thin local abstractions for foundational controls. Pin the dependency and verify current WordPress/React compatibility before broad adoption. Scope all styles under the plugin root.

## Consequences

Positive:

- consistent UI and faster implementation;
- central place for accessibility fixes and dependency adaptation;
- lower switching cost.

Negative:

- adapter maintenance;
- not every component can be used directly;
- compatibility spike is required.
