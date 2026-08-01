/**
 * useSettingsSection hook tests.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { act, renderHook, waitFor } from '@testing-library/react';
import { createElement, type ReactNode } from '@wordpress/element';
import { useSettingsSection } from '../../../assets/src/shared/hooks/useSettingsSection';
import { BootstrapProvider } from '../../../assets/src/shared/providers/BootstrapProvider';
import { NoticesProvider } from '../../../assets/src/shared/providers/NoticesProvider';
import type { AdminBootstrap } from '../../../assets/src/shared/types/bootstrap';

jest.mock('../../../assets/src/shared/api/client', () => ({
  getSettings: jest.fn(),
  updateSettingsSection: jest.fn(),
}));

import { getSettings, updateSettingsSection } from '../../../assets/src/shared/api/client';

const bootstrap: AdminBootstrap = {
  screen: 'settings',
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
    settingsSaved: 'Settings saved.',
    settingsSaveError: 'Unable to save settings.',
  },
};

function wrapper({ children }: { children: ReactNode }) {
  return createElement(
    BootstrapProvider,
    { value: bootstrap },
    createElement(NoticesProvider, null, children)
  );
}

describe('useSettingsSection', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    (getSettings as jest.Mock).mockResolvedValue({
      schema: {
        title: 'Settings',
        description: 'Configure plugin',
        sections: [{ id: 'rendering', title: 'Rendering' }],
      },
      values: {
        rendering: {
          defaultTheme: 'default',
          defaultToolbar: true,
        },
      },
      capabilities: { manageSettings: true },
      runtime: {
        pluginVersion: '1.3.1',
        mermaidVersion: '11.4.1',
        phpVersion: '8.3.0',
        wpVersion: '7.0',
      },
    });
  });

  it('tracks dirty state and replaces values after save', async () => {
    (updateSettingsSection as jest.Mock).mockResolvedValue({
      defaultTheme: 'dark',
      defaultToolbar: true,
    });

    const { result } = renderHook(() => useSettingsSection('rendering'), { wrapper });

    await waitFor(() => {
      expect(result.current.status).toBe('ready');
    });

    act(() => {
      result.current.setField('defaultTheme', 'dark');
    });

    expect(result.current.isDirty).toBe(true);

    await act(async () => {
      await result.current.saveSection();
    });

    expect(result.current.sectionValues.defaultTheme).toBe('dark');
    expect(result.current.isDirty).toBe(false);
    expect(updateSettingsSection).toHaveBeenCalledWith('rendering', {
      defaultTheme: 'dark',
      defaultToolbar: true,
    });
  });

  it('returns error state when settings load fails', async () => {
    (getSettings as jest.Mock).mockRejectedValue(new Error('Forbidden'));

    const { result } = renderHook(() => useSettingsSection('rendering'), { wrapper });

    await waitFor(() => {
      expect(result.current.status).toBe('error');
    });

    expect(result.current.error).toBe('Forbidden');
  });
});
