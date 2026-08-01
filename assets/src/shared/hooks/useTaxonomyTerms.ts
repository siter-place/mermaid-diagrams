/**
 * Debounced taxonomy term search against WordPress core REST.
 *
 * @package
 */

import { useCallback, useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

export interface TaxonomyTermOption {
	id: number;
	name: string;
}

interface UseTaxonomyTermsOptions {
	taxonomy: 'mdm-diagram-categories' | 'mdm-diagram-tags';
	search?: string;
	enabled?: boolean;
}

export function useTaxonomyTerms( {
	taxonomy,
	search = '',
	enabled = true,
}: UseTaxonomyTermsOptions ) {
	const [ terms, setTerms ] = useState< TaxonomyTermOption[] >( [] );
	const [ isLoading, setIsLoading ] = useState( false );

	const loadTerms = useCallback( async () => {
		if ( ! enabled ) {
			return;
		}

		setIsLoading( true );
		try {
			const params = new URLSearchParams( {
				per_page: '20',
				orderby: 'name',
				order: 'asc',
				hide_empty: 'false',
			} );
			if ( search.trim() ) {
				params.set( 'search', search.trim() );
			}

			const response = await apiFetch< Array< { id: number; name: string } > >(
				{
					path: `/wp/v2/${ taxonomy }?${ params.toString() }`,
				}
			);

			setTerms(
				response.map( ( term ) => ( {
					id: term.id,
					name: term.name,
				} ) )
			);
		} catch {
			setTerms( [] );
		} finally {
			setIsLoading( false );
		}
	}, [ taxonomy, search, enabled ] );

	useEffect( () => {
		const timer = window.setTimeout( () => {
			void loadTerms();
		}, 250 );

		return () => window.clearTimeout( timer );
	}, [ loadTerms ] );

	return { terms, isLoading, reload: loadTerms };
}
