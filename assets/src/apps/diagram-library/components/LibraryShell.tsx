/**
 * Diagram library shell layout.
 *
 * @package
 */

import { MdmButton } from '../../../shared/components/MdmButton';
import { MdmEmptyState } from '../../../shared/components/MdmEmptyState';
import { MdmErrorState } from '../../../shared/components/MdmErrorState';
import { MdmLoadingSkeleton } from '../../../shared/components/MdmLoadingSkeleton';
import { useDiagramList } from '../../../shared/hooks/useDiagramList';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';
import { DiagramTable } from './DiagramTable';
import { PaginationBar } from './PaginationBar';

export function LibraryShell() {
	const { i18n } = useBootstrap();
	const { status, data, error, query, setPage, reload } = useDiagramList();

	return (
		<div className="mdm-app-layout" data-testid="mdm-library-shell">
			<div className="mdm-app-header">
				<p>{ i18n.libraryTitle }</p>
				<MdmButton variant="primary" disabled title={ i18n.comingSoon }>
					{ i18n.addDiagram }
				</MdmButton>
			</div>

			{ status === 'loading' ? (
				<MdmLoadingSkeleton data-testid="mdm-library-loading" />
			) : null }

			{ status === 'empty' ? (
				<MdmEmptyState
					data-testid="mdm-library-empty"
					title={ i18n.emptyTitle }
					description={ i18n.emptyDescription }
				/>
			) : null }

			{ status === 'error' ? (
				<MdmErrorState
					data-testid="mdm-library-error"
					title={ i18n.errorTitle }
					message={ error ?? i18n.errorTitle }
					onRetry={ reload }
					retryLabel={ i18n.retry }
				/>
			) : null }

			{ status === 'ready' && data ? (
				<>
					<DiagramTable
						items={
							data.items as import('../../../shared/api/types').DiagramSummary[]
						}
					/>
					<PaginationBar
						page={ query.page }
						totalPages={ Math.max( 1, data.pagination.totalPages ) }
						onPageChange={ setPage }
					/>
				</>
			) : null }
		</div>
	);
}
