/**
 * Diagram preview side panel / modal.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { useEffect, useRef } from '@wordpress/element';
import { Button, Modal } from '@wordpress/components';
import { close, copy, edit, trash } from '@wordpress/icons';
import { DiagramViewport } from '../../../shared/components/DiagramViewport';
import { MdmBadge } from '../../../shared/components/MdmBadge';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';
import type {
	DiagramPreviewPayload,
	DiagramUsageResponse,
} from '../../../shared/api/types';

interface PreviewPanelProps {
	isOpen: boolean;
	status: 'idle' | 'loading' | 'ready' | 'error';
	payload: DiagramPreviewPayload | null;
	usage: DiagramUsageResponse | null;
	error: string | null;
	onClose: () => void;
	onDuplicate?: ( id: number ) => void;
	onTrash?: ( id: number ) => void;
}

export function PreviewPanel( {
	isOpen,
	status,
	payload,
	usage,
	error,
	onClose,
	onDuplicate,
	onTrash,
}: PreviewPanelProps ) {
	const { i18n, routes } = useBootstrap();
	const closeButtonRef = useRef< HTMLButtonElement | null >( null );

	useEffect( () => {
		if ( isOpen ) {
			closeButtonRef.current?.focus();
		}
	}, [ isOpen, payload?.id ] );

	if ( ! isOpen ) {
		return null;
	}

	const title = payload?.title ?? i18n.previewLoading ?? 'Loading preview…';

	return (
		<Modal
			title={ title }
			onRequestClose={ onClose }
			className="mdm-preview-panel"
			size="large"
		>
			<div className="mdm-modal-container" data-testid="mdm-preview-panel">
				<div className="mdm-modal-content">
					<div className="mdm-preview-panel__body">
						{ status === 'loading' ? (
							<div className="mdm-preview-panel__loading">
								<p>{ i18n.previewLoading || 'Loading preview\u2026' }</p>
							</div>
						) : null }
						{ status === 'error' ? (
							<div className="mdm-preview-panel__error" role="alert">
								<p>{ i18n.previewUnavailable || 'Preview is currently unavailable.' }</p>
							</div>
						) : null }
						{ payload ? (
							<>
								<div className="mdm-preview-panel__meta">
									<MdmBadge status={ payload.status } />
									<span className="mdm-preview-panel__type-label">{ payload.type }</span>
									{ usage ? (
										<span className="mdm-preview-panel__usage">
											{ ( i18n.usageSummary || 'Used in %1$s places' ).replace(
												'%1$s',
												String( usage.usageCount )
											) }
										</span>
									) : null }
								</div>
								<div className="mdm-preview-panel__viewport">
									{ payload.source ? (
										<DiagramViewport
											source={ payload.source }
											title={ payload.title }
											description={ payload.description }
											renderConfig={ payload.renderConfig }
										/>
									) : (
										<p className="mdm-preview-panel__no-source">
											{ i18n.invalidSource || 'Mermaid source is invalid.' }
										</p>
									) }
								</div>
							</>
						) : null }
					</div>
				</div>
				<div className="mdm-modal-footer">
					{ payload?.can.edit ? (
						<Button
							variant="secondary"
							icon={ edit }
							href={ `${ routes.editor }&diagram=${ payload.id }` }
							size="compact"
						>
							{ i18n.editDiagram || 'Edit' }
						</Button>
					) : null }
					{ onDuplicate && payload ? (
						<Button
							variant="secondary"
							icon={ copy }
							onClick={ () => onDuplicate( payload.id ) }
							size="compact"
						>
							{ i18n.duplicateDiagram || 'Duplicate' }
						</Button>
					) : null }
					{ onTrash && payload?.can.delete ? (
						<Button
							variant="secondary"
							isDestructive
							icon={ trash }
							onClick={ () => onTrash( payload.id ) }
							size="compact"
						>
							{ i18n.trashDiagram || 'Trash' }
						</Button>
					) : null }
					<Button
						ref={ closeButtonRef }
						variant="primary"
						icon={ close }
						onClick={ onClose }
						size="compact"
					>
						{ i18n.previewClose || 'Close' }
					</Button>
				</div>
			</div>
		</Modal>
	);
}
