import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import CommentPost from 'flarum/forum/components/CommentPost';
import Signature from '../components/Signature';
import shouldRenderSignatureOnPost from '../utils/signatureVisibility';

export default function extendCommentPost() {
  extend(CommentPost.prototype, 'content', function (content) {
    const post = this.attrs.post;

    if (!shouldRenderSignatureOnPost(post, this.revealContent)) {
      return;
    }

    const allowInlineEditing = app.forum.attribute<boolean>('allowInlineEditing') || false;
    const classicLook = app.forum.attribute<boolean>('classicLook') || false;
    const className = classicLook ? 'Post-signature Post-signature--classic' : 'Post-signature';

    content.push(
      <div className={className}>
        <Signature user={post.user()!} readonly={!allowInlineEditing} />
      </div>
    );
  });
}
