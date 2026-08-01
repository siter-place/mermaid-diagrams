/**
 * Error state adapter with WPDS styling and icon.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { Card, CardBody, Icon } from '@wordpress/components';
import { close } from '@wordpress/icons';
import { MdmButton } from './MdmButton';

interface MdmErrorStateProps {
  title: string;
  message: string;
  onRetry?: () => void;
  retryLabel?: string;
  'data-testid'?: string;
}

export function MdmErrorState({
  title,
  message,
  onRetry,
  retryLabel = 'Try again',
  'data-testid': testId,
}: MdmErrorStateProps) {
  return (
    <Card data-testid={testId}>
      <CardBody>
        <div style={{ textAlign: 'center', padding: '32px 16px' }}>
          <div style={{ color: '#d63638', marginBottom: 12 }}>
            <Icon icon={close} size={48} />
          </div>
          <h2 style={{ fontSize: 18, fontWeight: 600, margin: '0 0 8px 0', color: '#1d2327' }}>
            {title}
          </h2>
          <p style={{ color: 'var(--mdm-muted-color)', margin: '0 0 16px 0', fontSize: 13 }}>
            {message}
          </p>
          {onRetry ? (
            <MdmButton variant="secondary" onClick={onRetry}>
              {retryLabel}
            </MdmButton>
          ) : null}
        </div>
      </CardBody>
    </Card>
  );
}
