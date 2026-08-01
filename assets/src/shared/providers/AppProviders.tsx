/**
 * Root provider composition for admin React apps.
 *
 * @package
 */

import type { ReactNode } from '@wordpress/element';
import { getAdminBootstrap } from '../types/bootstrap';
import { AppErrorBoundary } from './AppErrorBoundary';
import { BootstrapProvider } from './BootstrapProvider';
import { NoticesProvider } from './NoticesProvider';
import '../styles/mdm-app.css';

interface AppProvidersProps {
	children: ReactNode;
}

export function AppProviders( { children }: AppProvidersProps ) {
	const bootstrap = getAdminBootstrap();

	return (
		<BootstrapProvider value={ bootstrap }>
			<NoticesProvider>
				<AppErrorBoundary>{ children }</AppErrorBoundary>
			</NoticesProvider>
		</BootstrapProvider>
	);
}
