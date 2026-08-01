/**
 * Diagram library admin entry point.
 *
 * @package
 */

import { createRoot } from '@wordpress/element';
import { App } from './App';

const mountNode = document.getElementById( 'mdm-diagram-library-root' );

if ( mountNode ) {
	createRoot( mountNode ).render( <App /> );
}
