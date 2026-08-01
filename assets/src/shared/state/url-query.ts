/**
 * URL query helpers for library pagination state.
 *
 * @package
 */

export interface LibraryQueryState {
	page: number;
	perPage: number;
	search?: string;
	category?: number[];
	tag?: number[];
	type?: string[];
	status?: string[];
	author?: number[];
	orderby?: 'modified' | 'title';
	order?: 'ASC' | 'DESC';
}

const DEFAULTS: LibraryQueryState = {
	page: 1,
	perPage: 20,
	orderby: 'modified',
	order: 'DESC',
};

function parseNumber( value: string | null, fallback: number ): number {
	if ( ! value ) {
		return fallback;
	}

	const parsed = Number.parseInt( value, 10 );
	return Number.isFinite( parsed ) && parsed > 0 ? parsed : fallback;
}

function parseNumberArray( values: string[] ): number[] {
	return values
		.map( ( value ) => Number.parseInt( value, 10 ) )
		.filter( ( value ) => Number.isFinite( value ) );
}

export function parseLibraryQuery(
	search: string,
	defaults: Partial< LibraryQueryState > = {}
): LibraryQueryState {
	const params = new URLSearchParams( search );
	const mergedDefaults = { ...DEFAULTS, ...defaults };

	return {
		page: parseNumber( params.get( 'paged' ), mergedDefaults.page ),
		perPage: parseNumber(
			params.get( 'per_page' ),
			mergedDefaults.perPage
		),
		search: params.get( 'search' ) || undefined,
		category: parseNumberArray( params.getAll( 'category[]' ) ),
		tag: parseNumberArray( params.getAll( 'tag[]' ) ),
		type: params.getAll( 'type[]' ).filter( Boolean ),
		status: params.getAll( 'status[]' ).filter( Boolean ),
		author: parseNumberArray( params.getAll( 'author[]' ) ),
		orderby:
			( params.get( 'orderby' ) as LibraryQueryState[ 'orderby' ] ) ||
			mergedDefaults.orderby,
		order:
			( params
				.get( 'order' )
				?.toUpperCase() as LibraryQueryState[ 'order' ] ) ||
			mergedDefaults.order,
	};
}

export function serializeLibraryQuery( state: LibraryQueryState ): string {
	const params = new URLSearchParams();

	if ( state.page > 1 ) {
		params.set( 'paged', String( state.page ) );
	}

	if ( state.perPage !== DEFAULTS.perPage ) {
		params.set( 'per_page', String( state.perPage ) );
	}

	if ( state.search ) {
		params.set( 'search', state.search );
	}

	state.category?.forEach( ( value ) =>
		params.append( 'category[]', String( value ) )
	);
	state.tag?.forEach( ( value ) =>
		params.append( 'tag[]', String( value ) )
	);
	state.type?.forEach( ( value ) => params.append( 'type[]', value ) );
	state.status?.forEach( ( value ) => params.append( 'status[]', value ) );
	state.author?.forEach( ( value ) =>
		params.append( 'author[]', String( value ) )
	);

	if ( state.orderby && state.orderby !== DEFAULTS.orderby ) {
		params.set( 'orderby', state.orderby );
	}

	if ( state.order && state.order !== DEFAULTS.order ) {
		params.set( 'order', state.order );
	}

	return params.toString();
}

export function updateBrowserQuery(
	state: LibraryQueryState,
	pageSlug: string
): void {
	const query = serializeLibraryQuery( state );
	const nextUrl = query ? `${ pageSlug }&${ query }` : pageSlug;
	window.history.replaceState( {}, '', nextUrl );
}

export function toSearchQuery( state: LibraryQueryState ) {
	return {
		page: state.page,
		per_page: state.perPage,
		search: state.search,
		category: state.category,
		tag: state.tag,
		type: state.type,
		status: state.status,
		author: state.author,
		orderby: state.orderby ?? DEFAULTS.orderby,
		order: state.order ?? DEFAULTS.order,
		view: 'summary' as const,
	};
}
