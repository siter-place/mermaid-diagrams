/**
 * Shared API Contract Types for Mermaid Diagrams.
 *
 * @package
 */

export interface TermRef {
	id: number;
	name: string;
	slug: string;
}

export interface AuthorRef {
	id: number;
	name: string;
}

export interface CanFlags {
	edit: boolean;
	delete: boolean;
	publish: boolean;
}

export interface PreviewRef {
	state: 'available' | 'unavailable' | 'pending';
	url: string;
}

export interface RenderConfig {
	theme?: string;
	defaultToolbar?: boolean;
	allowSourceDownload?: boolean;
	allowSvgDownload?: boolean;
	height?: number;
	width?: string;
	[ key: string ]: unknown;
}

export interface ValidationReceipt {
	sourceHash: string;
	mermaidVersion: string;
	diagramType: string;
	validatedAt: string;
	profile: 'browser' | 'worker';
}

export interface DiagramSummary {
	id: number;
	title: string;
	description: string;
	type: string;
	status: 'draft' | 'pending' | 'publish' | 'private' | 'trash' | string;
	categories: TermRef[];
	tags: TermRef[];
	author: AuthorRef;
	modifiedGmt: string;
	sourceHash: string;
	can: CanFlags;
	preview: PreviewRef;
	usageCount: number;
}

export interface DiagramDetail extends DiagramSummary {
	source: string;
	renderConfig: RenderConfig;
	versionToken: string;
	validationReceipt?: ValidationReceipt | null;
	createdAtGmt: string;
	lastEditor?: AuthorRef | null;
}

export interface DiagramPreviewPayload {
	id: number;
	title: string;
	description: string;
	type: string;
	status: string;
	source?: string;
	renderConfig: RenderConfig;
	validation: {
		state: string;
		receipt?: ValidationReceipt | null;
	};
	thumbnail: {
		state: 'available' | 'missing';
		attachmentId?: number | null;
	};
	can: CanFlags;
}

export interface PaginationMeta {
	page: number;
	perPage: number;
	totalItems: number;
	totalPages: number;
}

export interface DiagramSearchQuery {
	search?: string;
	category?: number[];
	tag?: number[];
	type?: string[];
	status?: string[];
	author?: number[];
	page?: number;
	per_page?: number;
	orderby?: 'modified' | 'title';
	order?: 'ASC' | 'DESC' | 'asc' | 'desc';
	view?: 'summary' | 'detail' | 'selector';
}

export interface DiagramSearchResponse {
	items: DiagramSummary[] | DiagramDetail[];
	pagination: PaginationMeta;
	facets: {
		types: string[];
		statuses: string[];
	};
}

export interface CreateDiagramRequest {
	title: string;
	source: string;
	description?: string;
	status?: string;
	categoryIds?: number[];
	tagIds?: number[];
	renderConfig?: RenderConfig;
	idempotencyKey?: string;
	validation?: ValidationReceipt;
}

export interface UpdateDiagramRequest {
	title?: string;
	source?: string;
	description?: string;
	status?: string;
	categoryIds?: number[];
	tagIds?: number[];
	renderConfig?: RenderConfig;
	expectedVersion?: string;
}

export type BulkOperationType =
	| 'add_categories'
	| 'remove_categories'
	| 'replace_categories'
	| 'add_tags'
	| 'remove_tags'
	| 'set_status'
	| 'trash'
	| 'restore';

export interface BulkOperationRequest {
	ids: number[];
	operation: BulkOperationType;
	payload?: {
		category_ids?: number[];
		tag_ids?: number[];
		status?: string;
		[ key: string ]: unknown;
	};
}

export interface BulkItemResult {
	id: number;
	ok: boolean;
	error?: {
		code: string;
		message: string;
	};
}

export interface BulkOperationResult {
	results: BulkItemResult[];
	summary: {
		requested: number;
		succeeded: number;
		failed: number;
	};
}

export interface DiagramUsageResponse {
	diagramId: number;
	usageCount: number;
	references: Array< {
		postId: number;
		title: string;
		status: string;
		editUrl?: string;
	} >;
}

export interface SettingsSchemaSection {
	id: string;
	title: string;
}

export interface SettingsResponse {
	schema: {
		title: string;
		description: string;
		sections: SettingsSchemaSection[];
	};
	values: Record< string, Record< string, unknown > >;
	capabilities: {
		manageSettings: boolean;
	};
	runtime: {
		pluginVersion: string;
		mermaidVersion: string;
		phpVersion: string;
		wpVersion: string;
	};
}

export interface ErrorEnvelope {
	code: string;
	message: string;
	data?: {
		status?: number;
		expectedVersion?: string;
		currentDiagram?: DiagramDetail;
		[ key: string ]: unknown;
	};
}

export class EditConflictError extends Error {
	public readonly code = 'mdm_edit_conflict';
	public readonly status = 409;

	constructor(
		message: string,
		public readonly expectedVersion?: string,
		public readonly currentDiagram?: DiagramDetail
	) {
		super( message );
		this.name = 'EditConflictError';
	}
}
