/**
 * Diagram preview panel hook with abortable fetch and cache.
 *
 * @package
 */

import { useCallback, useEffect, useRef, useState } from '@wordpress/element';
import { getDiagramPreview, getDiagramUsage } from '../api';
import type { DiagramPreviewPayload, DiagramUsageResponse } from '../api/types';
import { getErrorMessage } from '../utils/errors';

interface PreviewCacheEntry {
	payload: DiagramPreviewPayload;
	usage: DiagramUsageResponse | null;
}

interface UseDiagramPreviewResult {
	isOpen: boolean;
	diagramId: number | null;
	payload: DiagramPreviewPayload | null;
	usage: DiagramUsageResponse | null;
	status: 'idle' | 'loading' | 'ready' | 'error';
	error: string | null;
	openPreview: ( id: number ) => void;
	closePreview: () => void;
}

export function useDiagramPreview(): UseDiagramPreviewResult {
	const cacheRef = useRef< Map< number, PreviewCacheEntry > >( new Map() );
	const [ isOpen, setIsOpen ] = useState( false );
	const [ diagramId, setDiagramId ] = useState< number | null >( null );
	const [ payload, setPayload ] = useState< DiagramPreviewPayload | null >(
		null
	);
	const [ usage, setUsage ] = useState< DiagramUsageResponse | null >( null );
	const [ status, setStatus ] = useState<
		'idle' | 'loading' | 'ready' | 'error'
	>( 'idle' );
	const [ error, setError ] = useState< string | null >( null );

	const openPreview = useCallback( ( id: number ) => {
		setDiagramId( id );
		setIsOpen( true );
	}, [] );

	const closePreview = useCallback( () => {
		setIsOpen( false );
		setDiagramId( null );
		setStatus( 'idle' );
		setError( null );
	}, [] );

	useEffect( () => {
		if ( ! isOpen || diagramId === null ) {
			return;
		}

		const cached = cacheRef.current.get( diagramId );
		if ( cached ) {
			setPayload( cached.payload );
			setUsage( cached.usage );
			setStatus( 'ready' );
			return;
		}

		const controller = new AbortController();

		async function load() {
			setStatus( 'loading' );
			setError( null );

			try {
				const [ previewPayload, usagePayload ] = await Promise.all( [
					getDiagramPreview( diagramId as number, controller.signal ),
					getDiagramUsage( diagramId as number ).catch( () => null ),
				] );

				if ( controller.signal.aborted ) {
					return;
				}

				cacheRef.current.set( diagramId as number, {
					payload: previewPayload,
					usage: usagePayload,
				} );

				setPayload( previewPayload );
				setUsage( usagePayload );
				setStatus( 'ready' );
			} catch ( loadError ) {
				if ( controller.signal.aborted ) {
					return;
				}
				setPayload( null );
				setUsage( null );
				setError( getErrorMessage( loadError, 'Unable to load preview.' ) );
				setStatus( 'error' );
			}
		}

		void load();

		return () => controller.abort();
	}, [ isOpen, diagramId ] );

	return {
		isOpen,
		diagramId,
		payload,
		usage,
		status,
		error,
		openPreview,
		closePreview,
	};
}
