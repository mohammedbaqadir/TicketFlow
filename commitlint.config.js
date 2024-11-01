export default {
  extends: ['@commitlint/config-conventional'],
  rules: {
    'type-enum': [2, 'always', [
      'feat',
      'fix',
      'docs',
      'style',
      'refactor',
      'perf',
      'test',
      'build',
      'ci',
      'chore',
      'revert',
      'security'
    ]],
    'scope-enum': [2, 'always', [
      // Core
      'core',
      'deps',
      'config',

      // Security & Auth
      'security',  // Added back
      'auth',

      // Application Layers
      'api',
      'ui',
      'db',

      // Infrastructure
      'ci',
      'build',

      // Documentation
      'docs',

      // Testing
      'test'
    ]],
    'scope-case': [2, 'always', 'lowercase'],
    'scope-empty': [2, 'never'],

    'subject-empty': [2, 'never'],
    'subject-case': [2, 'always', ['sentence-case']],
    'subject-max-length': [2, 'always', 50],
    'header-max-length': [2, 'always', 72],
    'body-max-line-length': [2, 'always', 72],
    'body-leading-blank': [2, 'always'],
    'footer-leading-blank': [2, 'always'],

  }
};