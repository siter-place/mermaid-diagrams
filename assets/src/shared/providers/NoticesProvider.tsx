/**
 * Notices context for admin apps.
 *
 * @package
 */

import {
	createContext,
	useCallback,
	useContext,
	useMemo,
	useState,
	type ReactNode,
} from '@wordpress/element';
import { SnackbarList } from '@wordpress/components';

export type NoticeStatus = 'success' | 'error' | 'info';

export interface AppNotice {
	id: string;
	status: NoticeStatus;
	content: string;
}

interface NoticesContextValue {
	notices: AppNotice[];
	createNotice: ( status: NoticeStatus, content: string ) => void;
	removeNotice: ( id: string ) => void;
}

const NoticesContext = createContext< NoticesContextValue | null >( null );

interface NoticesProviderProps {
	children: ReactNode;
}

export function NoticesProvider( { children }: NoticesProviderProps ) {
	const [ notices, setNotices ] = useState< AppNotice[] >( [] );

	const createNotice = useCallback(
		( status: NoticeStatus, content: string ) => {
			setNotices( ( current ) => [
				...current,
				{
					id: `${ Date.now() }-${ Math.random()
						.toString( 36 )
						.slice( 2 ) }`,
					status,
					content,
				},
			] );
		},
		[]
	);

	const removeNotice = useCallback( ( id: string ) => {
		setNotices( ( current ) =>
			current.filter( ( notice ) => notice.id !== id )
		);
	}, [] );

	const value = useMemo(
		() => ( {
			notices,
			createNotice,
			removeNotice,
		} ),
		[ notices, createNotice, removeNotice ]
	);

	return (
		<NoticesContext.Provider value={ value }>
			{ children }
			<div data-testid="mdm-notices">
				<SnackbarList
					notices={ notices.map( ( notice ) => ( {
						id: notice.id,
						content: notice.content,
						status: notice.status,
					} ) ) }
					onRemove={ removeNotice }
				/>
			</div>
		</NoticesContext.Provider>
	);
}

export function useNotices(): NoticesContextValue {
	const context = useContext( NoticesContext );

	if ( ! context ) {
		throw new Error( 'useNotices must be used within NoticesProvider.' );
	}

	return context;
}
