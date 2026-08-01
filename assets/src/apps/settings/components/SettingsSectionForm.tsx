/**
 * Settings section form wrapper using WPDS Card structure.
 *
 * @package WebFalcon\MermaidDiagrams
 */

import type { ReactNode } from '@wordpress/element';
import { Card, CardHeader, CardBody, CardFooter, Icon } from '@wordpress/components';
import { MdmButton } from '../../../shared/components/MdmButton';
import { useBootstrap } from '../../../shared/providers/BootstrapProvider';

interface SettingsSectionFormProps {
  sectionTitle?: string;
  sectionDescription?: string;
  sectionIcon?: import('@wordpress/components').IconProps<object>['icon'];
  isDirty: boolean;
  isSaving: boolean;
  onSave: () => void;
  children: ReactNode;
}

export function SettingsSectionForm({
  sectionTitle,
  sectionDescription,
  sectionIcon,
  isDirty,
  isSaving,
  onSave,
  children,
}: SettingsSectionFormProps) {
  const { i18n } = useBootstrap();

  return (
    <Card className="mdm-settings-panel-card">
      {sectionTitle ? (
        <CardHeader>
          <div className="mdm-settings-header">
            {sectionIcon ? (
              <div className="mdm-settings-header-icon">
                <Icon icon={sectionIcon} size={20} />
              </div>
            ) : null}
            <div className="mdm-settings-header-text">
              <h2>{sectionTitle}</h2>
              {sectionDescription ? <p>{sectionDescription}</p> : null}
            </div>
          </div>
        </CardHeader>
      ) : null}

      <CardBody>
        <div className="mdm-settings-form-fields">{children}</div>
      </CardBody>

      <CardFooter>
        <div className="mdm-settings-footer">
          <span className={`mdm-settings-status ${isDirty ? 'is-dirty' : ''}`}>
            {isDirty ? 'Unsaved changes' : 'All changes saved'}
          </span>
          <MdmButton
            variant="primary"
            onClick={onSave}
            disabled={!isDirty || isSaving}
            isBusy={isSaving}
            data-testid="mdm-settings-save"
          >
            {i18n.saveSettings}
          </MdmButton>
        </div>
      </CardFooter>
    </Card>
  );
}
