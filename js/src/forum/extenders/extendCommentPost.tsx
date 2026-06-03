import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import CommentPost from 'flarum/forum/components/CommentPost';
import Signature from '../components/Signature';

export default function extendCommentPost() {
  extend(CommentPost.prototype, 'content', function (content) {
    const user = this.attrs.post.user?.();

    if (user && user.signature()) {
      const allowInlineEditing = app.forum.attribute<boolean>('allowInlineEditing') || false;

      content.push(
        <div className="Post-signature">
          <Signature user={user} readonly={!allowInlineEditing} />
        </div>
      );
    }
  });
}
