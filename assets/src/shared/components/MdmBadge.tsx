/**
 * Badge status component.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { Icon } from '@wordpress/components';
import { check, close, info, key } from '@wordpress/icons';

export type BadgeVariant = 'publish' | 'draft' | 'private' | 'trash' | 'info' | 'success' | 'warning';

interface MdmBadgeProps {
  status: string;
  label?: string;
  'data-testid'?: string;
}

const VARIANT_ICONS: Record<string, import('@wordpress/components').IconProps<object>['icon']> = {
  publish: check,
  draft: info,
  private: key,
  trash: close,
};

export function MdmBadge({ status, label, 'data-testid': testId }: MdmBadgeProps) {
  const normalizedStatus = status.toLowerCase();
  const displayLabel = label || status;
  const icon = VARIANT_ICONS[normalizedStatus];

  return (
    <span
      className={`mdm-status-badge mdm-status-badge--${normalizedStatus}`}
      data-testid={testId}
    >
      {icon ? <Icon icon={icon} size={12} /> : null}
      <span>{displayLabel}</span>
    </span>
  );
}
