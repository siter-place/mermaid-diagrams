/**
 * Diagram table shell.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import type { DiagramSummary } from '../../../shared/api/types';
import { MdmBadge } from '../../../shared/components/MdmBadge';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';

interface DiagramTableProps {
  items: DiagramSummary[];
}

function formatDate(value: string): string {
  if (!value || value.startsWith('-') || value.startsWith('0000')) {
    return '—';
  }

  const date = new Date(value);
  if (Number.isNaN(date.getTime()) || date.getFullYear() < 2000) {
    return '—';
  }

  return date.toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
}

export function DiagramTable({ items }: DiagramTableProps) {
  const { i18n } = useBootstrap();

  return (
    <div className="mdm-table-wrap" data-testid="mdm-diagram-table">
      <table className="widefat striped">
        <thead>
          <tr>
            <th scope="col">{i18n.columnTitle}</th>
            <th scope="col">{i18n.columnStatus}</th>
            <th scope="col">{i18n.columnAuthor}</th>
            <th scope="col">{i18n.columnModified}</th>
            <th scope="col">{i18n.columnUsage}</th>
          </tr>
        </thead>
        <tbody>
          {items.map((item) => (
            <tr key={item.id}>
              <td>
                <strong>{item.title}</strong>
                <span className="mdm-type-badge">{item.type}</span>
              </td>
              <td>
                <MdmBadge status={item.status} />
              </td>
              <td>{item.author?.name ?? '—'}</td>
              <td>{formatDate(item.modifiedGmt)}</td>
              <td>{item.usageCount ?? 0}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
