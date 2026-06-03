import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import type Mithril from 'mithril';
import UserPage from 'flarum/forum/components/UserPage';
import LinkButton from 'flarum/common/components/LinkButton';
import ItemList from 'flarum/common/utils/ItemList';

export default function extendUserPage() {
  extend(UserPage.prototype, 'navItems', function (items: ItemList<Mithril.Children>) {
    const user = this.user;
    const isSelf = user?.id() === app.session.user?.id();

    // Self-editing lives on the Settings page; only surface the profile tab when a
    // moderator is editing someone else's signature.
    if (user && !isSelf && user.canEditSignature()) {
      items.add(
        'signature',
        <LinkButton href={app.route('user.signature', { username: user.slug() })} icon="fas fa-signature">
          {app.translator.trans('fof-signature.forum.buttons.signature')}
        </LinkButton>,
        20
      );
    }
  });
}
