/**
 * Bootstrap React context provider.
 *
 * @package
 */

import { createContext, useContext, type ReactNode } from '@wordpress/element';
import type { AdminBootstrap } from '../types/bootstrap';

const BootstrapContext = createContext< AdminBootstrap | null >( null );

interface BootstrapProviderProps {
	value: AdminBootstrap;
	children: ReactNode;
}

export function BootstrapProvider( {
	value,
	children,
}: BootstrapProviderProps ) {
	return (
		<BootstrapContext.Provider value={ value }>
			{ children }
		</BootstrapContext.Provider>
	);
}

export function useBootstrap(): AdminBootstrap {
	const context = useContext( BootstrapContext );

	if ( ! context ) {
		throw new Error(
			'useBootstrap must be used within BootstrapProvider.'
		);
	}

	return context;
}
