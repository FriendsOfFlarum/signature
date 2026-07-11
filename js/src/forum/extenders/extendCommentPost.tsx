import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import CommentPost from 'flarum/forum/components/CommentPost';
import Signature from '../components/Signature';

export default function extendCommentPost() {
  extend(CommentPost.prototype, 'content', function (content) {
    const user = this.attrs.post.user?.();

    if (user && user.signature()) {
      const allowInlineEditing = app.forum.attribute<boolean>('allowInlineEditing') || false;
      const classicLook = app.forum.attribute<boolean>('classicLook') || false;
      const className = classicLook ? 'Post-signature Post-signature--classic' : 'Post-signature';

      content.push(
        <div className={className}>
          <Signature user={user} readonly={!allowInlineEditing} />
        </div>
      );
    }
  });
}
