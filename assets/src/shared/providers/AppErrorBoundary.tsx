/**
 * Application error boundary.
 *
 * @package
 */

import { Component, type ReactNode } from '@wordpress/element';
import { MdmErrorState } from '../components/MdmErrorState';

interface ErrorBoundaryProps {
	children: ReactNode;
}

interface ErrorBoundaryState {
	error: Error | null;
}

export class AppErrorBoundary extends Component<
	ErrorBoundaryProps,
	ErrorBoundaryState
> {
	constructor( props: ErrorBoundaryProps ) {
		super( props );
		this.state = { error: null };
	}

	static getDerivedStateFromError( error: Error ): ErrorBoundaryState {
		return { error };
	}

	render() {
		if ( this.state.error ) {
			return (
				<MdmErrorState
					title="Application error"
					message={ this.state.error.message }
					onRetry={ () => this.setState( { error: null } ) }
				/>
			);
		}

		return this.props.children;
	}
}
