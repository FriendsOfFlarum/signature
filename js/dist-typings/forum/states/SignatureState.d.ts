import Stream from 'flarum/common/utils/Stream';
export default class SignatureState {
    content: Stream<string>;
    editing: boolean;
    constructor();
    setContent(content: Stream<string>): void;
    toggleEditing(): void;
}
