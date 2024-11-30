export default {
  extends: ['@commitlint/config-conventional'],
  rules: {
    'type-enum': [2, 'always', [
      // Types (HOW the change is made)
      'feat',     // New feature
      'fix',      // Bug fix
      'docs',    // Documentation only
      'style',   // Formatting, typos, etc
      'refactor',// Code change that neither fixes a bug nor adds a feature
      'perf',   // Performance improvement
      'test',   // Adding missing tests
      'build',   // Changes that affect the build system or external dependencies
      'ci',   // Changes to CI configuration files and scripts
      'chore',   // Other changes that don't modify src or test files
      'revert',  // Reverts a previous commit
      'security', // Security improvements
    ]],
    'scope-enum': [2, 'always', [
      // Application Logic
      'action',      // Single-purpose business logic actions/operations
      'policy',      // Authorization and access control
      'event',       // Event handling and listeners
      'job',         // Background/queued jobs

      // Data Layer
      'model',       // Eloquent models, relationships, scopes
      'migration',   // Database structure changes
      'seed',        // Database seeding and factories

      // Interface Layer
      'api',         // API endpoints, resources, transformers
      'console',     // CLI commands and features

      // Presentation
      'view',        // Templates, components, frontend assets

      // Infrastructure
      'auth',        // Authentication mechanisms
      'cache',       // Caching configurations and strategies
      'config',      // Application configuration
      'integration',  // Third-party service integrations

      // Documentation
      'readme'
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
    'footer-max-line-length': [2, 'always', 100],
  }
};