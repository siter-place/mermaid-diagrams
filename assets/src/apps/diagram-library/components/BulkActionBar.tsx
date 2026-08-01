/**
 * Bulk action toolbar for selected diagrams.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { useState } from '@wordpress/element';
import {
	Button,
	Modal,
	SelectControl,
} from '@wordpress/components';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';
import { useTaxonomyTerms } from '../../../shared/hooks/useTaxonomyTerms';
import type { BulkOperationType } from '../../../shared/api/types';

interface BulkActionBarProps {
	selectedCount: number;
	isRunning: boolean;
	onApply: (
		operation: BulkOperationType,
		payload?: { category_ids?: number[]; tag_ids?: number[]; status?: string }
	) => Promise< void >;
}

export function BulkActionBar( {
	selectedCount,
	isRunning,
	onApply,
}: BulkActionBarProps ) {
	const { i18n } = useBootstrap();
	const [ action, setAction ] = useState< BulkOperationType | '' >( '' );
	const [ confirmTrash, setConfirmTrash ] = useState( false );
	const [ categoryId, setCategoryId ] = useState( '' );
	const [ tagId, setTagId ] = useState( '' );
	const [ status, setStatus ] = useState( 'draft' );
	const { terms: categories } = useTaxonomyTerms( {
		taxonomy: 'mdm-diagram-categories',
	} );
	const { terms: tags } = useTaxonomyTerms( {
		taxonomy: 'mdm-diagram-tags',
	} );

	if ( selectedCount === 0 ) {
		return null;
	}

	const categoryOps = [
		'add_categories',
		'remove_categories',
		'replace_categories',
	];
	const tagOps = [ 'add_tags', 'remove_tags' ];

	async function handleApply() {
		if ( ! action ) {
			return;
		}

		if ( action === 'trash' ) {
			setConfirmTrash( true );
			return;
		}

		const payload: {
			category_ids?: number[];
			tag_ids?: number[];
			status?: string;
		} = {};

		if ( categoryOps.includes( action ) && categoryId ) {
			payload.category_ids = [ Number( categoryId ) ];
		}
		if ( tagOps.includes( action ) && tagId ) {
			payload.tag_ids = [ Number( tagId ) ];
		}
		if ( action === 'set_status' ) {
			payload.status = status;
		}

		await onApply( action, payload );
		setAction( '' );
	}

	return (
		<div className="mdm-bulk-bar" data-testid="mdm-bulk-bar">
			<span>{ `${ selectedCount } selected` }</span>
			<SelectControl
				label={ i18n.bulkActions || 'Bulk actions' }
				hideLabelFromVision
				value={ action }
				options={ [
					{ value: '', label: i18n.bulkActions || 'Bulk actions' },
					{
						value: 'add_categories',
						label: i18n.bulkAddCategories || 'Add categories',
					},
					{
						value: 'remove_categories',
						label: i18n.bulkRemoveCategories || 'Remove categories',
					},
					{
						value: 'replace_categories',
						label: i18n.bulkReplaceCategories || 'Replace categories',
					},
					{ value: 'add_tags', label: i18n.bulkAddTags || 'Add tags' },
					{
						value: 'remove_tags',
						label: i18n.bulkRemoveTags || 'Remove tags',
					},
					{
						value: 'set_status',
						label: i18n.bulkSetStatus || 'Change status',
					},
					{ value: 'trash', label: i18n.bulkTrash || 'Move to trash' },
					{ value: 'restore', label: i18n.bulkRestore || 'Restore' },
				] }
				onChange={ ( value ) => setAction( value as BulkOperationType | '' ) }
			/>
			{ categoryOps.includes( action ) ? (
				<SelectControl
					label={ i18n.filterCategory || 'Category' }
					hideLabelFromVision
					value={ categoryId }
					options={ [
						{ value: '', label: i18n.filterCategory || 'Category' },
						...categories.map( ( term ) => ( {
							value: String( term.id ),
							label: term.name,
						} ) ),
					] }
					onChange={ setCategoryId }
				/>
			) : null }
			{ tagOps.includes( action ) ? (
				<SelectControl
					label={ i18n.filterTag || 'Tag' }
					hideLabelFromVision
					value={ tagId }
					options={ [
						{ value: '', label: i18n.filterTag || 'Tag' },
						...tags.map( ( term ) => ( {
							value: String( term.id ),
							label: term.name,
						} ) ),
					] }
					onChange={ setTagId }
				/>
			) : null }
			{ action === 'set_status' ? (
				<SelectControl
					label={ i18n.filterStatus || 'Status' }
					hideLabelFromVision
					value={ status }
					options={ [
						{ value: 'draft', label: 'Draft' },
						{ value: 'publish', label: 'Published' },
						{ value: 'private', label: 'Private' },
						{ value: 'pending', label: 'Pending' },
					] }
					onChange={ setStatus }
				/>
			) : null }
			<Button
				variant="primary"
				disabled={ ! action || isRunning }
				onClick={ () => void handleApply() }
			>
				{ i18n.bulkApply || 'Apply' }
			</Button>

			{ confirmTrash ? (
				<Modal
					title={ i18n.bulkTrash || 'Move to trash' }
					onRequestClose={ () => setConfirmTrash( false ) }
				>
					<p>{ 'Move selected diagrams to trash?' }</p>
					<div className="mdm-modal-actions">
						<Button variant="secondary" onClick={ () => setConfirmTrash( false ) }>
							{ i18n.cancel || 'Cancel' }
						</Button>
						<Button
							variant="primary"
							isDestructive
							onClick={ () => {
								void onApply( 'trash' ).then( () => setConfirmTrash( false ) );
							} }
						>
							{ i18n.confirmTrash || 'Confirm' }
						</Button>
					</div>
				</Modal>
			) : null }
		</div>
	);
}
