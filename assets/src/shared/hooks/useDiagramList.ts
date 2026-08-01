/**
 * Diagram list data hook.
 *
 * @package
 */

import { useCallback, useEffect, useMemo, useRef, useState } from '@wordpress/element';
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

export type DiagramListStatus =
	| 'loading'
	| 'ready'
	| 'empty'
	| 'no-match'
	| 'error';

interface UseDiagramListResult {
	status: DiagramListStatus;
	data: DiagramSearchResponse | null;
	error: string | null;
	query: LibraryQueryState;
	setPage: ( page: number ) => void;
	setFilters: ( patch: Partial< LibraryQueryState > ) => void;
	resetFilters: () => void;
	reload: () => void;
	hasActiveFilters: boolean;
}

const SEARCH_DEBOUNCE_MS = 300;

export function useDiagramList(): UseDiagramListResult {
	const bootstrap = useBootstrap();
	const [ query, setQuery ] = useState< LibraryQueryState >( () =>
		parseLibraryQuery( window.location.search, bootstrap.defaults )
	);
	const [ debouncedSearch, setDebouncedSearch ] = useState(
		query.search ?? ''
	);
	const [ status, setStatus ] = useState< DiagramListStatus >( 'loading' );
	const [ data, setData ] = useState< DiagramSearchResponse | null >( null );
	const [ error, setError ] = useState< string | null >( null );
	const [ reloadToken, setReloadToken ] = useState( 0 );
	const initialLoadRef = useRef( true );

	const hasActiveFilters = useMemo( () => {
		return Boolean(
			query.search ||
				query.category?.length ||
				query.tag?.length ||
				query.type?.length ||
				query.status?.length ||
				query.author?.length ||
				( query.orderby && query.orderby !== bootstrap.defaults.orderby ) ||
				( query.order && query.order !== bootstrap.defaults.order )
		);
	}, [ query, bootstrap.defaults.order, bootstrap.defaults.orderby ] );

	useEffect( () => {
		const timer = window.setTimeout( () => {
			setDebouncedSearch( query.search ?? '' );
		}, SEARCH_DEBOUNCE_MS );

		return () => window.clearTimeout( timer );
	}, [ query.search ] );

	const reload = useCallback( () => {
		setReloadToken( ( current ) => current + 1 );
	}, [] );

	const applyQuery = useCallback(
		( updater: ( current: LibraryQueryState ) => LibraryQueryState ) => {
			setQuery( ( current ) => {
				const next = updater( current );
				updateBrowserQuery( next, bootstrap.routes.library );
				return next;
			} );
		},
		[ bootstrap.routes.library ]
	);

	const setPage = useCallback(
		( page: number ) => {
			applyQuery( ( current ) => ( { ...current, page } ) );
		},
		[ applyQuery ]
	);

	const setFilters = useCallback(
		( patch: Partial< LibraryQueryState > ) => {
			applyQuery( ( current ) => ( {
				...current,
				...patch,
				page: patch.page ?? 1,
			} ) );
		},
		[ applyQuery ]
	);

	const resetFilters = useCallback( () => {
		applyQuery( () => ( {
			page: 1,
			perPage: bootstrap.defaults.perPage,
			orderby: bootstrap.defaults.orderby,
			order: bootstrap.defaults.order,
		} ) );
	}, [
		applyQuery,
		bootstrap.defaults.order,
		bootstrap.defaults.orderby,
		bootstrap.defaults.perPage,
	] );

	useEffect( () => {
		const controller = new AbortController();
		const effectiveQuery = {
			...query,
			search: debouncedSearch || undefined,
		};

		async function load() {
			setStatus( 'loading' );
			setError( null );

			try {
				const response = await searchDiagrams(
					toSearchQuery( effectiveQuery ),
					controller.signal
				);
				setData( response );

				if ( response.items.length === 0 ) {
					setStatus(
						hasActiveFilters || debouncedSearch
							? 'no-match'
							: initialLoadRef.current
								? 'empty'
								: 'no-match'
					);
				} else {
					setStatus( 'ready' );
				}

				initialLoadRef.current = false;
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
	}, [
		query,
		debouncedSearch,
		reloadToken,
		bootstrap.i18n.errorTitle,
		hasActiveFilters,
	] );

	return {
		status,
		data,
		error,
		query,
		setPage,
		setFilters,
		resetFilters,
		reload,
		hasActiveFilters,
	};
}
