/**
 * Settings section field components.
 *
 * @package
 */

import type { ReactElement } from '@wordpress/element';
import {
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';

interface SectionFieldProps {
	values: Record< string, unknown >;
	onChange: ( key: string, value: unknown ) => void;
}

export function RenderingSectionFields( {
	values,
	onChange,
}: SectionFieldProps ) {
	return (
		<>
			<SelectControl
				label="Default theme"
				value={ String( values.defaultTheme ?? 'default' ) }
				options={ [
					{ label: 'Default', value: 'default' },
					{ label: 'Dark', value: 'dark' },
					{ label: 'Forest', value: 'forest' },
					{ label: 'Neutral', value: 'neutral' },
				] }
				onChange={ ( value ) => onChange( 'defaultTheme', value ) }
			/>
			<ToggleControl
				label="Show default toolbar"
				checked={ Boolean( values.defaultToolbar ) }
				onChange={ ( value ) => onChange( 'defaultToolbar', value ) }
			/>
			<TextControl
				label="Default width"
				value={ String( values.defaultWidth ?? '100%' ) }
				onChange={ ( value ) => onChange( 'defaultWidth', value ) }
			/>
			<TextControl
				label="Default height"
				type="number"
				value={ String( values.defaultHeight ?? 480 ) }
				onChange={ ( value ) =>
					onChange( 'defaultHeight', Number( value ) )
				}
			/>
			<TextControl
				label="Maximum source length"
				type="number"
				value={ String( values.maxSourceLength ?? 524288 ) }
				onChange={ ( value ) =>
					onChange( 'maxSourceLength', Number( value ) )
				}
			/>
			<ToggleControl
				label="Public source access"
				checked={ Boolean( values.publicSourceAccess ) }
				onChange={ ( value ) =>
					onChange( 'publicSourceAccess', value )
				}
			/>
		</>
	);
}

export function DownloadsSectionFields( {
	values,
	onChange,
}: SectionFieldProps ) {
	return (
		<>
			<ToggleControl
				label="Allow source download"
				checked={ Boolean( values.allowSource ) }
				onChange={ ( value ) => onChange( 'allowSource', value ) }
			/>
			<ToggleControl
				label="Allow SVG download"
				checked={ Boolean( values.allowSvg ) }
				onChange={ ( value ) => onChange( 'allowSvg', value ) }
			/>
			<ToggleControl
				label="Allow PNG download"
				checked={ Boolean( values.allowPng ) }
				onChange={ ( value ) => onChange( 'allowPng', value ) }
			/>
		</>
	);
}

export function EditorSectionFields( { values, onChange }: SectionFieldProps ) {
	return (
		<>
			<TextControl
				label="Live validation debounce (ms)"
				type="number"
				value={ String( values.liveValidationDebounceMs ?? 300 ) }
				onChange={ ( value ) =>
					onChange( 'liveValidationDebounceMs', Number( value ) )
				}
			/>
			<ToggleControl
				label="Line numbers"
				checked={ Boolean( values.lineNumbers ) }
				onChange={ ( value ) => onChange( 'lineNumbers', value ) }
			/>
			<ToggleControl
				label="Autocomplete"
				checked={ Boolean( values.autocomplete ) }
				onChange={ ( value ) => onChange( 'autocomplete', value ) }
			/>
		</>
	);
}

export function VisualEditorSectionFields( {
	values,
	onChange,
}: SectionFieldProps ) {
	return (
		<>
			<ToggleControl
				label="Enable visual editor"
				checked={ Boolean( values.enabled ) }
				onChange={ ( value ) => onChange( 'enabled', value ) }
			/>
			<ToggleControl
				label="Allow flowchart adapter"
				checked={ Boolean( values.allowFlowchartAdapter ) }
				onChange={ ( value ) =>
					onChange( 'allowFlowchartAdapter', value )
				}
			/>
			<ToggleControl
				label="Experimental beta adapters"
				checked={ Boolean( values.experimentalBetaAdapters ) }
				onChange={ ( value ) =>
					onChange( 'experimentalBetaAdapters', value )
				}
			/>
		</>
	);
}

export function PermissionsSectionFields( {
	values,
	onChange,
}: SectionFieldProps ) {
	return (
		<>
			<ToggleControl
				label="Require publish capability"
				checked={ Boolean( values.requirePublishCap ) }
				onChange={ ( value ) => onChange( 'requirePublishCap', value ) }
			/>
			<ToggleControl
				label="Allow author library access"
				checked={ Boolean( values.allowAuthorLibrary ) }
				onChange={ ( value ) =>
					onChange( 'allowAuthorLibrary', value )
				}
			/>
		</>
	);
}

export function DataRetentionSectionFields( {
	values,
	onChange,
}: SectionFieldProps ) {
	return (
		<SelectControl
			label="Uninstall policy"
			value={ String( values.uninstallPolicy ?? 'preserve' ) }
			options={ [
				{ label: 'Preserve all data', value: 'preserve' },
				{ label: 'Delete settings only', value: 'settings_only' },
				{ label: 'Complete purge', value: 'complete_purge' },
			] }
			onChange={ ( value ) => onChange( 'uninstallPolicy', value ) }
		/>
	);
}

const SECTION_FIELDS: Record<
	string,
	( props: SectionFieldProps ) => ReactElement
> = {
	rendering: RenderingSectionFields,
	downloads: DownloadsSectionFields,
	editor: EditorSectionFields,
	visual_editor: VisualEditorSectionFields,
	permissions: PermissionsSectionFields,
	data_retention: DataRetentionSectionFields,
};

export function SectionFields( {
	sectionId,
	values,
	onChange,
}: SectionFieldProps & { sectionId: string } ) {
	const Component = SECTION_FIELDS[ sectionId ];

	if ( ! Component ) {
		return <p>Unknown settings section.</p>;
	}

	return <Component values={ values } onChange={ onChange } />;
}
