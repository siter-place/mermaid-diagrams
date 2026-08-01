# Architecture Overview Diagrams

These diagrams are explanatory architecture models for the implementation team. They are not generated plugin content.

## Component view

```mermaid
flowchart TB
  subgraph WordPress[WordPress Runtime]
    Bootstrap[Plugin Bootstrap and Service Providers]
    CPT[Diagram CPT and Taxonomies]
    REST[REST Controllers mdm/v1]
    BlockPHP[Dynamic Block Renderer]
    Settings[Settings and Migrations]
    Repo[WordPress Diagram Repository]
  end

  subgraph Admin[WordPress Administration]
    GB[Gutenberg Block React App]
    Library[Diagram Library React App]
    Editor[Mermaid Live Editor Svelte App]
    UI[plugin-ui Adapter Components]
  end

  subgraph Front[Published Page]
    Markup[Server-rendered Figure and JSON Payload]
    IAPI[Interactivity API Store]
    Controls[Zoom Pan Fit Fullscreen Download]
  end

  subgraph SharedJS[Shared TypeScript Services]
    API[REST API Client and DTOs]
    Mermaid[Mermaid Parse and Render Service]
    Export[Source and SVG Export]
    Thumb[Featured SVG Save Envelope]
    Visual[Visual Editor Adapter Registry]
    Flow[Flowchart React Flow Adapter]
  end

  Bootstrap --> CPT
  Bootstrap --> REST
  Bootstrap --> BlockPHP
  Bootstrap --> Settings
  REST --> Repo
  BlockPHP --> Repo

  GB --> UI
  Library --> UI
  Editor --> UI
  GB --> API
  Library --> API
  Editor --> API
  API --> REST

  GB --> Mermaid
  Library --> Mermaid
  Editor --> Mermaid
  Editor --> Visual
  Visual --> Flow
  Mermaid --> Export
  Mermaid --> Thumb
  Thumb --> API

  BlockPHP --> Markup
  Markup --> IAPI
  IAPI --> Mermaid
  IAPI --> Controls
  Controls --> Export
```

## Inline-to-library conversion

```mermaid
sequenceDiagram
  actor Author
  participant Block as Gutenberg Block
  participant API as mdm/v1 REST
  participant App as CreateDiagram Use Case
  participant WP as WordPress Repository

  Author->>Block: Enter inline source and metadata
  Block->>Block: Validate, render, and sanitize SVG
  Author->>Block: Save to diagram library
  Block->>API: POST source + validation + featured SVG + idempotency key
  API->>App: CreateDiagram command
  App->>WP: Create mdm_diagram
  WP-->>App: Diagram ID and version
  App-->>API: Normalized detail DTO
  API-->>Block: 201 Created
  Block->>Block: Set mode=reference and diagramId
  Block->>Block: Clear canonical inline source
  Block-->>Author: Shared diagram created
```

## Reference rendering

```mermaid
sequenceDiagram
  participant WP as WordPress Render Pipeline
  participant Block as DiagramBlockRenderer
  participant Repo as Diagram Query Service
  participant Browser
  participant Store as Interactivity API
  participant Mermaid as Bundled Mermaid Runtime

  WP->>Block: Render block attributes
  Block->>Repo: Resolve referenced diagram
  Repo-->>Block: Authorized canonical source
  Block-->>WP: Semantic markup + safe JSON payload
  WP-->>Browser: HTML response
  Browser->>Store: data-wp-init
  Store->>Mermaid: parse and render
  Mermaid-->>Store: SVG or diagnostic
  Store-->>Browser: Insert SVG and activate controls
```

## Dedicated editor save and conflict

```mermaid
stateDiagram-v2
  [*] --> Loading
  Loading --> Clean: Detail loaded
  Loading --> Error: Load failed
  Clean --> Dirty: User edits
  Dirty --> Validating: Debounce or explicit validate
  Validating --> Dirty: Valid
  Validating --> ValidationError: Invalid
  ValidationError --> Validating: Source changed
  Dirty --> Saving: Save
  Saving --> Clean: Saved and new version received
  Saving --> Conflict: HTTP 409
  Saving --> Error: Network or server error
  Conflict --> Dirty: Keep local and compare
  Conflict --> Clean: Reload server
  Conflict --> Dirty: Duplicate local as new
  Error --> Saving: Retry
```

## Visual editor safety boundary

```mermaid
flowchart LR
  Source[Canonical Mermaid Source] --> Analyze[Adapter Compatibility Analysis]
  Analyze -->|Unsupported| CodeOnly[Code Editor and Preview]
  Analyze -->|Read-only| VisualPreview[Visual Read-only Preview]
  Analyze -->|Editable| Parse[Parse to Neutral IR]
  Parse --> Canvas[React Flow Canvas]
  Canvas --> Serialize[Serialize to Mermaid]
  Serialize --> Loss{Loss or Unsupported Syntax?}
  Loss -->|Yes| Block[Block Apply and Show Loss Report]
  Loss -->|No| Validate[Mermaid Parse and Render]
  Validate -->|Invalid| Block
  Validate -->|Valid| Apply[Apply as One Code-editor Transaction]
  Apply --> Source
```

## PHP dependency direction

```mermaid
flowchart TD
  Presentation[Presentation: REST Block Admin Hooks] --> Application[Application: Commands Queries DTOs Policies]
  Application --> Domain[Domain: Diagram Values Repository Interfaces]
  Infrastructure[Infrastructure: WordPress Repository Cache Settings Assets] --> Domain
  Presentation --> Infrastructure

  classDef core stroke-width:2px;
  class Domain core;
```
