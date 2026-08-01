/**
 * Bulk diagram actions hook.
 *
 * @package
 */

import { useCallback, useState } from '@wordpress/element';
import { bulkOperation } from '../api';
import type { BulkOperationType } from '../api/types';

interface BulkPayload {
	category_ids?: number[];
	tag_ids?: number[];
	status?: string;
}

interface BulkActionResult {
	succeeded: number;
	failed: number;
	failedIds: number[];
}

interface UseBulkActionsResult {
	isRunning: boolean;
	lastResult: BulkActionResult | null;
	runBulk: (
		ids: number[],
		operation: BulkOperationType,
		payload?: BulkPayload
	) => Promise< BulkActionResult >;
}

export function useBulkActions(): UseBulkActionsResult {
	const [ isRunning, setIsRunning ] = useState( false );
	const [ lastResult, setLastResult ] = useState< BulkActionResult | null >(
		null
	);

	const runBulk = useCallback(
		async (
			ids: number[],
			operation: BulkOperationType,
			payload: BulkPayload = {}
		): Promise< BulkActionResult > => {
			setIsRunning( true );
			try {
				const response = await bulkOperation( {
					ids,
					operation,
					payload,
				} );

				const failedIds = response.results
					.filter( ( item ) => ! item.ok )
					.map( ( item ) => item.id );

				const result: BulkActionResult = {
					succeeded: response.summary.succeeded,
					failed: response.summary.failed,
					failedIds,
				};

				setLastResult( result );
				return result;
			} finally {
				setIsRunning( false );
			}
		},
		[]
	);

	return { isRunning, lastResult, runBulk };
}
