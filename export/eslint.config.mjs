import js from '@eslint/js'
import globals from 'globals'
import prettier from 'eslint-config-prettier'
import unicorn from 'eslint-plugin-unicorn'

export default [
    js.configs.recommended,
    unicorn.configs.recommended,
    prettier,
    {
        rules: {
            // These read as whole words in this codebase, not as abbreviations.
            // Every other replacement stays active.
            'unicorn/name-replacements': [
                'error',
                {
                    replacements: {
                        env: false,
                        props: false,
                        utils: false,
                    },
                },
            ],
        },
    },
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

            // Alpine components are plain objects whose methods bind `this` to the
            // component instance, so `this` outside a class is the normal case here.
            'unicorn/no-this-outside-of-class': 'off',

            // This is browser-only code, and `window.Alpine = Alpine` is Alpine's
            // documented bootstrap.
            'unicorn/prefer-global-this': 'off',
            'unicorn/no-global-object-property-assignment': 'off',

            // DOM and JSON APIs return and expect `null`, not `undefined`.
            'unicorn/no-null': 'off',

            'unicorn/filename-case': ['error', { case: 'camelCase' }],
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
