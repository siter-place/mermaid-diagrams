/**
 * Quick-create diagram hook with client-side validation.
 *
 * @package
 */

import { useCallback, useState } from '@wordpress/element';
import { createValidationReceipt } from '@mdm/mermaid-runtime/receipt';
import { createDiagram } from '../api';
import type { DiagramDetail } from '../api/types';
import { getErrorMessage } from '../utils/errors';

interface QuickCreateForm {
	title: string;
	source: string;
}

export type { QuickCreateForm };

interface UseQuickCreateResult {
	isOpen: boolean;
	isSaving: boolean;
	form: QuickCreateForm;
	validationError: string | null;
	open: () => void;
	close: () => void;
	setField: ( field: keyof QuickCreateForm, value: string ) => void;
	save: () => Promise< DiagramDetail | null >;
}

export function useQuickCreate(): UseQuickCreateResult {
	const [ isOpen, setIsOpen ] = useState( false );
	const [ isSaving, setIsSaving ] = useState( false );
	const [ validationError, setValidationError ] = useState< string | null >(
		null
	);
	const [ form, setForm ] = useState< QuickCreateForm >( {
		title: '',
		source: 'flowchart TD\n  A[Start] --> B[Finish]',
	} );

	const open = useCallback( () => {
		setValidationError( null );
		setIsOpen( true );
	}, [] );

	const close = useCallback( () => {
		setIsOpen( false );
		setValidationError( null );
	}, [] );

	const setField = useCallback(
		( field: keyof QuickCreateForm, value: string ) => {
			setForm( ( current ) => ( { ...current, [ field ]: value } ) );
			setValidationError( null );
		},
		[]
	);

	const save = useCallback( async (): Promise< DiagramDetail | null > => {
		if ( ! form.title.trim() ) {
			setValidationError( 'Title is required.' );
			return null;
		}

		setIsSaving( true );
		setValidationError( null );

		try {
			const receipt = await createValidationReceipt( form.source, 'browser' );
			const idempotencyKey = `quick-create-${ Date.now() }-${ Math.random()
				.toString( 36 )
				.slice( 2 ) }`;

			const created = await createDiagram(
				{
					title: form.title.trim(),
					source: form.source,
					status: 'draft',
					validation: receipt,
				},
				idempotencyKey
			);

			setForm( {
				title: '',
				source: 'flowchart TD\n  A[Start] --> B[Finish]',
			} );
			setIsOpen( false );
			return created;
		} catch ( error ) {
			setValidationError(
				getErrorMessage( error, 'Unable to save diagram.' )
			);
			return null;
		} finally {
			setIsSaving( false );
		}
	}, [ form ] );

	return {
		isOpen,
		isSaving,
		form,
		validationError,
		open,
		close,
		setField,
		save,
	};
}
