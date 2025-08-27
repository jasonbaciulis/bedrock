export default {
  trailingComma: 'es5',
  trailingCommaPHP: true,
  printWidth: 100,
  tabWidth: 2,
  overrides: [
    {
      files: ['*.php', '*.vue'],
      options: {
        tabWidth: 4,
      },
    },
    {
      files: ['*.blade.php'],
      options: {
        tabWidth: 4,
        parser: 'blade',
      },
    },
  ],
  semi: false,
  singleQuote: true,
  jsxSingleQuote: false,
  bracketSpacing: true,
  jsxBracketSameLine: false,
  arrowParens: 'avoid',
  plugins: ['@prettier/plugin-php', 'prettier-plugin-tailwindcss', 'prettier-plugin-blade'],
}
