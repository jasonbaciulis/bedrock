import js from '@eslint/js'
import globals from 'globals'
import prettier from 'eslint-config-prettier'

export default [
  js.configs.recommended,
  prettier,
  {
    ignores: [
      'node_modules/**',
      'vendor/**',
      'storage/**',
      'bootstrap/cache/**',
      'public/**',
      'resources/dist/**',
    ],
  },
  {
    files: ['resources/js/**/*.{js,mjs,cjs}'],
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: {
        ...globals.browser,
        Alpine: 'readonly',
        Statamic: 'readonly',
        appName: 'readonly',
        gtag: 'readonly',
      },
    },
    rules: {
      'no-console': 'off',
      'no-debugger': 'error',
    },
  },
  {
    files: ['vite.config.*', 'eslint.config.*'],
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: { ...globals.node },
    },
  },
]
