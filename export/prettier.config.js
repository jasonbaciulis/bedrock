export default {
  trailingComma: 'es5',
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
    {
      files: ['*.antlers.html'],
      options: {
        parser: 'antlers',
        tabWidth: 4,
      },
    },
  ],
  semi: false,
  singleQuote: true,
  jsxSingleQuote: false,
  bracketSpacing: true,
  arrowParens: 'avoid',
  plugins: [
    'prettier-plugin-tailwindcss',
    'prettier-plugin-blade',
    'prettier-plugin-antlers',
  ],
}
