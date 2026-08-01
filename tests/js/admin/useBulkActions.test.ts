/**
 * Bulk actions hook tests.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { act, renderHook } from '@testing-library/react';
import { useBulkActions } from '../../../assets/src/shared/hooks/useBulkActions';

jest.mock('../../../assets/src/shared/api/client', () => ({
  bulkOperation: jest.fn(),
}));

import { bulkOperation } from '../../../assets/src/shared/api/client';

describe('useBulkActions', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('returns partial failure ids from bulk response', async () => {
    (bulkOperation as jest.Mock).mockResolvedValue({
      results: [
        { id: 1, ok: true },
        { id: 2, ok: false, error: { code: 'mdm_forbidden', message: 'Denied' } },
      ],
      summary: { requested: 2, succeeded: 1, failed: 1 },
    });

    const { result } = renderHook(() => useBulkActions());

    let bulkResult: Awaited<ReturnType<typeof result.current.runBulk>> | undefined;
    await act(async () => {
      bulkResult = await result.current.runBulk([1, 2], 'trash');
    });

    expect(bulkResult?.failedIds).toEqual([2]);
    expect(result.current.lastResult?.succeeded).toBe(1);
  });
});
