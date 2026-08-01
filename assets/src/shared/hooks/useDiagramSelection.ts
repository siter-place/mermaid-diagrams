/**
 * Diagram list selection state hook.
 *
 * @package
 */

import { useCallback, useMemo, useState } from '@wordpress/element';
import type { DiagramSummary } from '../api/types';

interface UseDiagramSelectionResult {
	selectedIds: Set< number >;
	isSelected: ( id: number ) => boolean;
	toggleRow: ( id: number ) => void;
	togglePage: ( items: DiagramSummary[] ) => void;
	isPageSelected: ( items: DiagramSummary[] ) => boolean;
	clearSelection: () => void;
	setSelectedIds: ( ids: number[] ) => void;
	selectedCount: number;
}

export function useDiagramSelection(): UseDiagramSelectionResult {
	const [ selectedIds, setSelectedIdsState ] = useState< Set< number > >(
		new Set()
	);

	const toggleRow = useCallback( ( id: number ) => {
		setSelectedIdsState( ( current ) => {
			const next = new Set( current );
			if ( next.has( id ) ) {
				next.delete( id );
			} else {
				next.add( id );
			}
			return next;
		} );
	}, [] );

	const togglePage = useCallback( ( items: DiagramSummary[] ) => {
		setSelectedIdsState( ( current ) => {
			const pageIds = items.map( ( item ) => item.id );
			const allSelected =
				pageIds.length > 0 && pageIds.every( ( id ) => current.has( id ) );
			const next = new Set( current );
			if ( allSelected ) {
				pageIds.forEach( ( id ) => next.delete( id ) );
			} else {
				pageIds.forEach( ( id ) => next.add( id ) );
			}
			return next;
		} );
	}, [] );

	const isSelected = useCallback(
		( id: number ) => selectedIds.has( id ),
		[ selectedIds ]
	);

	const isPageSelected = useCallback(
		( items: DiagramSummary[] ) => {
			if ( items.length === 0 ) {
				return false;
			}
			return items.every( ( item ) => selectedIds.has( item.id ) );
		},
		[ selectedIds ]
	);

	const clearSelection = useCallback( () => {
		setSelectedIdsState( new Set() );
	}, [] );

	const setSelectedIds = useCallback( ( ids: number[] ) => {
		setSelectedIdsState( new Set( ids ) );
	}, [] );

	const selectedCount = useMemo( () => selectedIds.size, [ selectedIds ] );

	return {
		selectedIds,
		isSelected,
		toggleRow,
		togglePage,
		isPageSelected,
		clearSelection,
		setSelectedIds,
		selectedCount,
	};
}
