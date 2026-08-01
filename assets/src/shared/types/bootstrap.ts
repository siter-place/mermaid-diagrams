/**
 * Admin bootstrap payload exposed by PHP.
 *
 * @package
 */

export interface AdminBootstrap {
	screen: 'library' | 'settings';
	restRoot: string;
	nonce: string;
	locale: string;
	capabilities: {
		editDiagrams: boolean;
		manageSettings: boolean;
	};
	routes: {
		library: string;
		settings: string;
		editorNew: string;
	};
	defaults: {
		perPage: number;
		orderby: 'modified' | 'title';
		order: 'ASC' | 'DESC';
	};
	i18n: Record< string, string >;
}

declare global {
	interface Window {
		mdmAdminBootstrap?: AdminBootstrap;
	}
}

export function getAdminBootstrap(): AdminBootstrap {
	if ( ! window.mdmAdminBootstrap ) {
		throw new Error( 'Missing mdmAdminBootstrap payload.' );
	}

	return window.mdmAdminBootstrap;
}
