/**
 * Library filter, search, view layout switcher, and appearance settings controls.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { useCallback, useState } from '@wordpress/element';
import {
	Button,
	CheckboxControl,
	Popover,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import {
	chevronDown,
	chevronUp,
	closeSmall,
	cog,
	funnel,
	grid,
	table,
	undo,
} from '@wordpress/icons';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';
import { useTaxonomyTerms } from '../../../shared/hooks/useTaxonomyTerms';
import type { LibraryQueryState } from '../../../shared/state/url-query';

const COLLAPSE_KEY = 'mdm_filter_collapsed';

export type TableDensity = 'comfortable' | 'balanced' | 'compact';

export interface VisibleProperties {
	title: boolean;
	categories: boolean;
	tags: boolean;
	status: boolean;
	author: boolean;
	modified: boolean;
	usage: boolean;
	actions: boolean;
}

interface FilterBarProps {
	query: LibraryQueryState;
	viewMode: 'table' | 'grid';
	density: TableDensity;
	visibleProperties: VisibleProperties;
	onChange: ( patch: Partial< LibraryQueryState > ) => void;
	onReset: () => void;
	onViewModeChange: ( mode: 'table' | 'grid' ) => void;
	onDensityChange: ( density: TableDensity ) => void;
	onPropertyToggle: ( key: keyof VisibleProperties ) => void;
}

const STATUS_OPTIONS = [
	{ value: '', label: 'All statuses' },
	{ value: 'publish', label: 'Published' },
	{ value: 'draft', label: 'Draft' },
	{ value: 'pending', label: 'Pending' },
	{ value: 'private', label: 'Private' },
	{ value: 'trash', label: 'Trash' },
];

function getStoredCollapsed(): boolean {
	try {
		return localStorage.getItem( COLLAPSE_KEY ) === '1';
	} catch {
		return false;
	}
}

function setStoredCollapsed( collapsed: boolean ) {
	try {
		localStorage.setItem( COLLAPSE_KEY, collapsed ? '1' : '0' );
	} catch {
		// Silent fail for private browsing.
	}
}

interface ActiveFilter {
	key: string;
	label: string;
	onRemove: () => void;
}

export function FilterBar( {
	query,
	viewMode,
	density,
	visibleProperties,
	onChange,
	onReset,
	onViewModeChange,
	onDensityChange,
	onPropertyToggle,
}: FilterBarProps ) {
	const { i18n, diagramTypes = [] } = useBootstrap();
	const [ collapsed, setCollapsed ] = useState( getStoredCollapsed );
	const [ isPopoverOpen, setIsPopoverOpen ] = useState( false );
	const { terms: categories } = useTaxonomyTerms( {
		taxonomy: 'mdm-diagram-categories',
	} );
	const { terms: tags } = useTaxonomyTerms( {
		taxonomy: 'mdm-diagram-tags',
	} );

	const toggleCollapse = useCallback( () => {
		setCollapsed( ( prev ) => {
			const next = ! prev;
			setStoredCollapsed( next );
			return next;
		} );
	}, [] );

	const typeOptions = [
		{ value: '', label: i18n.filterType || 'Type' },
		...diagramTypes.map( ( type ) => ( {
			value: type.value,
			label: type.label,
		} ) ),
	];

	const categoryOptions = [
		{ value: '', label: i18n.filterCategory || 'Category' },
		...categories.map( ( term ) => ( {
			value: String( term.id ),
			label: term.name,
		} ) ),
	];

	const tagOptions = [
		{ value: '', label: i18n.filterTag || 'Tag' },
		...tags.map( ( term ) => ( {
			value: String( term.id ),
			label: term.name,
		} ) ),
	];

	const activeFilters: ActiveFilter[] = [];
	if ( query.search ) {
		activeFilters.push( {
			key: 'search',
			label: `Search: ${ query.search }`,
			onRemove: () => onChange( { search: undefined } ),
		} );
	}
	if ( query.category?.[ 0 ] ) {
		const cat = categories.find( ( t ) => t.id === query.category![ 0 ] );
		activeFilters.push( {
			key: 'category',
			label: `Category: ${ cat?.name || query.category[ 0 ] }`,
			onRemove: () => onChange( { category: undefined } ),
		} );
	}
	if ( query.tag?.[ 0 ] ) {
		const tag = tags.find( ( t ) => t.id === query.tag![ 0 ] );
		activeFilters.push( {
			key: 'tag',
			label: `Tag: ${ tag?.name || query.tag[ 0 ] }`,
			onRemove: () => onChange( { tag: undefined } ),
		} );
	}
	if ( query.type?.[ 0 ] ) {
		activeFilters.push( {
			key: 'type',
			label: `Type: ${ query.type[ 0 ] }`,
			onRemove: () => onChange( { type: undefined } ),
		} );
	}
	if ( query.status?.[ 0 ] ) {
		activeFilters.push( {
			key: 'status',
			label: `Status: ${ query.status[ 0 ] }`,
			onRemove: () => onChange( { status: undefined } ),
		} );
	}

	const hasActiveFilters = activeFilters.length > 0;

	return (
		<div className="mdm-filter-bar" data-testid="mdm-filter-bar">
			<div className="mdm-filter-bar__header">
				<div className="mdm-filter-bar__search">
					<TextControl
						type="search"
						value={ query.search ?? '' }
						onChange={ ( value ) => onChange( { search: value || undefined } ) }
						placeholder={ i18n.searchPlaceholder || 'Search diagrams' }
						aria-label={ i18n.searchPlaceholder || 'Search diagrams' }
						__nextHasNoMarginBottom
					/>
				</div>

				<Button
					variant="tertiary"
					icon={ collapsed ? chevronDown : chevronUp }
					onClick={ toggleCollapse }
					aria-expanded={ ! collapsed }
					aria-label={ collapsed ? 'Expand filters' : 'Collapse filters' }
					className="mdm-filter-bar__toggle"
					data-testid="mdm-filter-toggle"
				>
					<span className="mdm-filter-bar__toggle-icon">
						{ funnel }
					</span>
					{ i18n.filters || 'Filters' }
				</Button>

				{ hasActiveFilters ? (
					<Button
						variant="tertiary"
						icon={ undo }
						onClick={ onReset }
						className="mdm-filter-bar__reset"
						isDestructive
					>
						{ i18n.resetFilters || 'Reset all' }
					</Button>
				) : null }

				<div className="mdm-filter-bar__right-controls">
					<div className="mdm-view-switcher">
						<Button
							icon={ table }
							label="Table view"
							isPressed={ viewMode === 'table' }
							onClick={ () => onViewModeChange( 'table' ) }
							size="compact"
							data-testid="mdm-view-table"
						/>
						<Button
							icon={ grid }
							label="Grid view"
							isPressed={ viewMode === 'grid' }
							onClick={ () => onViewModeChange( 'grid' ) }
							size="compact"
							data-testid="mdm-view-grid"
						/>
					</div>

					<Button
						icon={ cog }
						label="View options"
						isPressed={ isPopoverOpen }
						onClick={ () => setIsPopoverOpen( ! isPopoverOpen ) }
						size="compact"
						data-testid="mdm-view-options-toggle"
					/>

					{ isPopoverOpen ? (
						<Popover
							position="bottom left"
							onClose={ () => setIsPopoverOpen( false ) }
							className="mdm-appearance-popover"
							data-testid="mdm-view-options-popover"
						>
							<div className="mdm-appearance-popover__content">
								<div className="mdm-appearance-popover__header">
									<h3>Appearance</h3>
									<Button
										variant="tertiary"
										size="compact"
										onClick={ () => {
											onDensityChange( 'balanced' );
											onChange( { orderby: 'modified', order: 'DESC', perPage: 20 } );
										} }
									>
										Reset view
									</Button>
								</div>

								<div className="mdm-appearance-popover__section">
									<label className="mdm-appearance-label">SORT BY</label>
									<div className="mdm-appearance-sort-row">
										<SelectControl
											value={ query.orderby ?? 'modified' }
											options={ [
												{ value: 'modified', label: 'Modified' },
												{ value: 'title', label: 'Title' },
											] }
											onChange={ ( val ) =>
												onChange( {
													orderby: val as 'modified' | 'title',
												} )
											}
											hideLabelFromVision
											__nextHasNoMarginBottom
										/>
										<div className="mdm-order-toggle">
											<Button
												size="compact"
												isPressed={ query.order === 'ASC' }
												onClick={ () => onChange( { order: 'ASC' } ) }
												label="Ascending"
											>
												↑
											</Button>
											<Button
												size="compact"
												isPressed={ query.order !== 'ASC' }
												onClick={ () => onChange( { order: 'DESC' } ) }
												label="Descending"
											>
												↓
											</Button>
										</div>
									</div>
								</div>

								<div className="mdm-appearance-popover__section">
									<label className="mdm-appearance-label">DENSITY</label>
									<div className="mdm-density-segmented">
										<Button
											size="compact"
											isPressed={ density === 'comfortable' }
											onClick={ () => onDensityChange( 'comfortable' ) }
										>
											Comfortable
										</Button>
										<Button
											size="compact"
											isPressed={ density === 'balanced' }
											onClick={ () => onDensityChange( 'balanced' ) }
										>
											Balanced
										</Button>
										<Button
											size="compact"
											isPressed={ density === 'compact' }
											onClick={ () => onDensityChange( 'compact' ) }
										>
											Compact
										</Button>
									</div>
								</div>

								<div className="mdm-appearance-popover__section">
									<label className="mdm-appearance-label">ITEMS PER PAGE</label>
									<div className="mdm-per-page-segmented">
										{ [ 10, 20, 50, 100 ].map( ( num ) => (
											<Button
												key={ num }
												size="compact"
												isPressed={ ( query.perPage ?? 20 ) === num }
												onClick={ () => onChange( { perPage: num, page: 1 } ) }
											>
												{ num }
											</Button>
										) ) }
									</div>
								</div>

								<div className="mdm-appearance-popover__section">
									<label className="mdm-appearance-label">PROPERTIES</label>
									<div className="mdm-properties-list">
										<CheckboxControl
											label="Categories"
											checked={ visibleProperties.categories }
											onChange={ () => onPropertyToggle( 'categories' ) }
											__nextHasNoMarginBottom
										/>
										<CheckboxControl
											label="Tags"
											checked={ visibleProperties.tags }
											onChange={ () => onPropertyToggle( 'tags' ) }
											__nextHasNoMarginBottom
										/>
										<CheckboxControl
											label="Status"
											checked={ visibleProperties.status }
											onChange={ () => onPropertyToggle( 'status' ) }
											__nextHasNoMarginBottom
										/>
										<CheckboxControl
											label="Author"
											checked={ visibleProperties.author }
											onChange={ () => onPropertyToggle( 'author' ) }
											__nextHasNoMarginBottom
										/>
										<CheckboxControl
											label="Modified"
											checked={ visibleProperties.modified }
											onChange={ () => onPropertyToggle( 'modified' ) }
											__nextHasNoMarginBottom
										/>
										<CheckboxControl
											label="Usage"
											checked={ visibleProperties.usage }
											onChange={ () => onPropertyToggle( 'usage' ) }
											__nextHasNoMarginBottom
										/>
									</div>
								</div>
							</div>
						</Popover>
					) : null }
				</div>
			</div>

			{ ! collapsed ? (
				<div className="mdm-filter-bar__controls" data-testid="mdm-filter-controls">
					<SelectControl
						label={ i18n.filterCategory || 'Category' }
						hideLabelFromVision
						value={ String( query.category?.[ 0 ] ?? '' ) }
						options={ categoryOptions }
						onChange={ ( value ) =>
							onChange( {
								category: value ? [ Number( value ) ] : undefined,
							} )
						}
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={ i18n.filterTag || 'Tag' }
						hideLabelFromVision
						value={ String( query.tag?.[ 0 ] ?? '' ) }
						options={ tagOptions }
						onChange={ ( value ) =>
							onChange( { tag: value ? [ Number( value ) ] : undefined } )
						}
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={ i18n.filterType || 'Type' }
						hideLabelFromVision
						value={ query.type?.[ 0 ] ?? '' }
						options={ typeOptions }
						onChange={ ( value ) =>
							onChange( { type: value ? [ value ] : undefined } )
						}
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={ i18n.filterStatus || 'Status' }
						hideLabelFromVision
						value={ query.status?.[ 0 ] ?? '' }
						options={ STATUS_OPTIONS }
						onChange={ ( value ) =>
							onChange( { status: value ? [ value ] : undefined } )
						}
						__nextHasNoMarginBottom
					/>
				</div>
			) : null }

			{ hasActiveFilters ? (
				<div className="mdm-filter-bar__pills" data-testid="mdm-active-filters">
					{ activeFilters.map( ( filter ) => (
						<span key={ filter.key } className="mdm-filter-pill">
							<span className="mdm-filter-pill__label">{ filter.label }</span>
							<Button
								className="mdm-filter-pill__remove"
								icon={ closeSmall }
								label={ `Remove ${ filter.label }` }
								onClick={ filter.onRemove }
								size="small"
							/>
						</span>
					) ) }
				</div>
			) : null }
		</div>
	);
}
