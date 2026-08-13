export default {
    semi: false,
    singleQuote: true,
    singleAttributePerLine: false,
    htmlWhitespaceSensitivity: 'css',
    printWidth: 100,
    trailingComma: 'es5',
    tabWidth: 4,
    bracketSpacing: true,
    arrowParens: 'avoid',
    overrides: [
        {
            files: ['**/*.yml'],
            options: {
                tabWidth: 2,
            },
        },
    ],
    plugins: ['prettier-plugin-tailwindcss', 'prettier-plugin-blade', 'prettier-plugin-antlers'],
}
