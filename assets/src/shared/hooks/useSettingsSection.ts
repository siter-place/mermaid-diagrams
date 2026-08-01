/**
 * Settings section form state hook.
 *
 * @package
 */

import { useCallback, useEffect, useMemo, useState } from '@wordpress/element';
import { getSettings, updateSettingsSection } from '../api';
import type { SettingsResponse } from '../api/types';
import { getErrorMessage } from '../utils/errors';
import { useNotices } from '../providers/NoticesProvider';
import { useBootstrap } from '../providers/BootstrapProvider';

interface UseSettingsSectionResult {
	settings: SettingsResponse | null;
	sectionValues: Record< string, unknown >;
	status: 'loading' | 'ready' | 'error';
	isDirty: boolean;
	isSaving: boolean;
	error: string | null;
	setField: ( key: string, value: unknown ) => void;
	saveSection: () => Promise< void >;
	reload: () => void;
}

export function useSettingsSection(
	sectionId: string
): UseSettingsSectionResult {
	const bootstrap = useBootstrap();
	const { createNotice } = useNotices();
	const [ settings, setSettings ] = useState< SettingsResponse | null >(
		null
	);
	const [ sectionValues, setSectionValues ] = useState<
		Record< string, unknown >
	>( {} );
	const [ baselineValues, setBaselineValues ] = useState<
		Record< string, unknown >
	>( {} );
	const [ status, setStatus ] = useState< 'loading' | 'ready' | 'error' >(
		'loading'
	);
	const [ error, setError ] = useState< string | null >( null );
	const [ isSaving, setIsSaving ] = useState( false );
	const [ reloadToken, setReloadToken ] = useState( 0 );

	const reload = useCallback( () => {
		setReloadToken( ( current ) => current + 1 );
	}, [] );

	useEffect( () => {
		async function load() {
			setStatus( 'loading' );
			setError( null );

			try {
				const response = await getSettings();
				setSettings( response );
				const values = response.values[ sectionId ] ?? {};
				setSectionValues( values );
				setBaselineValues( values );
				setStatus( 'ready' );
			} catch ( loadError ) {
				setError(
					getErrorMessage(
						loadError,
						bootstrap.i18n.settingsSaveError
					)
				);
				setStatus( 'error' );
			}
		}

		void load();
	}, [ sectionId, reloadToken, bootstrap.i18n.settingsSaveError ] );

	const isDirty = useMemo(
		() =>
			JSON.stringify( sectionValues ) !==
			JSON.stringify( baselineValues ),
		[ sectionValues, baselineValues ]
	);

	const setField = useCallback( ( key: string, value: unknown ) => {
		setSectionValues( ( current ) => ( {
			...current,
			[ key ]: value,
		} ) );
	}, [] );

	const saveSection = useCallback( async () => {
		setIsSaving( true );

		try {
			const normalized = await updateSettingsSection(
				sectionId,
				sectionValues
			);
			setSectionValues( normalized );
			setBaselineValues( normalized );
			setSettings( ( current ) =>
				current
					? {
							...current,
							values: {
								...current.values,
								[ sectionId ]: normalized,
							},
					  }
					: current
			);
			createNotice( 'success', bootstrap.i18n.settingsSaved );
		} catch ( saveError ) {
			createNotice(
				'error',
				getErrorMessage( saveError, bootstrap.i18n.settingsSaveError )
			);
		} finally {
			setIsSaving( false );
		}
	}, [
		sectionId,
		sectionValues,
		createNotice,
		bootstrap.i18n.settingsSaved,
		bootstrap.i18n.settingsSaveError,
	] );

	return {
		settings,
		sectionValues,
		status,
		isDirty,
		isSaving,
		error,
		setField,
		saveSection,
		reload,
	};
}
