/**
 * Runtime diagnostics section content.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import type { SettingsResponse } from '../../../shared/api/types';

interface RuntimeDiagnosticsProps {
  runtime: SettingsResponse['runtime'];
}

export function RuntimeDiagnostics({ runtime }: RuntimeDiagnosticsProps) {
  return (
    <div className="mdm-diagnostics" data-testid="mdm-runtime-diagnostics">
      <p className="mdm-diagnostics__description">
        System runtime metadata and environment versions.
      </p>
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
    </div>
  );
}
