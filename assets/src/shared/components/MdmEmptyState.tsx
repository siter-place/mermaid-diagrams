/**
 * Empty state adapter with WPDS styling and icon.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { Card, CardBody, Icon } from '@wordpress/components';
import { file } from '@wordpress/icons';

interface MdmEmptyStateProps {
  title: string;
  description: string;
  icon?: import('@wordpress/components').IconProps<object>['icon'];
  'data-testid'?: string;
}

export function MdmEmptyState({
  title,
  description,
  icon = file,
  'data-testid': testId,
}: MdmEmptyStateProps) {
  return (
    <Card data-testid={testId}>
      <CardBody>
        <div style={{ textAlign: 'center', padding: '32px 16px' }}>
          <div style={{ color: 'var(--mdm-muted-color)', marginBottom: 12 }}>
            <Icon icon={icon} size={48} />
          </div>
          <h2 style={{ fontSize: 18, fontWeight: 600, margin: '0 0 8px 0', color: '#1d2327' }}>
            {title}
          </h2>
          <p style={{ color: 'var(--mdm-muted-color)', margin: 0, fontSize: 13 }}>
            {description}
          </p>
        </div>
      </CardBody>
    </Card>
  );
}
