/**
 * Settings application shell.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import { useMemo, useState } from '@wordpress/element';
import { Icon } from '@wordpress/components';
import { cog, download, edit, layout, shield, file } from '@wordpress/icons';
import { MdmErrorState } from '../../../shared/components/MdmErrorState';
import { MdmLoadingSkeleton } from '../../../shared/components/MdmLoadingSkeleton';
import { useSettingsSection } from '../../../shared/hooks/useSettingsSection';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';
import { RuntimeDiagnostics } from './RuntimeDiagnostics';
import { SectionFields } from './SectionFields';
import { SettingsSectionForm } from './SettingsSectionForm';

const SECTION_ICONS: Record<string, import('@wordpress/components').IconProps<object>['icon']> = {
  rendering: cog,
  downloads: download,
  editor: edit,
  visual_editor: layout,
  permissions: shield,
  data_retention: file,
};

const SECTION_DESCRIPTIONS: Record<string, string> = {
  rendering: 'Configure default Mermaid theme, viewport dimensions, and rendering limits.',
  downloads: 'Manage export and download options for Mermaid diagram source and SVG assets.',
  editor: 'Set code editor preferences including line numbers, auto-completion, and debounce.',
  visual_editor: 'Configure flowchart visual editing capabilities and beta feature adapters.',
  permissions: 'Define capability requirements for publishing and library access.',
  data_retention: 'Specify data cleanup policy when the plugin is uninstalled.',
};

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
  } = useSettingsSection(activeSection);

  const sections = useMemo(() => settings?.schema.sections ?? [], [settings]);
  const activeSectionMeta = useMemo(
    () => sections.find((s) => s.id === activeSection),
    [sections, activeSection]
  );

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

  return (
    <div className="mdm-app-layout" data-testid="mdm-settings-shell">
      <div className="mdm-settings-layout">
        <nav className="mdm-settings-nav" aria-label="Settings sections">
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
        </nav>

        <SettingsSectionForm
          sectionTitle={activeSectionMeta?.title}
          sectionDescription={SECTION_DESCRIPTIONS[activeSection]}
          sectionIcon={SECTION_ICONS[activeSection]}
          isDirty={isDirty}
          isSaving={isSaving}
          onSave={() => {
            void saveSection();
          }}
        >
          <SectionFields sectionId={activeSection} values={sectionValues} onChange={setField} />
        </SettingsSectionForm>
      </div>

      {settings ? <RuntimeDiagnostics runtime={settings.runtime} /> : null}
    </div>
  );
}
