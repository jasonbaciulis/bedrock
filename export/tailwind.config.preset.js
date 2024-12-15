//--------------------------------------------------------------------------
// Tailwind preset configuration
//--------------------------------------------------------------------------
//

import colors from 'tailwindcss/colors'

export default {
  theme: {
    extend: {
      colors: {
        current: 'currentColor',
        transparent: 'transparent',
        // Error styling colors.
        red: colors.red,
        // Notice styling colors.
        yellow: colors.amber,
        // Success styling colors.
        green: colors.green,
      },
      spacing: {
        em: '1em',
      },
      zIndex: {
        // Z-index stuff behind it's parent.
        behind: '-1',
      },
      ringColor: theme => ({
        DEFAULT: theme('colors.primary.DEFAULT'),
      }),
    },
  },
  corePlugins: {
    container: false,
  },
  plugins: [
    // Use Tailwinds forms plugin for form styling: https://github.com/tailwindlabs/tailwindcss-forms
    require('@tailwindcss/forms'),
  ],
}
