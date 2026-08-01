/**
 * JS Unit Tests for REST API Client.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { EditConflictError } from '../../assets/src/shared/api/types';

describe('Shared REST API Contracts & Client', () => {
  test('EditConflictError correctly initializes code and status', () => {
    const error = new EditConflictError(
      'The diagram was modified by another user.',
      'v1-token',
      {
        id: 123,
        title: 'Conflict Diagram',
        source: 'flowchart LR; A-->B;',
        description: '',
        type: 'flowchart',
        status: 'draft',
        categories: [],
        tags: [],
        author: { id: 1, name: 'Admin' },
        modifiedGmt: '2026-08-01T20:00:00Z',
        sourceHash: 'hash123',
        can: { edit: true, delete: true, publish: true },
        preview: { state: 'available', url: '' },
        usageCount: 0,
        renderConfig: {},
        versionToken: 'v2-token',
        createdAtGmt: '2026-08-01T20:00:00Z',
      }
    );

    expect(error.name).toBe('EditConflictError');
    expect(error.code).toBe('mdm_edit_conflict');
    expect(error.status).toBe(409);
    expect(error.expectedVersion).toBe('v1-token');
    expect(error.currentDiagram?.id).toBe(123);
    expect(error.currentDiagram?.versionToken).toBe('v2-token');
  });
});
