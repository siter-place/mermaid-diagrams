# ADR-019: Lightweight Container and Service Provider Pattern

## Status
Accepted (Phase 01)

## Context
Phase 01 introduces modular boundaries for Diagram, Admin, and Upgrade bounded contexts. The plugin needs a structured way to register services, bind interface implementations, and run initialization hooks without monolithic bootstrap code or heavy framework dependencies.

## Decision
1. **Lightweight Container**: Implement `WebFalcon\MermaidDiagrams\Bootstrap\Container`, a minimal PHP 8.3 DI container supporting callable bindings and singleton instance resolution.
2. **Service Provider Interface**: Define `WebFalcon\MermaidDiagrams\Bootstrap\ServiceProvider` with `register(Container)` and `boot()` stages.
3. **Provider Registry**: Manage lifecycle execution via `ServiceProviderRegistry` wired inside `Plugin::on_init()`.
4. **Context Isolation**: Each bounded context (`Diagram`, `Admin`, `Upgrade`, etc.) owns its own `ServiceProvider` implementation.

## Consequences
- Keeps root plugin files thin and single-responsibility.
- Decouples interface definitions from WordPress-native implementations.
- Zero runtime overhead or third-party container framework dependencies.
