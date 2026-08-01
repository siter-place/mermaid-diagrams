# ADR-003: Diagram-Type Visual Editor Adapters

- Status: Accepted
- Date: 2026-07-29

## Context

Mermaid diagram types have different grammars and semantics. A React Flow flowchart editor cannot safely edit sequence, Gantt, class, state, ER, and all other Mermaid diagrams.

## Decision

Introduce a `VisualEditorAdapter` registry. Each adapter declares supported diagram types and syntax, parses to a neutral intermediate representation, serializes back to Mermaid, and emits a compatibility/loss report.

The first adapter supports a tested flowchart subset and ships as Beta. Mermaid source remains canonical. Unsupported or lossy content is read-only or blocked from visual mutation.

## Consequences

Positive:

- truthful feature scope;
- safe incremental support;
- isolated parser/serializer tests;
- future diagram types can be added independently.

Negative:

- visual support is incomplete by design;
- adapter development is substantial;
- users need clear mode-compatibility messages.

## Rejected alternatives

- one generic node-edge editor for every Mermaid type;
- saving React Flow JSON as the primary diagram;
- depending on Mermaid internal AST as a stable public contract.
