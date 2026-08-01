/**
 * Diagram library shell layout.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { useCallback, useState } from '@wordpress/element';
import { Modal } from '@wordpress/components';
import { plus } from '@wordpress/icons';
import { duplicateDiagram, deleteDiagram } from '../../../shared/api';
import { MdmButton } from '../../../shared/components/MdmButton';
import { MdmEmptyState } from '../../../shared/components/MdmEmptyState';
import { MdmErrorState } from '../../../shared/components/MdmErrorState';
import { MdmLoadingSkeleton } from '../../../shared/components/MdmLoadingSkeleton';
import { useBulkActions } from '../../../shared/hooks/useBulkActions';
import { useDiagramList } from '../../../shared/hooks/useDiagramList';
import { useDiagramPreview } from '../../../shared/hooks/useDiagramPreview';
import { useDiagramSelection } from '../../../shared/hooks/useDiagramSelection';
import { useQuickCreate } from '../../../shared/hooks/useQuickCreate';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';
import { useNotices } from '../../../shared/providers/NoticesProvider';
import type { DiagramSummary } from '../../../shared/api/types';
import type { BulkOperationType } from '../../../shared/api/types';
import { BulkActionBar } from './BulkActionBar';
import { DiagramGrid } from './DiagramGrid';
import { DiagramTable } from './DiagramTable';
import { FilterBar, type TableDensity, type VisibleProperties } from './FilterBar';
import { PaginationBar } from './PaginationBar';
import { PreviewPanel } from './PreviewPanel';
import { QuickCreateModal } from './QuickCreateModal';

export function LibraryShell() {
	const { i18n, capabilities } = useBootstrap();
	const { createNotice } = useNotices();
	const {
		status,
		data,
		error,
		query,
		setPage,
		setFilters,
		resetFilters,
		reload,
	} = useDiagramList();
	const selection = useDiagramSelection();
	const bulk = useBulkActions();
	const preview = useDiagramPreview();
	const quickCreate = useQuickCreate();

	const [ viewMode, setViewMode ] = useState< 'table' | 'grid' >( 'table' );
	const [ density, setDensity ] = useState< TableDensity >( 'balanced' );
	const [ visibleProperties, setVisibleProperties ] = useState< VisibleProperties >( {
		title: true,
		categories: true,
		tags: true,
		status: true,
		author: true,
		modified: true,
		usage: true,
		actions: true,
	} );

	const [ trashTarget, setTrashTarget ] = useState< DiagramSummary | null >( null );

	const handlePropertyToggle = useCallback( ( key: keyof VisibleProperties ) => {
		setVisibleProperties( ( prev ) => ( {
			...prev,
			[ key ]: ! prev[ key ],
		} ) );
	}, [] );

	const handleBulkApply = useCallback(
		async (
			operation: BulkOperationType,
			payload?: {
				category_ids?: number[];
				tag_ids?: number[];
				status?: string;
			}
		) => {
			const ids = Array.from( selection.selectedIds );
			const result = await bulk.runBulk( ids, operation, payload );

			createNotice(
				result.failed > 0 ? 'info' : 'success',
				( i18n.bulkSummary || '%1$s succeeded, %2$s failed.' )
					.replace( '%1$s', String( result.succeeded ) )
					.replace( '%2$s', String( result.failed ) )
			);

			if ( result.failedIds.length ) {
				selection.setSelectedIds( result.failedIds );
			} else {
				selection.clearSelection();
			}

			reload();
		},
		[ bulk, createNotice, i18n.bulkSummary, reload, selection ]
	);

	const handleDuplicate = useCallback(
		async ( item: DiagramSummary ) => {
			try {
				await duplicateDiagram( item.id );
				createNotice(
					'success',
					i18n.duplicatedNotice || 'Diagram duplicated.'
				);
				reload();
			} catch ( duplicateError ) {
				createNotice(
					'error',
					duplicateError instanceof Error
						? duplicateError.message
						: 'Unable to duplicate diagram.'
				);
			}
		},
		[ createNotice, i18n.duplicatedNotice, reload ]
	);

	const handleTrashConfirm = useCallback( async () => {
		if ( ! trashTarget ) {
			return;
		}

		try {
			await deleteDiagram( trashTarget.id );
			createNotice(
				'success',
				i18n.trashedNotice || 'Diagram moved to trash.'
			);
			setTrashTarget( null );
			preview.closePreview();
			reload();
		} catch ( trashError ) {
			createNotice(
				'error',
				trashError instanceof Error
					? trashError.message
					: 'Unable to trash diagram.'
			);
		}
	}, [ createNotice, i18n.trashedNotice, preview, reload, trashTarget ] );

	const handleQuickCreateSave = useCallback( async () => {
		const created = await quickCreate.save();
		if ( created ) {
			createNotice( 'success', i18n.savedNotice || 'Diagram saved.' );
			reload();
		}
	}, [ createNotice, i18n.savedNotice, quickCreate, reload ] );

	return (
		<div className="mdm-app-layout" data-testid="mdm-library-shell">
			<div className="mdm-app-header">
				<p className="mdm-app-subtitle">A list of all diagrams from all sources.</p>
				{ capabilities.createDiagrams !== false ? (
					<MdmButton variant="primary" icon={ plus } onClick={ quickCreate.open }>
						{ i18n.addDiagram || 'Add diagram' }
					</MdmButton>
				) : null }
			</div>

			<FilterBar
				query={ query }
				viewMode={ viewMode }
				density={ density }
				visibleProperties={ visibleProperties }
				onChange={ setFilters }
				onReset={ resetFilters }
				onViewModeChange={ setViewMode }
				onDensityChange={ setDensity }
				onPropertyToggle={ handlePropertyToggle }
			/>

			<BulkActionBar
				selectedCount={ selection.selectedCount }
				isRunning={ bulk.isRunning }
				onApply={ handleBulkApply }
			/>

			{ status === 'loading' ? (
				<MdmLoadingSkeleton data-testid="mdm-library-loading" />
			) : null }

			{ status === 'empty' ? (
				<MdmEmptyState
					data-testid="mdm-library-empty"
					title={ i18n.emptyTitle }
					description={ i18n.emptyDescription }
				/>
			) : null }

			{ status === 'no-match' ? (
				<MdmEmptyState
					data-testid="mdm-library-no-match"
					title={ i18n.noMatchTitle || 'No diagrams match your filters' }
					description={
						i18n.noMatchDescription ||
						'Try adjusting your search or filters.'
					}
				/>
			) : null }

			{ status === 'error' ? (
				<MdmErrorState
					data-testid="mdm-library-error"
					title={ i18n.errorTitle }
					message={ error ?? i18n.errorTitle }
					onRetry={ reload }
					retryLabel={ i18n.retry }
				/>
			) : null }

			{ status === 'ready' && data ? (
				<>
					{ viewMode === 'table' ? (
						<DiagramTable
							items={ data.items as DiagramSummary[] }
							selectedIds={ selection.selectedIds }
							density={ density }
							visibleProperties={ visibleProperties }
							onToggleRow={ selection.toggleRow }
							onTogglePage={ selection.togglePage }
							onPreview={ ( item ) => preview.openPreview( item.id ) }
							onDuplicate={ ( item ) => void handleDuplicate( item ) }
							onTrash={ ( item ) => setTrashTarget( item ) }
						/>
					) : (
						<DiagramGrid
							items={ data.items as DiagramSummary[] }
							selectedIds={ selection.selectedIds }
							onToggleRow={ selection.toggleRow }
							onPreview={ ( item ) => preview.openPreview( item.id ) }
							onDuplicate={ ( item ) => void handleDuplicate( item ) }
							onTrash={ ( item ) => setTrashTarget( item ) }
						/>
					) }
					<PaginationBar
						page={ query.page }
						totalPages={ Math.max( 1, data.pagination.totalPages ) }
						totalItems={ data.pagination.totalItems }
						perPage={ query.perPage }
						onPageChange={ setPage }
					/>
				</>
			) : null }

			<PreviewPanel
				isOpen={ preview.isOpen }
				status={ preview.status }
				payload={ preview.payload }
				usage={ preview.usage }
				error={ preview.error }
				onClose={ preview.closePreview }
				onDuplicate={ ( id ) => void handleDuplicate( { id } as DiagramSummary ) }
				onTrash={ ( id ) =>
					setTrashTarget( { id } as DiagramSummary )
				}
			/>

			<QuickCreateModal
				isOpen={ quickCreate.isOpen }
				isSaving={ quickCreate.isSaving }
				form={ quickCreate.form }
				validationError={ quickCreate.validationError }
				onClose={ quickCreate.close }
				onFieldChange={ quickCreate.setField }
				onSave={ () => void handleQuickCreateSave() }
			/>

			{ trashTarget ? (
				<Modal
					title={ i18n.trashDiagram || 'Move to trash' }
					onRequestClose={ () => setTrashTarget( null ) }
				>
					<div className="mdm-modal-container">
						<div className="mdm-modal-content">
							<p>{ `Move "${ trashTarget.title || preview.payload?.title || 'diagram' }" to trash?` }</p>
						</div>
						<div className="mdm-modal-footer">
							<MdmButton
								variant="secondary"
								onClick={ () => setTrashTarget( null ) }
							>
								{ i18n.cancel || 'Cancel' }
							</MdmButton>
							<MdmButton
								variant="primary"
								isDestructive
								onClick={ () => void handleTrashConfirm() }
							>
								{ i18n.confirmTrash || 'Confirm' }
							</MdmButton>
						</div>
					</div>
				</Modal>
			) : null }
		</div>
	);
}
