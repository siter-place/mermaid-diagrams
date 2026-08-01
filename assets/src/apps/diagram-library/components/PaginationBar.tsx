/**
 * Library pagination bar.
 *
 * @package
 */

import { MdmPagination } from '../../../shared/components/MdmPagination';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';

interface PaginationBarProps {
	page: number;
	totalPages: number;
	onPageChange: ( page: number ) => void;
}

export function PaginationBar( {
	page,
	totalPages,
	onPageChange,
}: PaginationBarProps ) {
	const { i18n } = useBootstrap();

	if ( totalPages <= 1 ) {
		return null;
	}

	return (
		<MdmPagination
			data-testid="mdm-library-pagination"
			page={ page }
			totalPages={ totalPages }
			previousLabel={ i18n.previousPage }
			nextLabel={ i18n.nextPage }
			pageLabel={ i18n.pageOf
				.replace( '%1$s', String( page ) )
				.replace( '%2$s', String( totalPages ) ) }
			onPrevious={ () => onPageChange( Math.max( 1, page - 1 ) ) }
			onNext={ () => onPageChange( Math.min( totalPages, page + 1 ) ) }
		/>
	);
}
