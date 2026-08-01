/**
 * Diagram table shell.
 *
 * @package
 */

import type { DiagramSummary } from '../../../shared/api/types';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';

interface DiagramTableProps {
	items: DiagramSummary[];
}

function formatDate( value: string ): string {
	if ( ! value ) {
		return '—';
	}

	const date = new Date( value );
	return Number.isNaN( date.getTime() ) ? value : date.toLocaleString();
}

export function DiagramTable( { items }: DiagramTableProps ) {
	const { i18n } = useBootstrap();

	return (
		<div className="mdm-table-wrap" data-testid="mdm-diagram-table">
			<table className="widefat striped">
				<thead>
					<tr>
						<th scope="col">{ i18n.columnTitle }</th>
						<th scope="col">{ i18n.columnStatus }</th>
						<th scope="col">{ i18n.columnAuthor }</th>
						<th scope="col">{ i18n.columnModified }</th>
						<th scope="col">{ i18n.columnUsage }</th>
					</tr>
				</thead>
				<tbody>
					{ items.map( ( item ) => (
						<tr key={ item.id }>
							<td>
								<strong>{ item.title }</strong>
								<span className="mdm-type-badge">
									{ item.type }
								</span>
							</td>
							<td>{ item.status }</td>
							<td>{ item.author?.name ?? '—' }</td>
							<td>{ formatDate( item.modifiedGmt ) }</td>
							<td>{ item.usageCount ?? 0 }</td>
						</tr>
					) ) }
				</tbody>
			</table>
		</div>
	);
}
