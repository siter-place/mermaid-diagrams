/**
 * Settings section form wrapper.
 *
 * @package
 */

import type { ReactNode } from '@wordpress/element';
import { MdmButton } from '../../../shared/components/MdmButton';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';

interface SettingsSectionFormProps {
	isDirty: boolean;
	isSaving: boolean;
	onSave: () => void;
	children: ReactNode;
}

export function SettingsSectionForm( {
	isDirty,
	isSaving,
	onSave,
	children,
}: SettingsSectionFormProps ) {
	const { i18n } = useBootstrap();

	return (
		<div className="mdm-settings-panel">
			{ children }
			<p>
				<MdmButton
					variant="primary"
					onClick={ onSave }
					disabled={ ! isDirty || isSaving }
					isBusy={ isSaving }
					data-testid="mdm-settings-save"
				>
					{ i18n.saveSettings }
				</MdmButton>
			</p>
		</div>
	);
}
