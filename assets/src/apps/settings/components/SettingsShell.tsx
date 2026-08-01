/**
 * Settings application shell with dark vertical sidebar navigation.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { useMemo, useState } from '@wordpress/element';
import { Button, Icon } from '@wordpress/components';
import { cog, download, edit, layout, shield, file, info } from '@wordpress/icons';
import { MdmErrorState } from '../../../shared/components/MdmErrorState';
import { MdmLoadingSkeleton } from '../../../shared/components/MdmLoadingSkeleton';
import { MdmButton } from '../../../shared/components/MdmButton';
import { useSettingsSection } from '../../../shared/hooks/useSettingsSection';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';
import { RuntimeDiagnostics } from './RuntimeDiagnostics';
import { SectionFields } from './SectionFields';

const SECTION_ICONS: Record<string, import('@wordpress/components').IconProps<object>['icon']> = {
  rendering: cog,
  downloads: download,
  editor: edit,
  visual_editor: layout,
  permissions: shield,
  data_retention: file,
  diagnostics: info,
};

const DIAGNOSTICS_SECTION_ID = 'diagnostics';

export function SettingsShell() {
  const bootstrap = useBootstrap();
  const [activeSection, setActiveSection] = useState('rendering');
  const {
    settings,
    sectionValues,
    status,
    isDirty,
    isSaving,
    error,
    setField,
    saveSection,
    reload,
  } = useSettingsSection(activeSection === DIAGNOSTICS_SECTION_ID ? 'rendering' : activeSection);

  const sections = useMemo(() => settings?.schema.sections ?? [], [settings]);

  const isDiagnosticsActive = activeSection === DIAGNOSTICS_SECTION_ID;

  if (!bootstrap.capabilities.manageSettings) {
    return (
      <MdmErrorState
        data-testid="mdm-settings-permission-denied"
        title={bootstrap.i18n.permissionDenied}
        message={bootstrap.i18n.permissionDenied}
      />
    );
  }

  if (status === 'loading') {
    return <MdmLoadingSkeleton data-testid="mdm-settings-loading" />;
  }

  if (status === 'error') {
    return (
      <MdmErrorState
        data-testid="mdm-settings-error"
        title={bootstrap.i18n.settingsSaveError}
        message={error ?? bootstrap.i18n.settingsSaveError}
        onRetry={reload}
        retryLabel={bootstrap.i18n.retry}
      />
    );
  }

  const activeSectionMeta = sections.find((s) => s.id === activeSection);
  const activeSectionTitle = isDiagnosticsActive
    ? 'Runtime Diagnostics'
    : (activeSectionMeta?.title ?? '');

  return (
    <div className="mdm-app-layout" data-testid="mdm-settings-shell">
      <div className="mdm-settings-layout">
        <aside className="mdm-settings-sidebar">
          <div className="mdm-settings-sidebar__header">
            <h2 className="mdm-settings-sidebar__title">Settings</h2>
            <p className="mdm-settings-sidebar__description">
              Configure your Mermaid Diagrams plugin preferences.
            </p>
          </div>

          <nav className="mdm-settings-sidebar__nav" aria-label="Settings sections">
            {sections.map((section) => {
              const isActive = activeSection === section.id;
              const icon = SECTION_ICONS[section.id] ?? cog;

              return (
                <button
                  key={section.id}
                  type="button"
                  className={`mdm-settings-nav-item ${isActive ? 'is-active' : ''}`}
                  onClick={() => setActiveSection(section.id)}
                  data-testid={`mdm-settings-nav-${section.id}`}
                >
                  <Icon icon={icon} size={18} />
                  <span>{section.title}</span>
                </button>
              );
            })}
            <button
              type="button"
              className={`mdm-settings-nav-item ${isDiagnosticsActive ? 'is-active' : ''}`}
              onClick={() => setActiveSection(DIAGNOSTICS_SECTION_ID)}
              data-testid="mdm-settings-nav-diagnostics"
            >
              <Icon icon={info} size={18} />
              <span>Runtime Diagnostics</span>
            </button>
          </nav>

          {isDirty && !isDiagnosticsActive ? (
            <div className="mdm-settings-sidebar__footer">
              <Button
                variant="primary"
                className="mdm-settings-sidebar__review-btn"
                onClick={() => void saveSection()}
                isBusy={isSaving}
                disabled={isSaving}
              >
                {isSaving ? 'Saving...' : 'Review 1 change\u2026'}
              </Button>
            </div>
          ) : null}
        </aside>

        <main className="mdm-settings-content-card">
          <div className="mdm-settings-content-card__header">
            <h2 className="mdm-settings-content-card__title">{activeSectionTitle}</h2>
            {!isDiagnosticsActive ? (
              <MdmButton
                variant="primary"
                onClick={() => void saveSection()}
                disabled={!isDirty || isSaving}
                isBusy={isSaving}
                data-testid="mdm-settings-save"
              >
                {bootstrap.i18n.saveSettings || 'Save settings'}
              </MdmButton>
            ) : null}
          </div>

          <div className="mdm-settings-content-card__body">
            {isDiagnosticsActive && settings ? (
              <RuntimeDiagnostics runtime={settings.runtime} />
            ) : (
              <div className="mdm-settings-form-fields">
                <SectionFields sectionId={activeSection} values={sectionValues} onChange={setField} />
              </div>
            )}
          </div>
        </main>
      </div>
    </div>
  );
}
