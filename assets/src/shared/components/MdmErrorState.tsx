/**
 * Error state adapter.
 *
 * @package
 */

import { Card, CardBody } from '@wordpress/components';
import { MdmButton } from './MdmButton';

interface MdmErrorStateProps {
	title: string;
	message: string;
	onRetry?: () => void;
	retryLabel?: string;
	'data-testid'?: string;
}

export function MdmErrorState( {
	title,
	message,
	onRetry,
	retryLabel = 'Try again',
	'data-testid': testId,
}: MdmErrorStateProps ) {
	return (
		<Card data-testid={ testId }>
			<CardBody>
				<h2>{ title }</h2>
				<p>{ message }</p>
				{ onRetry ? (
					<MdmButton variant="secondary" onClick={ onRetry }>
						{ retryLabel }
					</MdmButton>
				) : null }
			</CardBody>
		</Card>
	);
}
