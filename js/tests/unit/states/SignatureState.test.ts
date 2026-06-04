import SignatureState from '../../../src/forum/states/SignatureState';
import Stream from 'flarum/common/utils/Stream';

describe('SignatureState', () => {
  it('starts empty and not editing', () => {
    const state = new SignatureState();

    expect(state.content()).toBe('');
    expect(state.editing).toBe(false);
  });

  it('stores the content it is given', () => {
    const state = new SignatureState();

    state.setContent(Stream('hello world'));

    expect(state.content()).toBe('hello world');
  });

  it('replaces the content on a subsequent setContent', () => {
    const state = new SignatureState();

    state.setContent(Stream('first'));
    state.setContent(Stream('second'));

    expect(state.content()).toBe('second');
  });

  it('exposes content as a writable stream', () => {
    const state = new SignatureState();

    state.content('updated');

    expect(state.content()).toBe('updated');
  });

  it('toggles the editing flag', () => {
    const state = new SignatureState();

    state.toggleEditing();
    expect(state.editing).toBe(true);

    state.toggleEditing();
    expect(state.editing).toBe(false);
  });

  it('returns to the original editing state after two toggles', () => {
    const state = new SignatureState();
    const initial = state.editing;

    state.toggleEditing();
    state.toggleEditing();

    expect(state.editing).toBe(initial);
  });
});
