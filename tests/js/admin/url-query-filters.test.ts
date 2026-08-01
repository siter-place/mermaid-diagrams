/**
 * URL query round-trip tests for Phase 05 filters.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import {
  parseLibraryQuery,
  serializeLibraryQuery,
  toSearchQuery,
} from '../../../assets/src/shared/state/url-query';

describe('url-query filters', () => {
  it('round-trips all filter params', () => {
    const state = {
      page: 2,
      perPage: 50,
      search: 'auth',
      category: [7],
      tag: [3],
      type: ['flowchart'],
      status: ['publish'],
      author: [1],
      orderby: 'title' as const,
      order: 'ASC' as const,
    };

    const serialized = serializeLibraryQuery(state);
    const parsed = parseLibraryQuery(`?${serialized}`);

    expect(parsed).toMatchObject(state);
    expect(toSearchQuery(parsed)).toMatchObject({
      page: 2,
      per_page: 50,
      search: 'auth',
      category: [7],
      tag: [3],
      type: ['flowchart'],
      status: ['publish'],
      author: [1],
      orderby: 'title',
      order: 'ASC',
      view: 'summary',
    });
  });
});
