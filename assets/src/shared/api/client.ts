/**
 * REST API Client for Mermaid Diagrams using WordPress api-fetch.
 *
 * @package
 */

import apiFetch from '@wordpress/api-fetch';
import {
	BulkOperationRequest,
	BulkOperationResult,
	CreateDiagramRequest,
	DiagramDetail,
	DiagramPreviewPayload,
	DiagramSearchQuery,
	DiagramSearchResponse,
	DiagramUsageResponse,
	EditConflictError,
	ErrorEnvelope,
	SettingsResponse,
	UpdateDiagramRequest,
} from './types';

const REST_NAMESPACE = '/mdm/v1';

/**
 * Format query params for apiFetch path string.
 * @param params
 */
function buildQueryString( params: Record< string, unknown > ): string {
	const query = new URLSearchParams();

	for ( const [ key, value ] of Object.entries( params ) ) {
		if ( value === undefined || value === null ) {
			continue;
		}
		if ( Array.isArray( value ) ) {
			for ( const item of value ) {
				query.append( `${ key }[]`, String( item ) );
			}
		} else {
			query.append( key, String( value ) );
		}
	}

	const str = query.toString();
	return str ? `?${ str }` : '';
}

/**
 * Parse and throw normalized API errors.
 * @param error
 */
function handleApiError( error: unknown ): never {
	const err = error as ErrorEnvelope;
	if ( err && err.code === 'mdm_edit_conflict' ) {
		throw new EditConflictError(
			err.message || 'The diagram was modified by another session.',
			err.data?.expectedVersion,
			err.data?.currentDiagram
		);
	}
	if ( err && err.message ) {
		throw new Error( err.message );
	}
	throw new Error( 'An unexpected REST API error occurred.' );
}

/**
 * Search and list diagrams.
 * @param query
 * @param signal
 */
export async function searchDiagrams(
	query: DiagramSearchQuery = {},
	signal?: AbortSignal
): Promise< DiagramSearchResponse > {
	try {
		const queryString = buildQueryString(
			query as Record< string, unknown >
		);
		const path = `${ REST_NAMESPACE }/diagrams${ queryString }`;
		return await apiFetch< DiagramSearchResponse >( { path, signal } );
	} catch ( error ) {
		handleApiError( error );
	}
}

/**
 * Get a single diagram detail by ID.
 * @param id
 * @param signal
 */
export async function getDiagram(
	id: number,
	signal?: AbortSignal
): Promise< DiagramDetail > {
	try {
		const path = `${ REST_NAMESPACE }/diagrams/${ id }`;
		return await apiFetch< DiagramDetail >( { path, signal } );
	} catch ( error ) {
		handleApiError( error );
	}
}

/**
 * Create a new diagram.
 * @param data
 * @param idempotencyKey
 */
export async function createDiagram(
	data: CreateDiagramRequest,
	idempotencyKey?: string
): Promise< DiagramDetail > {
	try {
		const payload = {
			...data,
			idempotencyKey: idempotencyKey || data.idempotencyKey,
		};
		return await apiFetch< DiagramDetail >( {
			path: `${ REST_NAMESPACE }/diagrams`,
			method: 'POST',
			data: payload,
		} );
	} catch ( error ) {
		handleApiError( error );
	}
}

/**
 * Update an existing diagram with optimistic version token check.
 * @param id
 * @param data
 */
export async function updateDiagram(
	id: number,
	data: UpdateDiagramRequest
): Promise< DiagramDetail > {
	try {
		return await apiFetch< DiagramDetail >( {
			path: `${ REST_NAMESPACE }/diagrams/${ id }`,
			method: 'PATCH',
			data,
		} );
	} catch ( error ) {
		handleApiError( error );
	}
}

/**
 * Trash or force-delete a diagram.
 * @param id
 * @param force
 */
export async function deleteDiagram(
	id: number,
	force = false
): Promise< { deleted: boolean; id: number; force: boolean } > {
	try {
		const path = `${ REST_NAMESPACE }/diagrams/${ id }?force=${
			force ? 'true' : 'false'
		}`;
		return await apiFetch< {
			deleted: boolean;
			id: number;
			force: boolean;
		} >( {
			path,
			method: 'DELETE',
		} );
	} catch ( error ) {
		handleApiError( error );
	}
}

/**
 * Get authorized preview render payload.
 * @param id
 * @param signal
 */
export async function getDiagramPreview(
	id: number,
	signal?: AbortSignal
): Promise< DiagramPreviewPayload > {
	try {
		const path = `${ REST_NAMESPACE }/diagrams/${ id }/preview`;
		return await apiFetch< DiagramPreviewPayload >( { path, signal } );
	} catch ( error ) {
		handleApiError( error );
	}
}

/**
 * Duplicate a diagram as a new draft copy.
 * @param id
 * @param options
 */
export async function duplicateDiagram(
	id: number,
	options: { title?: string; keepTerms?: boolean } = {}
): Promise< DiagramDetail > {
	try {
		return await apiFetch< DiagramDetail >( {
			path: `${ REST_NAMESPACE }/diagrams/${ id }/duplicate`,
			method: 'POST',
			data: options,
		} );
	} catch ( error ) {
		handleApiError( error );
	}
}

/**
 * Execute a bulk operation on multiple diagram IDs.
 * @param data
 */
export async function bulkOperation(
	data: BulkOperationRequest
): Promise< BulkOperationResult > {
	try {
		return await apiFetch< BulkOperationResult >( {
			path: `${ REST_NAMESPACE }/diagrams/bulk`,
			method: 'POST',
			data,
		} );
	} catch ( error ) {
		handleApiError( error );
	}
}

/**
 * Get usage information for a diagram.
 * @param id
 */
export async function getDiagramUsage(
	id: number
): Promise< DiagramUsageResponse > {
	try {
		return await apiFetch< DiagramUsageResponse >( {
			path: `${ REST_NAMESPACE }/diagrams/${ id }/usage`,
		} );
	} catch ( error ) {
		handleApiError( error );
	}
}

/**
 * Get full settings schema and current section values.
 */
export async function getSettings(): Promise< SettingsResponse > {
	try {
		return await apiFetch< SettingsResponse >( {
			path: `${ REST_NAMESPACE }/settings`,
		} );
	} catch ( error ) {
		handleApiError( error );
	}
}

/**
 * Update a single settings section.
 * @param section
 * @param payload
 */
export async function updateSettingsSection(
	section: string,
	payload: Record< string, unknown >
): Promise< Record< string, unknown > > {
	try {
		return await apiFetch< Record< string, unknown > >( {
			path: `${ REST_NAMESPACE }/settings/${ section }`,
			method: 'PATCH',
			data: payload,
		} );
	} catch ( error ) {
		handleApiError( error );
	}
}
