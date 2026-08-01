/**
 * Quick-create diagram modal with validation.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import {
	Button,
	Modal,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import { check } from '@wordpress/icons';
import { DiagramViewport } from '../../../shared/components/DiagramViewport';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';
import type { QuickCreateForm } from '../../../shared/hooks/useQuickCreate';

interface QuickCreateModalProps {
	isOpen: boolean;
	isSaving: boolean;
	form: QuickCreateForm;
	validationError: string | null;
	onClose: () => void;
	onFieldChange: ( field: keyof QuickCreateForm, value: string ) => void;
	onSave: () => void;
}

export function QuickCreateModal( {
	isOpen,
	isSaving,
	form,
	validationError,
	onClose,
	onFieldChange,
	onSave,
}: QuickCreateModalProps ) {
	const { i18n } = useBootstrap();

	if ( ! isOpen ) {
		return null;
	}

	return (
		<Modal
			title={ i18n.quickCreateTitle || 'Create diagram' }
			onRequestClose={ onClose }
			className="mdm-quick-create-modal"
			isFullScreen
		>
			<div className="mdm-modal-container">
				<div className="mdm-modal-content">
					<div className="mdm-quick-create-layout">
						<div className="mdm-quick-create-layout__form">
							<TextControl
								label={ i18n.fieldTitle || 'Title' }
								help={ i18n.fieldTitleHelp || 'Provide a descriptive title for this diagram.' }
								value={ form.title }
								onChange={ ( value ) => onFieldChange( 'title', value ) }
							/>
							<TextareaControl
								label={ i18n.fieldSource || 'Mermaid source' }
								help={ i18n.fieldSourceHelp || 'Enter valid Mermaid syntax (e.g. flowchart, sequenceDiagram).' }
								value={ form.source }
								onChange={ ( value ) => onFieldChange( 'source', value ) }
								rows={ 16 }
							/>
							{ validationError ? (
								<p className="mdm-form-error" role="alert">
									{ validationError }
								</p>
							) : null }
						</div>
						<div className="mdm-quick-create-layout__preview">
							<h3 className="mdm-quick-create-layout__preview-title">
								{ i18n.previewTitle || 'Live Preview' }
							</h3>
							<DiagramViewport
								source={ form.source }
								title={ form.title }
								className="mdm-quick-create-modal__preview"
							/>
						</div>
					</div>
				</div>
				<div className="mdm-modal-footer">
					<Button variant="secondary" onClick={ onClose }>
						{ i18n.cancel || 'Cancel' }
					</Button>
					<Button
						variant="primary"
						icon={ check }
						isBusy={ isSaving }
						disabled={ isSaving }
						onClick={ onSave }
					>
						{ i18n.save || 'Save' }
					</Button>
				</div>
			</div>
		</Modal>
	);
}
