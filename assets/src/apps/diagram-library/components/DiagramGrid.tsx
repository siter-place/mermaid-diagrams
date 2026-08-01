/**
 * Diagram grid / card view.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { copy, edit, info, trash } from '@wordpress/icons';
import type { DiagramSummary } from '../../../shared/api/types';
import { DiagramViewport } from '../../../shared/components/DiagramViewport';
import { MdmBadge } from '../../../shared/components/MdmBadge';
import { MdmButton } from '../../../shared/components/MdmButton';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';

interface DiagramGridProps {
	items: DiagramSummary[];
	selectedIds: Set< number >;
	onToggleRow: ( id: number ) => void;
	onPreview: ( item: DiagramSummary ) => void;
	onDuplicate: ( item: DiagramSummary ) => void;
	onTrash: ( item: DiagramSummary ) => void;
}

export function DiagramGrid( {
	items,
	selectedIds,
	onToggleRow,
	onPreview,
	onDuplicate,
	onTrash,
}: DiagramGridProps ) {
	const { i18n, routes } = useBootstrap();

	return (
		<div className="mdm-grid-wrap" data-testid="mdm-diagram-grid">
			{ items.map( ( item ) => {
				const isSelected = selectedIds.has( item.id );

				return (
					<div
						key={ item.id }
						className={ `mdm-card ${ isSelected ? 'is-selected' : '' }` }
						data-testid={ `mdm-card-${ item.id }` }
					>
						<div className="mdm-card__preview" onClick={ () => onPreview( item ) }>
							<DiagramViewport
								source={ item.source ?? 'graph TD\n  A --> B' }
								title={ item.title }
							/>
						</div>
						<div className="mdm-card__content">
							<div className="mdm-card__header">
								<input
									type="checkbox"
									checked={ isSelected }
									onChange={ () => onToggleRow( item.id ) }
									aria-label={ item.title }
									className="mdm-card__checkbox"
								/>
								<h3 className="mdm-card__title" title={ item.title }>
									{ item.title }
								</h3>
								<span className="mdm-type-badge">{ item.type }</span>
							</div>
							<div className="mdm-card__meta">
								<MdmBadge status={ item.status } />
								{ item.author?.name ? (
									<span className="mdm-card__author">{ item.author.name }</span>
								) : null }
							</div>
						</div>
						<div className="mdm-card__footer">
							<div className="mdm-card__actions">
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
							</div>
						</div>
					</div>
				);
			} ) }
		</div>
	);
}
