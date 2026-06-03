import Component from 'flarum/common/Component';
import type Mithril from 'mithril';
import SignatureState from '../states/SignatureState';
/**
 * Self-service signature editing for the Settings page. Owns the shared
 * SignatureState so the Edit button and the Signature component stay in sync,
 * mirroring how SignaturePage drives editing for moderators.
 */
export default class SignatureSettings extends Component {
    signatureState: SignatureState;
    oninit(vnode: Mithril.Vnode): void;
    view(): JSX.Element | null;
}
