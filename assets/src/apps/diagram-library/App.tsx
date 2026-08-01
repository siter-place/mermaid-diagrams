/**
 * Diagram library application root.
 *
 * @package
 */

import { AppProviders } from '../../shared/providers/AppProviders';
import { LibraryShell } from './components/LibraryShell';

export function App() {
	return (
		<AppProviders>
			<LibraryShell />
		</AppProviders>
	);
}
