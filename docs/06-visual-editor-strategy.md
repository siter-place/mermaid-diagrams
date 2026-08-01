# 6. Visual Editor Strategy

## 6.1 Problem statement

Mermaid is a text-based declarative language. A visual canvas represents nodes, edges, positions, groups, and properties. These models overlap, but they are not identical.

A credible visual editor must answer four questions:

1. Which Mermaid constructs can it understand?
2. Which visual changes can it serialize back without semantic loss?
3. What happens to unknown comments, directives, styling, links, classes, and future syntax?
4. Which representation is authoritative after a visual edit?

Without explicit answers, “visual editing” risks corrupting valid diagrams.

## 6.2 Canonical-source rule

The Mermaid source remains canonical at all times.

Visual state is one of:

- **derived:** parsed from the current source and safe to discard;
- **cached layout:** visual positions and selection preferences tied to a source hash;
- **editing draft:** an in-memory representation awaiting serialization and validation.

It is never an independent saved diagram definition that can silently diverge.

## 6.3 Scope by diagram type

### Phase 1 visual support

Support only Mermaid flowcharts declared through accepted forms such as `flowchart` or `graph`, after exact grammar coverage is established.

Potential supported subset:

- nodes with common shapes;
- directed and undirected edges supported by the adapter;
- edge labels;
- node labels;
- subgraphs/groups;
- direction;
- basic class/style assignments only when round-trippable;
- comments preserved where the parser strategy supports them;
- drag positioning stored as optional visual metadata;
- add/delete/duplicate node;
- add/delete edge;
- edit labels and IDs through guarded flows;
- align/distribute;
- automatic layout;
- pan/zoom/multi-select.

### Later adapters

Possible future adapters:

- state diagrams;
- class diagrams;
- ER diagrams;
- mindmaps.

Sequence, Gantt, timeline, pie, quadrant, requirement, Git graph, C4, Sankey, and other types need separate feasibility studies. A shared canvas does not make their semantics equivalent.

## 6.4 Adapter contract

```ts
export interface VisualEditorAdapter<TModel = unknown> {
  readonly id: string;
  readonly version: string;
  readonly supportedDiagramTypes: readonly string[];

  analyze(source: string): Promise<VisualCompatibilityReport>;
  parse(source: string): Promise<VisualParseResult<TModel>>;
  serialize(model: TModel, context: SerializeContext): Promise<SerializeResult>;
  validateRoundTrip(source: string): Promise<RoundTripReport>;
  migrateModel?(model: unknown, fromVersion: string): TModel;
}
```

### Compatibility report

```ts
interface VisualCompatibilityReport {
  mode: 'editable' | 'read-only' | 'unsupported';
  diagramType: string | null;
  supportedFeatures: string[];
  unsupportedFeatures: Array<{
    code: string;
    message: string;
    range?: SourceRange;
    severity: 'warning' | 'blocking';
  }>;
}
```

### Serialization result

```ts
interface SerializeResult {
  source: string;
  changes: ChangeSummary[];
  loss: LossItem[];
  requiresConfirmation: boolean;
}
```

## 6.5 Intermediate representation

Do not use React Flow node objects as the domain model. Define a neutral flowchart IR:

```ts
interface FlowchartModel {
  direction: 'TB' | 'TD' | 'BT' | 'RL' | 'LR';
  nodes: FlowNode[];
  edges: FlowEdge[];
  groups: FlowGroup[];
  styles: StyleRule[];
  comments: PreservedText[];
  extensions: UnknownConstruct[];
  layout?: LayoutState;
}
```

React Flow becomes one adapter from IR to canvas nodes/edges. Mermaid parsing and serialization remain independent of React Flow internals.

Benefits:

- easier unit testing;
- future canvas-library replacement;
- explicit unknown construct preservation;
- controlled serialization;
- less dependency leakage into saved data.

## 6.6 Parsing strategy options

### Option A — Reuse or adapt existing flowchart converter

Inspect `mermaid-reactflow-editor` parsing utilities and selectively adapt MIT-licensed code.

Advantages:

- fastest proof of concept;
- known mapping to React Flow;
- existing node/edge/group behavior.

Risks:

- parser may support only a subset;
- regex or simplified parsing may lose syntax;
- future Mermaid grammar changes;
- code may be tightly coupled to application state.

### Option B — Use Mermaid internal AST/database APIs

Not recommended as the stable foundation. Mermaid public API clearly exposes parse/validation and render, but internal parser/database structures may change across versions.

Advantages:

- potentially richer semantic model.

Risks:

- internal API instability;
- difficult upgrades;
- diagram-specific implementation details;
- unsupported contract.

### Option C — Build a dedicated supported-subset parser

Use a formal parser or carefully bounded grammar for the supported flowchart subset.

Advantages:

- known round-trip guarantees;
- explicit errors and ranges;
- stable internal contract.

Risks:

- larger initial effort;
- must track Mermaid grammar;
- incomplete by design.

### Recommendation

Start with Option A for an architecture spike, but place it behind the adapter interface and validate it against a corpus. If loss rates or grammar ambiguity are unacceptable, move to Option C before calling the feature production-ready.

## 6.7 Round-trip safety

Every adapter needs golden tests:

```text
source
  -> parse to IR
  -> serialize without edits
  -> Mermaid parse validation
  -> semantic comparison
  -> expected preservation report
```

Text equality is not always required because formatting may change. Semantic equality must cover:

- same node IDs and labels;
- same edge endpoints, directions, labels, and types;
- same groups and membership;
- same relevant style/class assignments;
- preserved unknown constructs or an explicit blocking warning.

A no-edit open/save must never silently remove valid source.

## 6.8 Unknown syntax policy

Classify unknown constructs:

- **Preservable:** can be retained as anchored source fragments while editing unrelated known constructs.
- **Read-only compatible:** diagram can be displayed visually but any edit could reorder or lose unknown syntax.
- **Blocking:** visual mode cannot safely represent or serialize it.

When blocking constructs exist:

- keep visual preview available if useful;
- disable mutation controls;
- link the user to the exact source range in code mode;
- do not offer “Save visual changes.”

## 6.9 Node identity and renaming

Mermaid node IDs are semantic references. Visual label changes and ID changes are different actions.

- Editing a label changes display text only.
- Renaming an ID requires checking all edge, class, style, link, and group references.
- Invalid or duplicate IDs are blocked.
- The UI should rarely expose raw ID editing as a casual field; place it under advanced properties.

## 6.10 Layout behavior

Mermaid decides final rendered layout. React Flow positions are useful for editing but not guaranteed to match Mermaid output.

Recommended policy:

- save visual coordinates in `_mdm_visual_model` keyed to source hash and adapter version;
- use coordinates when reopening visual mode;
- do not claim they control final Mermaid front-end layout;
- offer “Apply layout direction” or supported source-level settings separately;
- automatic layout updates the visual canvas and, only where meaningful, Mermaid direction/group ordering;
- never reorder source automatically merely because the user opened visual mode.

A future custom renderer could honor exact coordinates, but that would no longer be standard Mermaid rendering and is outside scope.

## 6.11 Edit transaction

1. User enters Visual tab.
2. Adapter analyzes current source.
3. If editable, parse to IR and load layout cache if source hash matches.
4. User edits the canvas.
5. Changes remain in visual draft state.
6. On Apply, serialize to Mermaid source.
7. Run Mermaid `parse` and render.
8. Run loss/round-trip checks.
9. If safe, replace the code-editor draft and mark the session dirty.
10. User performs normal diagram save through REST.

Visual Apply and server Save are separate actions. This keeps undo and validation understandable.

## 6.12 Undo and redo

- Code editor maintains its own history.
- Visual canvas maintains command-based history for node/edge operations.
- Applying visual changes creates one meaningful code-editor transaction.
- Switching modes must not silently reset history.
- After external source edits, visual history is invalidated and the user is informed.

## 6.13 Visual editor component structure

```text
visual-editor/
├── core/
│   ├── VisualEditorAdapter.ts
│   ├── VisualCompatibilityReport.ts
│   ├── VisualEditorRegistry.ts
│   ├── VisualSession.ts
│   └── roundTrip.ts
├── canvas/
│   ├── VisualCanvas.tsx
│   ├── CanvasToolbar.tsx
│   ├── PropertiesPanel.tsx
│   └── selection.ts
└── adapters/
    └── flowchart/
        ├── FlowchartAdapter.ts
        ├── parser/
        ├── serializer/
        ├── model/
        ├── react-flow/
        ├── layout/
        └── tests/
```

## 6.14 Feature flag and maturity labels

Initial settings:

- visual editor disabled by default in production until the corpus passes;
- Administrators can enable **Flowchart visual editor (Beta)**;
- the editor displays the exact supported subset and a feedback link;
- telemetry is not collected unless separately designed and consented.

Graduation criteria:

- zero destructive no-edit round trips in the supported corpus;
- documented syntax coverage;
- blocking detection for unsupported constructs;
- keyboard-accessible core operations;
- Playwright coverage for create/edit/apply/save/reopen;
- upgrade tests across at least two Mermaid versions;
- acceptable bundle and interaction performance.

## 6.15 Recommendation summary

There is no responsible “universal visual Mermaid editor” shortcut in the supplied projects. The practical enterprise path is:

1. deliver a strong code editor for all Mermaid types;
2. create a visual adapter framework;
3. launch a flowchart-only Beta using React Flow;
4. measure syntax coverage and round-trip safety;
5. add diagram types only through separate adapters and tests.

## 6.18 Delivery decision

Visual editing is a required later feature, but it starts only after the adapted Mermaid Live Editor, REST persistence, validation contract, revisions, and visual test baselines are stable. The visual phase is independently releasable and may initially be hidden behind a feature flag. This sequencing does not reduce the acceptance criteria for loss-aware round trips.
