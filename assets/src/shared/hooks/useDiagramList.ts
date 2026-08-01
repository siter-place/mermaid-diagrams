/**
 * Diagram list data hook.
 *
 * @package
 */

import { useCallback, useEffect, useState } from '@wordpress/element';
import { searchDiagrams } from '../api';
import type { DiagramSearchResponse } from '../api/types';
import { getErrorMessage } from '../utils/errors';
import {
	parseLibraryQuery,
	toSearchQuery,
	updateBrowserQuery,
	type LibraryQueryState,
} from '../state/url-query';
import { useBootstrap } from '../providers/BootstrapProvider';

export type DiagramListStatus = 'loading' | 'ready' | 'empty' | 'error';

interface UseDiagramListResult {
	status: DiagramListStatus;
	data: DiagramSearchResponse | null;
	error: string | null;
	query: LibraryQueryState;
	setPage: ( page: number ) => void;
	reload: () => void;
}

export function useDiagramList(): UseDiagramListResult {
	const bootstrap = useBootstrap();
	const [ query, setQuery ] = useState< LibraryQueryState >( () =>
		parseLibraryQuery( window.location.search, bootstrap.defaults )
	);
	const [ status, setStatus ] = useState< DiagramListStatus >( 'loading' );
	const [ data, setData ] = useState< DiagramSearchResponse | null >( null );
	const [ error, setError ] = useState< string | null >( null );
	const [ reloadToken, setReloadToken ] = useState( 0 );

	const reload = useCallback( () => {
		setReloadToken( ( current ) => current + 1 );
	}, [] );

	const setPage = useCallback(
		( page: number ) => {
			setQuery( ( current ) => {
				const next = { ...current, page };
				updateBrowserQuery( next, bootstrap.routes.library );
				return next;
			} );
		},
		[ bootstrap.routes.library ]
	);

	useEffect( () => {
		const controller = new AbortController();

		async function load() {
			setStatus( 'loading' );
			setError( null );

			try {
				const response = await searchDiagrams(
					toSearchQuery( query ),
					controller.signal
				);
				setData( response );
				setStatus( response.items.length === 0 ? 'empty' : 'ready' );
			} catch ( loadError ) {
				if ( controller.signal.aborted ) {
					return;
				}

				setData( null );
				setError(
					getErrorMessage( loadError, bootstrap.i18n.errorTitle )
				);
				setStatus( 'error' );
			}
		}

		void load();

		return () => controller.abort();
	}, [ query, reloadToken, bootstrap.i18n.errorTitle ] );

	return {
		status,
		data,
		error,
		query,
		setPage,
		reload,
	};
}
