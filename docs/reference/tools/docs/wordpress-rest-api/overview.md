# WordPress REST API — Overview

**Canonical source:** https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/  
**Role in this project:** Primary browser-application and integration boundary  
**Research date:** 2026-08-01

## What it is

WordPress route/controller system with JSON schemas, argument validation/sanitization, permission callbacks, authentication, pagination, embedding, and media endpoints.

## Why it was reviewed

Mermaid Diagrams needs this reference to make a concrete decision about its development workflow, runtime architecture, user interface, integration surface, or testing strategy. The source is advisory until an ADR or phase plan adopts a specific behavior.

## Project relevance

- Planned phases: 02 onward.
- Source code is not vendored in this planning package.
- Exact version/commit and license evidence must be recorded in `../../sources-lock.md` during Phase 00.
- Any implementation copied or adapted from upstream requires license/header review and an upgrade strategy.
