/**
 * Runtime diagnostics panel.
 *
 * @package
 */

import type { SettingsResponse } from '../../../shared/api/types';

interface RuntimeDiagnosticsProps {
	runtime: SettingsResponse[ 'runtime' ];
}

export function RuntimeDiagnostics( { runtime }: RuntimeDiagnosticsProps ) {
	return (
		<section
			className="mdm-runtime-diagnostics"
			data-testid="mdm-runtime-diagnostics"
		>
			<h2>Runtime diagnostics</h2>
			<dl>
				<dt>Plugin version</dt>
				<dd>{ runtime.pluginVersion }</dd>
				<dt>Mermaid version</dt>
				<dd>{ runtime.mermaidVersion }</dd>
				<dt>PHP version</dt>
				<dd>{ runtime.phpVersion }</dd>
				<dt>WordPress version</dt>
				<dd>{ runtime.wpVersion }</dd>
			</dl>
		</section>
	);
}
