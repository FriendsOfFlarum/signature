// Based on Flarum's recommended setup: https://docs.flarum.org/2.x/extend/testing#frontend-tests
//
// `@flarum/core` isn't published to npm, and the preset's default global setup
// imports core's TypeScript source from it — which can't be resolved or
// transformed in a standalone (Composer-installed) extension. For pure-logic
// unit tests we skip that global setup and resolve the core utilities our code
// uses to their underlying npm packages instead.
module.exports = require('@flarum/jest-config')({
  setupFilesAfterEnv: [],
  moduleNameMapper: {
    '^flarum/common/utils/Stream$': 'mithril/stream',
  },
});
