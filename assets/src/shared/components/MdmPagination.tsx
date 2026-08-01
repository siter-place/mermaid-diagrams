/**
 * Pagination adapter with item count summary.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { MdmButton } from './MdmButton';

interface MdmPaginationProps {
  page: number;
  totalPages: number;
  totalItems?: number;
  perPage?: number;
  previousLabel: string;
  nextLabel: string;
  pageLabel: string;
  onPrevious: () => void;
  onNext: () => void;
  'data-testid'?: string;
}

export function MdmPagination({
  page,
  totalPages,
  totalItems,
  perPage = 20,
  previousLabel,
  nextLabel,
  pageLabel,
  onPrevious,
  onNext,
  'data-testid': testId,
}: MdmPaginationProps) {
  const startItem = totalItems && totalItems > 0 ? (page - 1) * perPage + 1 : 0;
  const endItem = totalItems ? Math.min(page * perPage, totalItems) : 0;

  return (
    <div className="mdm-pagination-bar" data-testid={testId}>
      <div className="mdm-pagination-summary">
        {totalItems ? `Showing ${startItem}–${endItem} of ${totalItems} items` : null}
      </div>

      <nav className="mdm-pagination" aria-label="Pagination">
        <MdmButton
          variant="secondary"
          onClick={onPrevious}
          disabled={page <= 1}
        >
          {previousLabel}
        </MdmButton>
        <span>{pageLabel}</span>
        <MdmButton
          variant="secondary"
          onClick={onNext}
          disabled={page >= totalPages}
        >
          {nextLabel}
        </MdmButton>
      </nav>
    </div>
  );
}
