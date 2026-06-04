import shouldRenderSignatureOnPost from '../../../src/forum/utils/signatureVisibility';

type FakePostOptions = {
  hidden?: boolean;
  signature?: string | null;
  noUser?: boolean;
};

// A minimal duck-typed stand-in for a Post model — the helper only calls
// `isHidden()` and `user()` (which returns something with `signature()`).
function fakePost({ hidden = false, signature = 'My signature', noUser = false }: FakePostOptions = {}): any {
  return {
    isHidden: () => hidden,
    user: () => (noUser ? null : { signature: () => signature }),
  };
}

describe('shouldRenderSignatureOnPost', () => {
  it('shows the signature on a visible post whose author has one', () => {
    expect(shouldRenderSignatureOnPost(fakePost(), false)).toBe(true);
  });

  it('hides the signature on a hidden post that has not been revealed', () => {
    expect(shouldRenderSignatureOnPost(fakePost({ hidden: true }), false)).toBe(false);
  });

  it('treats an undefined reveal flag as not revealed', () => {
    expect(shouldRenderSignatureOnPost(fakePost({ hidden: true }), undefined)).toBe(false);
  });

  it('shows the signature on a hidden post once its content is revealed', () => {
    expect(shouldRenderSignatureOnPost(fakePost({ hidden: true }), true)).toBe(true);
  });

  it('hides the signature when the author has none', () => {
    expect(shouldRenderSignatureOnPost(fakePost({ signature: null }), false)).toBe(false);
    expect(shouldRenderSignatureOnPost(fakePost({ signature: '' }), false)).toBe(false);
  });

  it('hides the signature when the post has no author', () => {
    expect(shouldRenderSignatureOnPost(fakePost({ noUser: true }), false)).toBe(false);
  });

  it('still requires a signature on a revealed hidden post', () => {
    expect(shouldRenderSignatureOnPost(fakePost({ hidden: true, signature: null }), true)).toBe(false);
  });
});
