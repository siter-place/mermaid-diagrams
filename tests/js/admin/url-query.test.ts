/**
 * URL query parser tests.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import {
  parseLibraryQuery,
  serializeLibraryQuery,
  toSearchQuery,
} from '../../../assets/src/shared/state/url-query';

describe('url-query', () => {
  it('parses pagination defaults', () => {
    const parsed = parseLibraryQuery('');

    expect(parsed.page).toBe(1);
    expect(parsed.perPage).toBe(20);
    expect(parsed.orderby).toBe('modified');
    expect(parsed.order).toBe('DESC');
  });

  it('serializes and parses pagination state', () => {
    const state = {
      page: 2,
      perPage: 10,
      orderby: 'title' as const,
      order: 'ASC' as const,
    };

    const serialized = serializeLibraryQuery(state);
    const parsed = parseLibraryQuery(`?${serialized}`);

    expect(parsed.page).toBe(2);
    expect(parsed.perPage).toBe(10);
    expect(parsed.orderby).toBe('title');
    expect(parsed.order).toBe('ASC');
  });

  it('maps library query to REST search query', () => {
    const query = toSearchQuery({
      page: 3,
      perPage: 15,
      search: 'auth',
    });

    expect(query).toEqual({
      page: 3,
      per_page: 15,
      search: 'auth',
      category: undefined,
      tag: undefined,
      type: undefined,
      status: undefined,
      author: undefined,
      orderby: 'modified',
      order: 'DESC',
      view: 'summary',
    });
  });
});
