/**
 * Diagram library data view powered by @wordpress/dataviews.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { useCallback, useEffect, useMemo, useRef, useState } from '@wordpress/element';
import { DataViews } from '@wordpress/dataviews';
import {
	Button,
	RadioControl,
	SelectControl,
	CheckboxControl,
} from '@wordpress/components';
import { copy, edit, info, trash, backup, category, tag, published } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { searchDiagrams, duplicateDiagram, bulkOperation } from '../../../shared/api';
import { MdmBadge } from '../../../shared/components/MdmBadge';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';
import { useNotices } from '../../../shared/providers/NoticesProvider';
import { useDiagramPreview } from '../../../shared/hooks/useDiagramPreview';
import { useQuickCreate } from '../../../shared/hooks/useQuickCreate';
import { useTaxonomyTerms } from '../../../shared/hooks/useTaxonomyTerms';
import { PreviewPanel } from './PreviewPanel';
import { QuickCreateModal } from './QuickCreateModal';
import type { DiagramSummary, DiagramSearchResponse, BulkOperationType } from '../../../shared/api/types';
import type { View, Action } from '@wordpress/dataviews';

function formatDate( value: string ): string {
	if ( ! value || value.startsWith( '-' ) || value.startsWith( '0000' ) ) {
		return '\u2014';
	}
	const date = new Date( value );
	if ( Number.isNaN( date.getTime() ) || date.getFullYear() < 2000 ) {
		return '\u2014';
	}
	return date.toLocaleDateString( undefined, {
		year: 'numeric',
		month: 'short',
		day: 'numeric',
	} );
}

function formatTerms( terms: Array< { name: string } > ): string {
	if ( ! terms || ! terms.length ) {
		return '\u2014';
	}
	return terms.map( ( t ) => t.name ).join( ', ' );
}

interface ViewToQueryParams {
	search?: string;
	category?: number[];
	tag?: number[];
	type?: string[];
	status?: string[];
	author?: number[];
	page: number;
	per_page: number;
	orderby: 'modified' | 'title';
	order: 'ASC' | 'DESC';
	view: 'summary';
}

function viewToRestQuery( view: View ): ViewToQueryParams {
	const sortField = view.sort?.field ?? 'modified';
	const orderby: 'modified' | 'title' = sortField === 'title' ? 'title' : 'modified';

	const params: ViewToQueryParams = {
		page: view.page ?? 1,
		per_page: view.perPage ?? 20,
		orderby,
		order: ( view.sort?.direction ?? 'desc' ).toUpperCase() as 'ASC' | 'DESC',
		view: 'summary',
	};

	if ( view.search ) {
		params.search = view.search;
	}

	if ( view.filters ) {
		for ( const filter of view.filters ) {
			const val = filter.value;
			if ( ! val || ( Array.isArray( val ) && val.length === 0 ) ) {
				continue;
			}
			switch ( filter.field ) {
				case 'status':
					params.status = Array.isArray( val ) ? val : [ val ];
					break;
				case 'type':
					params.type = Array.isArray( val ) ? val : [ val ];
					break;
				case 'categories':
					params.category = Array.isArray( val )
						? val.map( Number )
						: [ Number( val ) ];
					break;
				case 'tags':
					params.tag = Array.isArray( val )
						? val.map( Number )
						: [ Number( val ) ];
					break;
				case 'author':
					params.author = Array.isArray( val )
						? val.map( Number )
						: [ Number( val ) ];
					break;
			}
		}
	}

	return params;
}

const DEFAULT_VIEW: View = {
	type: 'table',
	search: '',
	filters: [],
	page: 1,
	perPage: 20,
	sort: { field: 'modified', direction: 'desc' },
	titleField: 'title',
	fields: [ 'type', 'categories', 'tags', 'status', 'author', 'modified', 'usage' ],
	layout: {
		styles: {
			type: { width: '100px' },
			status: { width: '100px' },
			author: { width: '120px' },
			modified: { width: '140px' },
			usage: { width: '80px', align: 'end' },
		},
	},
};

export function DiagramDataView() {
	const { i18n, routes, capabilities, diagramTypes = [], defaults } = useBootstrap();
	const { createNotice } = useNotices();
	const preview = useDiagramPreview();
	const quickCreate = useQuickCreate();

	const { terms: categoryTerms } = useTaxonomyTerms( { taxonomy: 'mdm-diagram-categories' } );
	const { terms: tagTerms } = useTaxonomyTerms( { taxonomy: 'mdm-diagram-tags' } );

	const [ view, setView ] = useState< View >( () => ( {
		...DEFAULT_VIEW,
		perPage: defaults.perPage,
		sort: {
			field: defaults.orderby,
			direction: defaults.order.toLowerCase() as 'asc' | 'desc',
		},
	} ) );

	const [ data, setData ] = useState< DiagramSummary[] >( [] );
	const [ isLoading, setIsLoading ] = useState( true );
	const [ paginationInfo, setPaginationInfo ] = useState( { totalItems: 0, totalPages: 0 } );
	const [ reloadToken, setReloadToken ] = useState( 0 );
	const abortRef = useRef< AbortController | null >( null );

	const reload = useCallback( () => {
		setReloadToken( ( t ) => t + 1 );
	}, [] );

	useEffect( () => {
		abortRef.current?.abort();
		const controller = new AbortController();
		abortRef.current = controller;

		setIsLoading( true );

		const queryParams = viewToRestQuery( view );

		searchDiagrams( queryParams, controller.signal )
			.then( ( response: DiagramSearchResponse ) => {
				if ( controller.signal.aborted ) {
					return;
				}
				setData( response.items as DiagramSummary[] );
				setPaginationInfo( {
					totalItems: response.pagination.totalItems,
					totalPages: response.pagination.totalPages,
				} );
				setIsLoading( false );
			} )
			.catch( ( err: unknown ) => {
				if ( controller.signal.aborted ) {
					return;
				}
				setIsLoading( false );
				const msg = err instanceof Error ? err.message : 'Failed to load diagrams.';
				createNotice( 'error', msg );
			} );

		return () => controller.abort();
	}, [ view, reloadToken, createNotice ] );

	const statusElements = useMemo(
		() => [
			{ value: 'publish', label: __( 'Published', 'mermaid-diagrams' ) },
			{ value: 'draft', label: __( 'Draft', 'mermaid-diagrams' ) },
			{ value: 'pending', label: __( 'Pending', 'mermaid-diagrams' ) },
			{ value: 'private', label: __( 'Private', 'mermaid-diagrams' ) },
			{ value: 'trash', label: __( 'Trash', 'mermaid-diagrams' ) },
		],
		[]
	);

	const typeElements = useMemo(
		() => diagramTypes.map( ( dt ) => ( { value: dt.value, label: dt.label } ) ),
		[ diagramTypes ]
	);

	const categoryElements = useMemo(
		() => categoryTerms.map( ( t ) => ( { value: String( t.id ), label: t.name } ) ),
		[ categoryTerms ]
	);

	const tagElements = useMemo(
		() => tagTerms.map( ( t ) => ( { value: String( t.id ), label: t.name } ) ),
		[ tagTerms ]
	);

	const fields = useMemo(
		() => [
			{
				id: 'title',
				label: __( 'Title', 'mermaid-diagrams' ),
				enableHiding: false,
				enableGlobalSearch: true,
				render: ( { item }: { item: DiagramSummary } ) => (
					<strong>{ item.title }</strong>
				),
			},
			{
				id: 'type',
				label: __( 'Type', 'mermaid-diagrams' ),
				getValue: ( { item }: { item: DiagramSummary } ) => item.type,
				elements: typeElements,
				filterBy: {
					operators: [ 'isAny' as const ],
				},
				enableSorting: false,
			},
			{
				id: 'categories',
				label: __( 'Categories', 'mermaid-diagrams' ),
				getValue: ( { item }: { item: DiagramSummary } ) =>
					item.categories?.map( ( c ) => String( c.id ) ) ?? [],
				render: ( { item }: { item: DiagramSummary } ) => (
					<span>{ formatTerms( item.categories ) }</span>
				),
				elements: categoryElements,
				filterBy: {
					operators: [ 'isAny' as const ],
				},
				enableSorting: false,
			},
			{
				id: 'tags',
				label: __( 'Tags', 'mermaid-diagrams' ),
				getValue: ( { item }: { item: DiagramSummary } ) =>
					item.tags?.map( ( t ) => String( t.id ) ) ?? [],
				render: ( { item }: { item: DiagramSummary } ) => (
					<span>{ formatTerms( item.tags ) }</span>
				),
				elements: tagElements,
				filterBy: {
					operators: [ 'isAny' as const ],
				},
				enableSorting: false,
			},
			{
				id: 'status',
				label: __( 'Status', 'mermaid-diagrams' ),
				getValue: ( { item }: { item: DiagramSummary } ) => item.status,
				render: ( { item }: { item: DiagramSummary } ) => (
					<MdmBadge status={ item.status } />
				),
				elements: statusElements,
				filterBy: {
					operators: [ 'isAny' as const ],
				},
				enableSorting: false,
			},
			{
				id: 'author',
				label: __( 'Author', 'mermaid-diagrams' ),
				getValue: ( { item }: { item: DiagramSummary } ) => item.author?.name ?? '',
				render: ( { item }: { item: DiagramSummary } ) => (
					<span>{ item.author?.name ?? '\u2014' }</span>
				),
				enableSorting: false,
			},
			{
				id: 'modified',
				label: __( 'Modified', 'mermaid-diagrams' ),
				getValue: ( { item }: { item: DiagramSummary } ) => item.modifiedGmt,
				render: ( { item }: { item: DiagramSummary } ) => (
					<span>{ formatDate( item.modifiedGmt ) }</span>
				),
				enableSorting: true,
			},
			{
				id: 'usage',
				label: __( 'Usage', 'mermaid-diagrams' ),
				getValue: ( { item }: { item: DiagramSummary } ) => item.usageCount ?? 0,
				render: ( { item }: { item: DiagramSummary } ) => (
					<span>{ item.usageCount ?? 0 }</span>
				),
				enableSorting: false,
			},
		],
		[ typeElements, categoryElements, tagElements, statusElements ]
	);

	const actions: Action< DiagramSummary >[] = useMemo(
		() => [
			{
				id: 'edit',
				label: i18n.editDiagram || __( 'Edit', 'mermaid-diagrams' ),
				isPrimary: true,
				icon: edit,
				isEligible: ( item: DiagramSummary ) => item.can.edit,
				callback: ( items: DiagramSummary[] ) => {
					if ( items[ 0 ] ) {
						window.location.href = `${ routes.editor }&diagram=${ items[ 0 ].id }`;
					}
				},
			},
			{
				id: 'preview',
				label: i18n.preview || __( 'Preview', 'mermaid-diagrams' ),
				isPrimary: true,
				icon: info,
				callback: ( items: DiagramSummary[] ) => {
					if ( items[ 0 ] ) {
						preview.openPreview( items[ 0 ].id );
					}
				},
			},
			{
				id: 'duplicate',
				label: i18n.duplicateDiagram || __( 'Duplicate', 'mermaid-diagrams' ),
				isPrimary: true,
				icon: copy,
				callback: async ( items: DiagramSummary[] ) => {
					for ( const item of items ) {
						try {
							await duplicateDiagram( item.id );
							createNotice( 'success', i18n.duplicatedNotice || 'Diagram duplicated.' );
						} catch ( err ) {
							createNotice(
								'error',
								err instanceof Error ? err.message : 'Unable to duplicate diagram.'
							);
						}
					}
					reload();
				},
			},
			{
				id: 'manage-categories',
				label: __( 'Manage categories', 'mermaid-diagrams' ),
				icon: category,
				supportsBulk: true,
				isEligible: ( item: DiagramSummary ) => item.can.edit,
				RenderModal: ( { items, closeModal, onActionPerformed } ) => {
					const [ mode, setMode ] = useState< 'add' | 'remove' | 'replace' >( 'add' );
					const [ selectedIds, setSelectedIds ] = useState< number[] >( [] );
					const { terms } = useTaxonomyTerms( { taxonomy: 'mdm-diagram-categories' } );

					const operationMap: Record< string, BulkOperationType > = {
						add: 'add_categories',
						remove: 'remove_categories',
						replace: 'replace_categories',
					};

					return (
						<div className="mdm-modal-container">
							<div className="mdm-modal-content">
								<p>{ `Apply to ${ items.length } diagram(s):` }</p>
								<RadioControl
									label={ __( 'Operation', 'mermaid-diagrams' ) }
									selected={ mode }
									options={ [
										{ label: __( 'Add categories', 'mermaid-diagrams' ), value: 'add' },
										{ label: __( 'Remove categories', 'mermaid-diagrams' ), value: 'remove' },
										{ label: __( 'Replace all categories', 'mermaid-diagrams' ), value: 'replace' },
									] }
									onChange={ ( val ) => setMode( val as 'add' | 'remove' | 'replace' ) }
								/>
								<div className="mdm-term-picker">
									{ terms.map( ( term ) => (
										<CheckboxControl
											key={ term.id }
											label={ term.name }
											checked={ selectedIds.includes( term.id ) }
											onChange={ ( checked ) => {
												setSelectedIds( ( prev ) =>
													checked
														? [ ...prev, term.id ]
														: prev.filter( ( id ) => id !== term.id )
												);
											} }
										/>
									) ) }
								</div>
							</div>
							<div className="mdm-modal-footer">
								<Button variant="secondary" onClick={ closeModal }>
									{ __( 'Cancel', 'mermaid-diagrams' ) }
								</Button>
								<Button
									variant="primary"
									disabled={ selectedIds.length === 0 }
									onClick={ async () => {
										try {
											const result = await bulkOperation( {
												ids: items.map( ( item ) => item.id ),
												operation: operationMap[ mode ],
												payload: { category_ids: selectedIds },
											} );
											createNotice(
												'success',
												`Categories updated: ${ result.summary.succeeded } of ${ result.summary.requested } succeeded.`
											);
										} catch ( err ) {
											createNotice(
												'error',
												err instanceof Error ? err.message : 'Bulk operation failed.'
											);
										}
										onActionPerformed?.( items );
										closeModal?.();
										reload();
									} }
								>
									{ __( 'Apply', 'mermaid-diagrams' ) }
								</Button>
							</div>
						</div>
					);
				},
			},
			{
				id: 'manage-tags',
				label: __( 'Manage tags', 'mermaid-diagrams' ),
				icon: tag,
				supportsBulk: true,
				isEligible: ( item: DiagramSummary ) => item.can.edit,
				RenderModal: ( { items, closeModal, onActionPerformed } ) => {
					const [ mode, setMode ] = useState< 'add' | 'remove' >( 'add' );
					const [ selectedIds, setSelectedIds ] = useState< number[] >( [] );
					const { terms } = useTaxonomyTerms( { taxonomy: 'mdm-diagram-tags' } );

					const operationMap: Record< string, BulkOperationType > = {
						add: 'add_tags',
						remove: 'remove_tags',
					};

					return (
						<div className="mdm-modal-container">
							<div className="mdm-modal-content">
								<p>{ `Apply to ${ items.length } diagram(s):` }</p>
								<RadioControl
									label={ __( 'Operation', 'mermaid-diagrams' ) }
									selected={ mode }
									options={ [
										{ label: __( 'Add tags', 'mermaid-diagrams' ), value: 'add' },
										{ label: __( 'Remove tags', 'mermaid-diagrams' ), value: 'remove' },
									] }
									onChange={ ( val ) => setMode( val as 'add' | 'remove' ) }
								/>
								<div className="mdm-term-picker">
									{ terms.map( ( term ) => (
										<CheckboxControl
											key={ term.id }
											label={ term.name }
											checked={ selectedIds.includes( term.id ) }
											onChange={ ( checked ) => {
												setSelectedIds( ( prev ) =>
													checked
														? [ ...prev, term.id ]
														: prev.filter( ( id ) => id !== term.id )
												);
											} }
										/>
									) ) }
								</div>
							</div>
							<div className="mdm-modal-footer">
								<Button variant="secondary" onClick={ closeModal }>
									{ __( 'Cancel', 'mermaid-diagrams' ) }
								</Button>
								<Button
									variant="primary"
									disabled={ selectedIds.length === 0 }
									onClick={ async () => {
										try {
											const result = await bulkOperation( {
												ids: items.map( ( item ) => item.id ),
												operation: operationMap[ mode ],
												payload: { tag_ids: selectedIds },
											} );
											createNotice(
												'success',
												`Tags updated: ${ result.summary.succeeded } of ${ result.summary.requested } succeeded.`
											);
										} catch ( err ) {
											createNotice(
												'error',
												err instanceof Error ? err.message : 'Bulk operation failed.'
											);
										}
										onActionPerformed?.( items );
										closeModal?.();
										reload();
									} }
								>
									{ __( 'Apply', 'mermaid-diagrams' ) }
								</Button>
							</div>
						</div>
					);
				},
			},
			{
				id: 'change-status',
				label: __( 'Change status', 'mermaid-diagrams' ),
				icon: published,
				supportsBulk: true,
				isEligible: ( item: DiagramSummary ) => item.can.edit && item.status !== 'trash',
				RenderModal: ( { items, closeModal, onActionPerformed } ) => {
					const [ newStatus, setNewStatus ] = useState( 'publish' );

					return (
						<div className="mdm-modal-container">
							<div className="mdm-modal-content">
								<p>{ `Change status for ${ items.length } diagram(s):` }</p>
								<SelectControl
									label={ __( 'New status', 'mermaid-diagrams' ) }
									value={ newStatus as 'publish' | 'draft' | 'pending' | 'private' }
									options={ [
										{ label: __( 'Published', 'mermaid-diagrams' ), value: 'publish' },
										{ label: __( 'Draft', 'mermaid-diagrams' ), value: 'draft' },
										{ label: __( 'Pending', 'mermaid-diagrams' ), value: 'pending' },
										{ label: __( 'Private', 'mermaid-diagrams' ), value: 'private' },
									] }
									onChange={ ( val: string ) => setNewStatus( val ) }
								/>
							</div>
							<div className="mdm-modal-footer">
								<Button variant="secondary" onClick={ closeModal }>
									{ __( 'Cancel', 'mermaid-diagrams' ) }
								</Button>
								<Button
									variant="primary"
									onClick={ async () => {
										try {
											const result = await bulkOperation( {
												ids: items.map( ( item ) => item.id ),
												operation: 'set_status',
												payload: { status: newStatus },
											} );
											createNotice(
												'success',
												`Status changed: ${ result.summary.succeeded } of ${ result.summary.requested } succeeded.`
											);
										} catch ( err ) {
											createNotice(
												'error',
												err instanceof Error ? err.message : 'Bulk operation failed.'
											);
										}
										onActionPerformed?.( items );
										closeModal?.();
										reload();
									} }
								>
									{ __( 'Apply', 'mermaid-diagrams' ) }
								</Button>
							</div>
						</div>
					);
				},
			},
			{
				id: 'trash',
				label: i18n.trashDiagram || __( 'Move to trash', 'mermaid-diagrams' ),
				icon: trash,
				isEligible: ( item: DiagramSummary ) => item.can.delete && item.status !== 'trash',
				supportsBulk: true,
				RenderModal: ( { items, closeModal, onActionPerformed } ) => {
					const count = items.length;
					const title = count === 1 ? items[ 0 ].title : `${ count } diagrams`;
					return (
						<div className="mdm-modal-container">
							<div className="mdm-modal-content">
								<p>{ `Move "${ title }" to trash?` }</p>
							</div>
							<div className="mdm-modal-footer">
								<Button variant="secondary" onClick={ closeModal }>
									{ __( 'Cancel', 'mermaid-diagrams' ) }
								</Button>
								<Button
									variant="primary"
									isDestructive
									onClick={ async () => {
										try {
											await bulkOperation( {
												ids: items.map( ( item ) => item.id ),
												operation: 'trash',
											} );
											createNotice(
												'success',
												i18n.trashedNotice || 'Diagram moved to trash.'
											);
										} catch ( err ) {
											createNotice(
												'error',
												err instanceof Error ? err.message : 'Failed to trash.'
											);
										}
										onActionPerformed?.( items );
										closeModal?.();
										reload();
									} }
								>
									{ __( 'Confirm', 'mermaid-diagrams' ) }
								</Button>
							</div>
						</div>
					);
				},
			},
			{
				id: 'restore',
				label: __( 'Restore', 'mermaid-diagrams' ),
				icon: backup,
				isEligible: ( item: DiagramSummary ) => item.status === 'trash',
				supportsBulk: true,
				callback: async ( items: DiagramSummary[] ) => {
					try {
						const result = await bulkOperation( {
							ids: items.map( ( item ) => item.id ),
							operation: 'restore',
						} );
						createNotice(
							'success',
							`Restored: ${ result.summary.succeeded } of ${ result.summary.requested } succeeded.`
						);
					} catch ( err ) {
						createNotice(
							'error',
							err instanceof Error ? err.message : 'Failed to restore.'
						);
					}
					reload();
				},
			},
		],
		[ i18n, routes, preview, createNotice, reload ]
	);

	const handleQuickCreateSave = useCallback( async () => {
		const created = await quickCreate.save();
		if ( created ) {
			createNotice( 'success', i18n.savedNotice || 'Diagram saved.' );
			reload();
		}
	}, [ createNotice, i18n.savedNotice, quickCreate, reload ] );

	const defaultLayouts = useMemo(
		() => ( {
			table: {
				layout: {
					styles: {
						type: { width: '100px' },
						status: { width: '100px' },
						author: { width: '120px' },
						modified: { width: '140px' },
						usage: { width: '80px', align: 'end' as const },
					},
				},
			},
			grid: {
				layout: {
					badgeFields: [ 'status', 'type' ],
				},
			},
			list: {},
		} ),
		[]
	);

	const addDiagramButton = useMemo(
		() =>
			capabilities.createDiagrams !== false ? (
				<Button variant="primary" onClick={ quickCreate.open }>
					{ i18n.addDiagram || __( 'Add diagram', 'mermaid-diagrams' ) }
				</Button>
			) : undefined,
		[ capabilities.createDiagrams, i18n.addDiagram, quickCreate.open ]
	);

	return (
		<div className="mdm-app-layout" data-testid="mdm-library-shell">
			<header className="mdm-page-header">
				<div className="mdm-page-header__text">
					<h1 className="mdm-page-header__title">
						{ __( 'Diagrams', 'mermaid-diagrams' ) }
					</h1>
					<p className="mdm-page-header__description">
						{ __( 'A list of all Diagrams on the system.', 'mermaid-diagrams' ) }
					</p>
				</div>
				{ addDiagramButton }
			</header>

			<DataViews
				data={ data }
				fields={ fields }
				view={ view }
				onChangeView={ setView }
				actions={ actions }
				paginationInfo={ paginationInfo }
				defaultLayouts={ defaultLayouts }
				isLoading={ isLoading }
				getItemId={ ( item: DiagramSummary ) => String( item.id ) }
				search
				searchLabel={ i18n.searchPlaceholder || __( 'Search diagrams', 'mermaid-diagrams' ) }
			/>

			<PreviewPanel
				isOpen={ preview.isOpen }
				status={ preview.status }
				payload={ preview.payload }
				usage={ preview.usage }
				error={ preview.error }
				onClose={ preview.closePreview }
				onDuplicate={ ( id ) => {
					void duplicateDiagram( id ).then( () => {
						createNotice( 'success', i18n.duplicatedNotice || 'Diagram duplicated.' );
						reload();
					} );
				} }
			onTrash={ ( id ) => {
				void bulkOperation( { ids: [ id ], operation: 'trash' } ).then( () => {
					createNotice( 'success', i18n.trashedNotice || 'Diagram moved to trash.' );
					reload();
				} );
			} }
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
		</div>
	);
}
