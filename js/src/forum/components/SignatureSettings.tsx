import Component from 'flarum/common/Component';
import app from 'flarum/forum/app';
import Button from 'flarum/common/components/Button';
import FieldSet from 'flarum/common/components/FieldSet';
import type Mithril from 'mithril';
import Signature from './Signature';
import SignatureState from '../states/SignatureState';

/**
 * Self-service signature editing for the Settings page. Owns the shared
 * SignatureState so the Edit button and the Signature component stay in sync,
 * mirroring how SignaturePage drives editing for moderators.
 */
export default class SignatureSettings extends Component {
  signatureState!: SignatureState;

  oninit(vnode: Mithril.Vnode) {
    super.oninit(vnode);

    this.signatureState = new SignatureState();
  }

  view() {
    const user = app.session.user;

    if (!user) {
      return null;
    }

    return (
      <FieldSet className="Settings-signature" label={app.translator.trans('fof-signature.forum.settings.heading')}>
        {!this.signatureState.editing && (
          <div className="Settings-signature-controls">
            <Button className="Button" icon="fas fa-edit" onclick={() => this.signatureState.toggleEditing()}>
              {app.translator.trans('fof-signature.forum.buttons.edit')}
            </Button>
          </div>
        )}
        <Signature user={user} state={this.signatureState} />
      </FieldSet>
    );
  }
}
