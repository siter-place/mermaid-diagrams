# ADR-018: WordPress Abilities API and MCP Adapter Integration Strategy

## Status
Accepted (Phase 00 Architecture Spike)

## Context
Spike 5 analyzed the WordPress MCP Adapter (`0.5.0`) and Abilities API integration for machine-executable actions.

## Decision
1. **Companion Plugin Requirement**: Recommend or detect the WordPress MCP Adapter plugin rather than bundling the adapter via Composer, preventing global class/function conflicts.
2. **Strict Action Hook Lifecycle**:
   - Register ability categories on `wp_abilities_api_categories_init`.
   - Register individual abilities on `wp_abilities_api_init`.
3. **Graceful Fallback**: Wrap ability registrations in `function_exists('wp_register_ability')` checks so the plugin functions perfectly without MCP Adapter active.

## Consequences
- Prevents PHP fatal errors when MCP Adapter is installed independently.
- Ensures full compliance with WordPress 7.0 Abilities API registration lifecycle.
