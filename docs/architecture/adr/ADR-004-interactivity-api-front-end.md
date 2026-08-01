# ADR-004: WordPress Interactivity API for Published-Page Controls

- Status: Accepted
- Date: 2026-07-29

## Context

Published diagrams require zoom, pan, reset, fit, fullscreen, and download controls. Shipping React on the public front end is unnecessary and inconsistent with the requested WordPress-native direction.

## Decision

Use a dynamic block with server-rendered semantic markup and a `viewScriptModule` powered by the WordPress Interactivity API. React remains limited to Gutenberg and custom administration applications.

## Consequences

Positive:

- WordPress-native front-end state/actions;
- lower runtime overhead than a React app;
- conditional loading through block metadata;
- server-first accessible markup.

Negative:

- shared React components cannot be reused directly on the front end;
- some renderer/export logic needs framework-neutral modules or adapters;
- minimum WordPress must include the required Interactivity API capabilities.

## Rejected alternatives

- hydrate a React application for every diagram;
- use ad-hoc global DOM event scripts;
- render all interaction controls through an iframe.
