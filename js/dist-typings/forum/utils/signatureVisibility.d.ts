import type Post from 'flarum/common/models/Post';
/**
 * Whether a signature should be rendered beneath the given post in its current
 * view.
 *
 * Hidden (soft-deleted) posts don't show a signature unless the viewer has
 * revealed the post's content, and the author must actually have a signature.
 */
export default function shouldRenderSignatureOnPost(post: Post, revealContent: boolean | undefined): boolean;
