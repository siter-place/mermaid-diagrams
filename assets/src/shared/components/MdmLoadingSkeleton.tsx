/**
 * Loading skeleton adapter.
 *
 * @package
 */

interface MdmLoadingSkeletonProps {
	rows?: number;
	'data-testid'?: string;
}

export function MdmLoadingSkeleton( {
	rows = 5,
	'data-testid': testId,
}: MdmLoadingSkeletonProps ) {
	return (
		<div data-testid={ testId } aria-busy="true" aria-live="polite">
			{ Array.from( { length: rows } ).map( ( _, index ) => (
				<div
					key={ index }
					className="mdm-skeleton-row"
					style={ { marginBottom: 8 } }
				/>
			) ) }
		</div>
	);
}
