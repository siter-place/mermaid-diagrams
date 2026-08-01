/**
 * Selection hook tests.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { act, renderHook } from '@testing-library/react';
import { useDiagramSelection } from '../../../assets/src/shared/hooks/useDiagramSelection';

const items = [
  { id: 1, title: 'One' },
  { id: 2, title: 'Two' },
] as import('../../../assets/src/shared/api/types').DiagramSummary[];

describe('useDiagramSelection', () => {
  it('toggles row selection', () => {
    const { result } = renderHook(() => useDiagramSelection());

    act(() => {
      result.current.toggleRow(1);
    });

    expect(result.current.isSelected(1)).toBe(true);
    expect(result.current.selectedCount).toBe(1);

    act(() => {
      result.current.toggleRow(1);
    });

    expect(result.current.isSelected(1)).toBe(false);
  });

  it('selects and clears page items', () => {
    const { result } = renderHook(() => useDiagramSelection());

    act(() => {
      result.current.togglePage(items);
    });

    expect(result.current.isPageSelected(items)).toBe(true);

    act(() => {
      result.current.togglePage(items);
    });

    expect(result.current.selectedCount).toBe(0);
  });
});
