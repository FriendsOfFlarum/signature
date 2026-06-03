import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import type Mithril from 'mithril';
import SettingsPage from 'flarum/forum/components/SettingsPage';
import ItemList from 'flarum/common/utils/ItemList';
import SignatureSettings from '../components/SignatureSettings';

export default function extendSettingsPage() {
  extend(SettingsPage.prototype, 'settingsItems', function (items: ItemList<Mithril.Children>) {
    const user = app.session.user;

    if (!user || !user.canEditSignature()) {
      return;
    }

    items.add('signature', <SignatureSettings />);
  });
}
