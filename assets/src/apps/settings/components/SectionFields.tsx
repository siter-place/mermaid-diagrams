/**
 * Settings section field components with WPDS help text and i18n.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import type { ReactElement } from '@wordpress/element';
import { SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

interface SectionFieldProps {
  values: Record<string, unknown>;
  onChange: (key: string, value: unknown) => void;
}

export function RenderingSectionFields({ values, onChange }: SectionFieldProps) {
  return (
    <>
      <SelectControl
        label={__('Default theme', 'mermaid-diagrams')}
        help={__('Default theme applied when rendering diagrams on the frontend and editor.', 'mermaid-diagrams')}
        value={String(values.defaultTheme ?? 'default')}
        options={[
          { label: __('Default', 'mermaid-diagrams'), value: 'default' },
          { label: __('Dark', 'mermaid-diagrams'), value: 'dark' },
          { label: __('Forest', 'mermaid-diagrams'), value: 'forest' },
          { label: __('Neutral', 'mermaid-diagrams'), value: 'neutral' },
        ]}
        onChange={(value) => onChange('defaultTheme', value)}
      />
      <ToggleControl
        label={__('Show default toolbar', 'mermaid-diagrams')}
        help={__('Show zoom, pan, and reset toolbar controls on rendered diagrams.', 'mermaid-diagrams')}
        checked={Boolean(values.defaultToolbar)}
        onChange={(value) => onChange('defaultToolbar', value)}
      />
      <TextControl
        label={__('Default width', 'mermaid-diagrams')}
        help={__('Default CSS width for diagram viewports (e.g. 100%, 800px).', 'mermaid-diagrams')}
        value={String(values.defaultWidth ?? '100%')}
        onChange={(value) => onChange('defaultWidth', value)}
      />
      <TextControl
        label={__('Default height (px)', 'mermaid-diagrams')}
        help={__('Default height in pixels for rendered diagram viewports.', 'mermaid-diagrams')}
        type="number"
        value={String(values.defaultHeight ?? 480)}
        onChange={(value) => onChange('defaultHeight', Number(value))}
      />
      <TextControl
        label={__('Maximum source length (bytes)', 'mermaid-diagrams')}
        help={__('Maximum source code size limit in bytes (default: 524,288 bytes / 500 KB).', 'mermaid-diagrams')}
        type="number"
        value={String(values.maxSourceLength ?? 524288)}
        onChange={(value) => onChange('maxSourceLength', Number(value))}
      />
      <ToggleControl
        label={__('Public source access', 'mermaid-diagrams')}
        help={__('Allow public frontend access to raw diagram source code downloads.', 'mermaid-diagrams')}
        checked={Boolean(values.publicSourceAccess)}
        onChange={(value) => onChange('publicSourceAccess', value)}
      />
    </>
  );
}

export function DownloadsSectionFields({ values, onChange }: SectionFieldProps) {
  return (
    <>
      <ToggleControl
        label={__('Allow source download', 'mermaid-diagrams')}
        help={__('Enable raw .mmd source code export button on diagram viewports.', 'mermaid-diagrams')}
        checked={Boolean(values.allowSource)}
        onChange={(value) => onChange('allowSource', value)}
      />
      <ToggleControl
        label={__('Allow SVG download', 'mermaid-diagrams')}
        help={__('Enable vector SVG diagram image export button on diagram viewports.', 'mermaid-diagrams')}
        checked={Boolean(values.allowSvg)}
        onChange={(value) => onChange('allowSvg', value)}
      />
      <ToggleControl
        label={__('Allow PNG download', 'mermaid-diagrams')}
        help={__('Enable raster PNG diagram image export option on diagram viewports.', 'mermaid-diagrams')}
        checked={Boolean(values.allowPng)}
        onChange={(value) => onChange('allowPng', value)}
      />
    </>
  );
}

export function EditorSectionFields({ values, onChange }: SectionFieldProps) {
  return (
    <>
      <TextControl
        label={__('Live validation debounce (ms)', 'mermaid-diagrams')}
        help={__('Delay in milliseconds before running syntax validation while typing in the editor.', 'mermaid-diagrams')}
        type="number"
        value={String(values.liveValidationDebounceMs ?? 300)}
        onChange={(value) => onChange('liveValidationDebounceMs', Number(value))}
      />
      <ToggleControl
        label={__('Line numbers', 'mermaid-diagrams')}
        help={__('Display line numbers in the Mermaid source code editor.', 'mermaid-diagrams')}
        checked={Boolean(values.lineNumbers)}
        onChange={(value) => onChange('lineNumbers', value)}
      />
      <ToggleControl
        label={__('Autocomplete', 'mermaid-diagrams')}
        help={__('Enable keyword and syntax completion suggestions in the code editor.', 'mermaid-diagrams')}
        checked={Boolean(values.autocomplete)}
        onChange={(value) => onChange('autocomplete', value)}
      />
    </>
  );
}

export function VisualEditorSectionFields({ values, onChange }: SectionFieldProps) {
  return (
    <>
      <ToggleControl
        label={__('Enable visual editor', 'mermaid-diagrams')}
        help={__('Enable interactive visual node-based editor mode for supported diagram types.', 'mermaid-diagrams')}
        checked={Boolean(values.enabled)}
        onChange={(value) => onChange('enabled', value)}
      />
      <ToggleControl
        label={__('Allow flowchart adapter', 'mermaid-diagrams')}
        help={__('Allow visual flowchart editing adapter.', 'mermaid-diagrams')}
        checked={Boolean(values.allowFlowchartAdapter)}
        onChange={(value) => onChange('allowFlowchartAdapter', value)}
      />
      <ToggleControl
        label={__('Experimental beta adapters', 'mermaid-diagrams')}
        help={__('Enable experimental visual editing adapters for sequence and class diagrams.', 'mermaid-diagrams')}
        checked={Boolean(values.experimentalBetaAdapters)}
        onChange={(value) => onChange('experimentalBetaAdapters', value)}
      />
    </>
  );
}

export function PermissionsSectionFields({ values, onChange }: SectionFieldProps) {
  return (
    <>
      <ToggleControl
        label={__('Require publish capability', 'mermaid-diagrams')}
        help={__('Require publish_mdm_diagrams capability to publish diagrams directly.', 'mermaid-diagrams')}
        checked={Boolean(values.requirePublishCap)}
        onChange={(value) => onChange('requirePublishCap', value)}
      />
      <ToggleControl
        label={__('Allow author library access', 'mermaid-diagrams')}
        help={__('Allow users with Author role to insert shared library diagrams into posts.', 'mermaid-diagrams')}
        checked={Boolean(values.allowAuthorLibrary)}
        onChange={(value) => onChange('allowAuthorLibrary', value)}
      />
    </>
  );
}

export function DataRetentionSectionFields({ values, onChange }: SectionFieldProps) {
  return (
    <SelectControl
      label={__('Uninstall policy', 'mermaid-diagrams')}
      help={__('Choose whether to preserve or remove plugin data when the plugin is deleted.', 'mermaid-diagrams')}
      value={String(values.uninstallPolicy ?? 'preserve')}
      options={[
        { label: __('Preserve all data', 'mermaid-diagrams'), value: 'preserve' },
        { label: __('Delete settings only', 'mermaid-diagrams'), value: 'settings_only' },
        { label: __('Complete purge', 'mermaid-diagrams'), value: 'complete_purge' },
      ]}
      onChange={(value) => onChange('uninstallPolicy', value)}
    />
  );
}

const SECTION_FIELDS: Record<string, (props: SectionFieldProps) => ReactElement> = {
  rendering: RenderingSectionFields,
  downloads: DownloadsSectionFields,
  editor: EditorSectionFields,
  visual_editor: VisualEditorSectionFields,
  permissions: PermissionsSectionFields,
  data_retention: DataRetentionSectionFields,
};

export function SectionFields({
  sectionId,
  values,
  onChange,
}: SectionFieldProps & { sectionId: string }) {
  const Component = SECTION_FIELDS[sectionId];

  if (!Component) {
    return <p>{__('Unknown settings section.', 'mermaid-diagrams')}</p>;
  }

  return <Component values={values} onChange={onChange} />;
}
