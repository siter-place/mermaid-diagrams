/**
 * Empty state adapter.
 *
 * @package
 */

import { Card, CardBody } from '@wordpress/components';

interface MdmEmptyStateProps {
	title: string;
	description: string;
	'data-testid'?: string;
}

export function MdmEmptyState( {
	title,
	description,
	'data-testid': testId,
}: MdmEmptyStateProps ) {
	return (
		<Card data-testid={ testId }>
			<CardBody>
				<h2>{ title }</h2>
				<p>{ description }</p>
			</CardBody>
		</Card>
	);
}
