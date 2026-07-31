import js from '@eslint/js'
import globals from 'globals'
import prettier from 'eslint-config-prettier'

export default [
    js.configs.recommended,
    prettier,
    {
        files: ['resources/js/**/*.js'],
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'script',
            globals: {
                ...globals.browser,
                Alpine: 'readonly',
                Livewire: 'readonly',
                FilamentNotification: 'readonly',
            },
        },
        rules: {
            'no-unused-vars': ['error', { args: 'none' }],
        },
    },
    {
        ignores: ['resources/dist/**'],
    },
]
