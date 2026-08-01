/**
 * Runtime diagnostics panel using WPDS Card.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { Card, CardHeader, CardBody, Icon } from '@wordpress/components';
import { info } from '@wordpress/icons';
import type { SettingsResponse } from '../../../shared/api/types';

interface RuntimeDiagnosticsProps {
  runtime: SettingsResponse['runtime'];
}

export function RuntimeDiagnostics({ runtime }: RuntimeDiagnosticsProps) {
  return (
    <Card className="mdm-runtime-diagnostics-card" data-testid="mdm-runtime-diagnostics">
      <CardHeader>
        <div className="mdm-settings-header">
          <div className="mdm-settings-header-icon">
            <Icon icon={info} size={20} />
          </div>
          <div className="mdm-settings-header-text">
            <h2>Runtime Diagnostics</h2>
            <p>System runtime metadata and environment versions.</p>
          </div>
        </div>
      </CardHeader>

      <CardBody>
        <dl className="mdm-diagnostics-grid">
          <div className="mdm-diagnostics-item">
            <dt>Plugin version</dt>
            <dd>{runtime.pluginVersion}</dd>
          </div>
          <div className="mdm-diagnostics-item">
            <dt>Mermaid version</dt>
            <dd>{runtime.mermaidVersion}</dd>
          </div>
          <div className="mdm-diagnostics-item">
            <dt>PHP version</dt>
            <dd>{runtime.phpVersion}</dd>
          </div>
          <div className="mdm-diagnostics-item">
            <dt>WordPress version</dt>
            <dd>{runtime.wpVersion}</dd>
          </div>
        </dl>
      </CardBody>
    </Card>
  );
}
