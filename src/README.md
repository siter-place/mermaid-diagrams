# Source implementation boundary

This directory contains the plugin kernel, domain model, and infrastructure implementations for Mermaid Diagrams:

- `Bootstrap/`: Core bootstrap class (`Plugin.php`), `Compatibility.php`, purpose-built DI `Container.php`, `ServiceProvider.php` interface, `ServiceProviderRegistry.php`, `Activation.php`, `Deactivation.php`.
- `Diagram/`:
  - `Domain/`: Value Objects (`DiagramId`, `DiagramSource`, `SourceHash`, `DiagramTitle`, `DiagramDescription`, `DiagramStatus`, `DiagramType`, `DiagramVersion`, `ValidationReceipt`, `RenderConfig`), Aggregate Root (`Diagram`), Port (`DiagramRepository`), Exceptions.
  - `Infrastructure/`: `DiagramPostType` (`mdm_diagram`), `DiagramTaxonomies` (`mdm_diagram_category`, `mdm_diagram_tag`), `DiagramMeta`, `DiagramCapabilities`, `WordPressDiagramRepository`.
  - `DiagramServiceProvider.php`: Registers CPT, taxonomies, meta, capabilities, and repository binding.
- `Upgrade/`: `UpgradeRunner.php`, `Migration/CreateUsageTables.php` (`mdm_usage`, `mdm_usage_dirty`), `UpgradeServiceProvider.php`.
- `Admin/`: `AdminMenu.php` (top-level page shell), `Cli/MdmCliCommand.php` (`wp mdm`), `AdminServiceProvider.php`.
