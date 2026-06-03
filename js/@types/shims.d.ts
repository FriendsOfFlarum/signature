import 'flarum/common/models/User';

declare module 'flarum/common/models/User' {
  export default interface User {
    signature(): string | null;
    signatureHtml(): string | null;
    canEditSignature(): boolean;
    canHaveSignature(): boolean;
  }
}
