/**
 * Settings application root.
 *
 * @package
 */

import { AppProviders } from '../../shared/providers/AppProviders';
import { SettingsShell } from './components/SettingsShell';

export function App() {
	return (
		<AppProviders>
			<SettingsShell />
		</AppProviders>
	);
}
