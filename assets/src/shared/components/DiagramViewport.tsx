/**
 * Shared Mermaid diagram viewport for admin preview surfaces.
 *
 * @package
 */

import { useEffect, useRef, useState } from '@wordpress/element';
import { renderMermaid } from '@mdm/mermaid-runtime/render';
import type { RenderConfig } from '../api/types';

export type DiagramViewportStatus = 'idle' | 'loading' | 'ready' | 'invalid' | 'error';

const DEFAULT_RENDER_CONFIG: RenderConfig = {};

interface DiagramViewportProps {
	source: string;
	title?: string;
	description?: string;
	renderConfig?: RenderConfig;
	className?: string;
	'data-testid'?: string;
}

export function DiagramViewport( {
	source,
	title = '',
	description = '',
	renderConfig = DEFAULT_RENDER_CONFIG,
	className = '',
	'data-testid': testId = 'mdm-diagram-viewport',
}: DiagramViewportProps ) {
	const containerRef = useRef< HTMLDivElement | null >( null );
	const [ status, setStatus ] = useState< DiagramViewportStatus >( 'idle' );
	const [ errorMessage, setErrorMessage ] = useState< string | null >( null );

	useEffect( () => {
		const container = containerRef.current;
		if ( ! container || ! source.trim() ) {
			setStatus( 'invalid' );
			return;
		}

		const controller = new AbortController();
		const renderToken = String( Date.now() );

		async function render() {
			setStatus( 'loading' );
			setErrorMessage( null );

			try {
				const result = await renderMermaid(
					undefined,
					source,
					renderConfig as Record< string, unknown >,
					renderToken,
					{ title, description }
				);

				if ( controller.signal.aborted ) {
					return;
				}

				container.innerHTML = result.svg;
				setStatus( 'ready' );
			} catch ( error ) {
				if ( controller.signal.aborted ) {
					return;
				}

				// eslint-disable-next-line no-console
				console.error( 'DiagramViewport render error:', error );

				const message =
					error instanceof Error ? error.message : 'Render failed';
				if ( message.includes( 'invalid' ) ) {
					setStatus( 'invalid' );
				} else {
					setStatus( 'error' );
				}
				setErrorMessage( message );
				container.innerHTML = '';
			}
		}

		void render();

		return () => {
			controller.abort();
			if ( container ) {
				container.innerHTML = '';
			}
		};
	}, [ source, title, description, renderConfig ] );

	return (
		<div
			className={ `mdm-diagram-viewport ${ className }`.trim() }
			data-testid={ testId }
			data-status={ status }
		>
			{ status === 'loading' ? (
				<p className="mdm-diagram-viewport__status">{ 'Loading preview…' }</p>
			) : null }
			{ status === 'invalid' ? (
				<p className="mdm-diagram-viewport__status mdm-diagram-viewport__status--invalid">
					{ 'Invalid Mermaid source.' }
				</p>
			) : null }
			{ status === 'error' ? (
				<p className="mdm-diagram-viewport__status mdm-diagram-viewport__status--error">
					{ errorMessage }
				</p>
			) : null }
			<div
				ref={ containerRef }
				className="mdm-diagram-viewport__canvas"
				role="img"
				aria-label={ title || 'Diagram preview' }
			/>
		</div>
	);
}
