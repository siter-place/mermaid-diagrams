/**
 * Settings admin entry point.
 *
 * @package
 */

import { createRoot } from '@wordpress/element';
import { App } from './App';

const mountNode = document.getElementById( 'mdm-settings-root' );

if ( mountNode ) {
	createRoot( mountNode ).render( <App /> );
}
