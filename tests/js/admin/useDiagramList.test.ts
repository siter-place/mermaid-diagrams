/**
 * useDiagramList hook tests.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { renderHook, waitFor } from '@testing-library/react';
import { createElement, type ReactNode } from '@wordpress/element';
import { useDiagramList } from '../../../assets/src/shared/hooks/useDiagramList';
import { BootstrapProvider } from '../../../assets/src/shared/providers/BootstrapProvider';
import type { AdminBootstrap } from '../../../assets/src/shared/types/bootstrap';

jest.mock('../../../assets/src/shared/api/client', () => ({
  searchDiagrams: jest.fn(),
}));

import { searchDiagrams } from '../../../assets/src/shared/api/client';

const bootstrap: AdminBootstrap = {
  screen: 'library',
  restRoot: 'http://example.test/wp-json/mdm/v1',
  nonce: 'test-nonce',
  locale: 'en_US',
  capabilities: {
    editDiagrams: true,
    manageSettings: true,
  },
  routes: {
    library: 'http://example.test/wp-admin/admin.php?page=mdm-diagrams',
    settings: 'http://example.test/wp-admin/admin.php?page=mdm-settings',
    editorNew: 'http://example.test/wp-admin/admin.php?page=mdm-diagram-editor&action=new',
  },
  defaults: {
    perPage: 20,
    orderby: 'modified',
    order: 'DESC',
  },
  i18n: {
    errorTitle: 'Unable to load diagrams',
    retry: 'Try again',
  },
};

function wrapper({ children }: { children: ReactNode }) {
  return createElement(BootstrapProvider, { value: bootstrap }, children);
}

describe('useDiagramList', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    window.history.replaceState({}, '', '/wp-admin/admin.php?page=mdm-diagrams');
  });

  it('returns ready state with items', async () => {
    (searchDiagrams as jest.Mock).mockResolvedValue({
      items: [{ id: 1, title: 'Flow A' }],
      pagination: { page: 1, perPage: 20, totalItems: 1, totalPages: 1 },
      facets: { types: [], statuses: [] },
    });

    const { result } = renderHook(() => useDiagramList(), { wrapper });

    await waitFor(() => {
      expect(result.current.status).toBe('ready');
    });

    expect(result.current.data?.items).toHaveLength(1);
  });

  it('returns empty state when no items are found', async () => {
    (searchDiagrams as jest.Mock).mockResolvedValue({
      items: [],
      pagination: { page: 1, perPage: 20, totalItems: 0, totalPages: 0 },
      facets: { types: [], statuses: [] },
    });

    const { result } = renderHook(() => useDiagramList(), { wrapper });

    await waitFor(() => {
      expect(result.current.status).toBe('empty');
    });
  });

  it('returns error state when search fails', async () => {
    (searchDiagrams as jest.Mock).mockRejectedValue(new Error('Network failed'));

    const { result } = renderHook(() => useDiagramList(), { wrapper });

    await waitFor(() => {
      expect(result.current.status).toBe('error');
    });

    expect(result.current.error).toBe('Network failed');
  });
});
