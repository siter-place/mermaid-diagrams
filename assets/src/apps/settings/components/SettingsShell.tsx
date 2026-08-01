/**
 * Settings application shell.
 *
 * @package
 */

import { useMemo, useState } from '@wordpress/element';
import { MdmButton } from '../../../shared/components/MdmButton';
import { MdmErrorState } from '../../../shared/components/MdmErrorState';
import { MdmLoadingSkeleton } from '../../../shared/components/MdmLoadingSkeleton';
import { useSettingsSection } from '../../../shared/hooks/useSettingsSection';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';
import { RuntimeDiagnostics } from './RuntimeDiagnostics';
import { SectionFields } from './SectionFields';
import { SettingsSectionForm } from './SettingsSectionForm';

export function SettingsShell() {
	const bootstrap = useBootstrap();
	const [ activeSection, setActiveSection ] = useState( 'rendering' );
	const {
		settings,
		sectionValues,
		status,
		isDirty,
		isSaving,
		error,
		setField,
		saveSection,
		reload,
	} = useSettingsSection( activeSection );

	const sections = useMemo(
		() => settings?.schema.sections ?? [],
		[ settings ]
	);

	if ( ! bootstrap.capabilities.manageSettings ) {
		return (
			<MdmErrorState
				data-testid="mdm-settings-permission-denied"
				title={ bootstrap.i18n.permissionDenied }
				message={ bootstrap.i18n.permissionDenied }
			/>
		);
	}

	if ( status === 'loading' ) {
		return <MdmLoadingSkeleton data-testid="mdm-settings-loading" />;
	}

	if ( status === 'error' ) {
		return (
			<MdmErrorState
				data-testid="mdm-settings-error"
				title={ bootstrap.i18n.settingsSaveError }
				message={ error ?? bootstrap.i18n.settingsSaveError }
				onRetry={ reload }
				retryLabel={ bootstrap.i18n.retry }
			/>
		);
	}

	return (
		<div className="mdm-app-layout" data-testid="mdm-settings-shell">
			<div className="mdm-settings-layout">
				<nav
					className="mdm-settings-nav"
					aria-label="Settings sections"
				>
					{ sections.map( ( section ) => (
						<MdmButton
							key={ section.id }
							variant={
								activeSection === section.id
									? 'primary'
									: 'secondary'
							}
							onClick={ () => setActiveSection( section.id ) }
							data-testid={ `mdm-settings-nav-${ section.id }` }
						>
							{ section.title }
						</MdmButton>
					) ) }
				</nav>

				<SettingsSectionForm
					isDirty={ isDirty }
					isSaving={ isSaving }
					onSave={ () => {
						void saveSection();
					} }
				>
					<SectionFields
						sectionId={ activeSection }
						values={ sectionValues }
						onChange={ setField }
					/>
				</SettingsSectionForm>
			</div>

			{ settings ? (
				<RuntimeDiagnostics runtime={ settings.runtime } />
			) : null }
		</div>
	);
}
