/**
 * Provider composition tests.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { createElement } from '@wordpress/element';
import { render, screen } from '@testing-library/react';
import { AppErrorBoundary } from '../../../assets/src/shared/providers/AppErrorBoundary';
import { BootstrapProvider } from '../../../assets/src/shared/providers/BootstrapProvider';
import type { AdminBootstrap } from '../../../assets/src/shared/types/bootstrap';

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

function ThrowingChild() {
  throw new Error('Bootstrap failure');
}

describe('admin providers', () => {
  it('renders children within bootstrap provider', () => {
    render(
      createElement(
        BootstrapProvider,
        { value: bootstrap },
        createElement('div', null, 'Library ready')
      )
    );

    expect(screen.getByText('Library ready')).toBeInTheDocument();
  });

  it('shows error boundary when child throws', () => {
    render(
      createElement(
        AppErrorBoundary,
        null,
        createElement(ThrowingChild)
      )
    );

    expect(console).toHaveErrored();
    expect(screen.getByRole('heading', { name: 'Application error' })).toBeInTheDocument();
    expect(screen.getByText('Bootstrap failure')).toBeInTheDocument();
  });
});
