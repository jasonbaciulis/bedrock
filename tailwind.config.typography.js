//--------------------------------------------------------------------------
// Tailwind Typography configuration
//--------------------------------------------------------------------------
//
// Here you may overwrite the default Tailwind Typography (or prosé) styles.
// Some defaults are provided.
// More info: https://tailwindcss.com/docs/typography-plugin.
//

const plugin = require('tailwindcss/plugin')

module.exports = {
  theme: {
    extend: {
      typography: theme => ({
        DEFAULT: {
          css: {
            '--tw-prose-body': theme('colors.gray.700'),
            '--tw-prose-headings': theme('colors.gray.DEFAULT'),
            '--tw-prose-counters': theme('colors.primary.DEFAULT'),
            '--tw-prose-bullets': theme('colors.gray.DEFAULT'),
            '--tw-prose-quotes': theme('colors.gray.DEFAULT'),
            '--tw-prose-quote-borders': theme('colors.primary.DEFAULT'),
            '--tw-prose-bold': theme('colors.gray.DEFAULT'),
            '--tw-prose-links': theme('colors.primary.500'),

            'h1, h2, h3, h4': {
              color: `${theme('colors.gray.DEFAULT')}`,
            },
            'ul > li p, ol > li p': {
              marginTop: '0 !important',
              marginBottom: '0 !important',
            },
            a: {
              fontWeight: '600',
              textDecoration: 'none',
            },
            'a:hover': {
              color: `${theme('colors.primary.600')}`,
            },
          },
        },
      }),
    },
  },
  plugins: [
    require('@tailwindcss/typography')({
      modifiers: [],
    }),
  ],
}
