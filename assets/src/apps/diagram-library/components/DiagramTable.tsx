/**
 * Diagram table shell.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { CheckboxControl } from '@wordpress/components';
import { copy, edit, info, trash } from '@wordpress/icons';
import type { DiagramSummary } from '../../../shared/api/types';
import { MdmBadge } from '../../../shared/components/MdmBadge';
import { MdmButton } from '../../../shared/components/MdmButton';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';
import type { TableDensity, VisibleProperties } from './FilterBar';

interface DiagramTableProps {
	items: DiagramSummary[];
	selectedIds: Set< number >;
	density?: TableDensity;
	visibleProperties?: VisibleProperties;
	onToggleRow: ( id: number ) => void;
	onTogglePage: ( items: DiagramSummary[] ) => void;
	onPreview: ( item: DiagramSummary ) => void;
	onDuplicate: ( item: DiagramSummary ) => void;
	onTrash: ( item: DiagramSummary ) => void;
}

const DEFAULT_PROPERTIES: VisibleProperties = {
	title: true,
	categories: true,
	tags: true,
	status: true,
	author: true,
	modified: true,
	usage: true,
	actions: true,
};

function formatDate( value: string ): string {
	if ( ! value || value.startsWith( '-' ) || value.startsWith( '0000' ) ) {
		return '—';
	}

	const date = new Date( value );
	if ( Number.isNaN( date.getTime() ) || date.getFullYear() < 2000 ) {
		return '—';
	}

	return date.toLocaleDateString( undefined, {
		year: 'numeric',
		month: 'short',
		day: 'numeric',
	} );
}

function formatTerms( terms: DiagramSummary[ 'categories' ] ): string {
	if ( ! terms.length ) {
		return '—';
	}
	return terms.map( ( term ) => term.name ).join( ', ' );
}

export function DiagramTable( {
	items,
	selectedIds,
	density = 'balanced',
	visibleProperties = DEFAULT_PROPERTIES,
	onToggleRow,
	onTogglePage,
	onPreview,
	onDuplicate,
	onTrash,
}: DiagramTableProps ) {
	const { i18n, routes } = useBootstrap();
	const allSelected =
		items.length > 0 && items.every( ( item ) => selectedIds.has( item.id ) );

	return (
		<div
			className={ `mdm-table-wrap mdm-table-wrap--density-${ density }` }
			data-testid="mdm-diagram-table"
		>
			<table className="widefat striped">
				<thead>
					<tr>
						<td className="check-column">
							<CheckboxControl
								aria-label={ i18n.selectAll || 'Select all on page' }
								checked={ allSelected }
								onChange={ () => onTogglePage( items ) }
							/>
						</td>
						<th scope="col">{ i18n.columnTitle }</th>
						{ visibleProperties.categories ? (
							<th scope="col">{ i18n.columnCategories || 'Categories' }</th>
						) : null }
						{ visibleProperties.tags ? (
							<th scope="col">{ i18n.columnTags || 'Tags' }</th>
						) : null }
						{ visibleProperties.status ? (
							<th scope="col">{ i18n.columnStatus }</th>
						) : null }
						{ visibleProperties.author ? (
							<th scope="col">{ i18n.columnAuthor }</th>
						) : null }
						{ visibleProperties.modified ? (
							<th scope="col">{ i18n.columnModified }</th>
						) : null }
						{ visibleProperties.usage ? (
							<th scope="col">{ i18n.columnUsage }</th>
						) : null }
						{ visibleProperties.actions ? (
							<th scope="col">{ i18n.columnActions || 'Actions' }</th>
						) : null }
					</tr>
				</thead>
				<tbody>
					{ items.map( ( item ) => (
						<tr key={ item.id } aria-label={ item.title }>
							<th scope="row" className="check-column">
								<CheckboxControl
									aria-label={ item.title }
									checked={ selectedIds.has( item.id ) }
									onChange={ () => onToggleRow( item.id ) }
								/>
							</th>
							<td>
								<strong>{ item.title }</strong>
								<span className="mdm-type-badge">{ item.type }</span>
							</td>
							{ visibleProperties.categories ? (
								<td>{ formatTerms( item.categories ) }</td>
							) : null }
							{ visibleProperties.tags ? (
								<td>{ formatTerms( item.tags ) }</td>
							) : null }
							{ visibleProperties.status ? (
								<td>
									<MdmBadge status={ item.status } />
								</td>
							) : null }
							{ visibleProperties.author ? (
								<td>{ item.author?.name ?? '—' }</td>
							) : null }
							{ visibleProperties.modified ? (
								<td>{ formatDate( item.modifiedGmt ) }</td>
							) : null }
							{ visibleProperties.usage ? (
								<td>{ item.usageCount ?? 0 }</td>
							) : null }
							{ visibleProperties.actions ? (
								<td className="mdm-row-actions">
									{ item.can.edit ? (
										<MdmButton
											variant="tertiary"
											size="compact"
											icon={ edit }
											href={ `${ routes.editor }&diagram=${ item.id }` }
											label={ `${ i18n.editDiagram || 'Edit' } ${ item.title }` }
											showTooltip
										/>
									) : null }
									<MdmButton
										variant="tertiary"
										size="compact"
										icon={ info }
										onClick={ () => onPreview( item ) }
										label={ `${ i18n.preview || 'Preview' } ${ item.title }` }
										showTooltip
									/>
									<MdmButton
										variant="tertiary"
										size="compact"
										icon={ copy }
										onClick={ () => onDuplicate( item ) }
										label={ `${ i18n.duplicateDiagram || 'Duplicate' } ${ item.title }` }
										showTooltip
									/>
									{ item.can.delete ? (
										<MdmButton
											variant="tertiary"
											size="compact"
											isDestructive
											icon={ trash }
											onClick={ () => onTrash( item ) }
											label={ `${ i18n.trashDiagram || 'Trash' } ${ item.title }` }
											showTooltip
										/>
									) : null }
								</td>
							) : null }
						</tr>
					) ) }
				</tbody>
			</table>
		</div>
	);
}
