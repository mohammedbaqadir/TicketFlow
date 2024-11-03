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
      // Scopes (WHAT is being changed)
      // Core
      'core',    // Core application logic
      'deps',   // Dependencies
      'config', // Configuration files

      // Security & Auth
      'auth',   // Authentication/Authorization

      // Application Layers
      'api',  // API related changes
      'ui',   // User interface
      'db',   // Database

      // Infrastructure
      'infra',  // Infrastructure related changes
      'deploy', // Deployment specific changes

      // Documentation
      'docs',  // Documentation

      // Testing
      'test',    // Test infrastructure
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