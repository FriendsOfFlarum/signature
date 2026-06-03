import app from 'flarum/forum/app';
import extendUserPage from './extenders/extendUserPage';
import extendCommentPost from './extenders/extendCommentPost';
import extendSettingsPage from './extenders/extendSettingsPage';

export { default as extend } from './extend';

app.initializers.add('fof-signature', () => {
  extendUserPage();
  extendCommentPost();
  extendSettingsPage();
});
