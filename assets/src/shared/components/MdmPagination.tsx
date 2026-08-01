/**
 * Pagination adapter.
 *
 * @package
 */

import { MdmButton } from './MdmButton';

interface MdmPaginationProps {
	page: number;
	totalPages: number;
	previousLabel: string;
	nextLabel: string;
	pageLabel: string;
	onPrevious: () => void;
	onNext: () => void;
	'data-testid'?: string;
}

export function MdmPagination( {
	page,
	totalPages,
	previousLabel,
	nextLabel,
	pageLabel,
	onPrevious,
	onNext,
	'data-testid': testId,
}: MdmPaginationProps ) {
	return (
		<nav
			className="mdm-pagination"
			aria-label="Pagination"
			data-testid={ testId }
		>
			<MdmButton
				variant="secondary"
				onClick={ onPrevious }
				disabled={ page <= 1 }
			>
				{ previousLabel }
			</MdmButton>
			<span>{ pageLabel }</span>
			<MdmButton
				variant="secondary"
				onClick={ onNext }
				disabled={ page >= totalPages }
			>
				{ nextLabel }
			</MdmButton>
		</nav>
	);
}
