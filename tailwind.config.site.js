//--------------------------------------------------------------------------
// Tailwind site configuration
//--------------------------------------------------------------------------
//
// Use this file to completely define the current sites design system by
// adding and extending to Tailwinds default utility classes.
// Docs: https://tailwindcss.com/docs/configuration
// Default: https://github.com/tailwindlabs/tailwindcss/blob/master/stubs/config.full.js
//

import defaultTheme from 'tailwindcss/defaultTheme'
import colors from 'tailwindcss/colors'

export default {
  presets: [],
  theme: {
    extend: {
      colors: {
        black: '#000',
        white: '#fff',
        // Grays: gray colors, with a default fallback if you don't need shades. Always set a DEFAULT when you use shades.
        gray: {
          DEFAULT: colors.gray['900'],
          ...colors.gray,
        },
        // Primary: primary brand color with a default fallback if you don't need shades. Always set a DEFAULT when you use shades.
        primary: {
          DEFAULT: colors.sky['400'],
          ...colors.sky,
        },
        secondary: {
          DEFAULT: colors.indigo['500'],
          ...colors.indigo,
        },
      },
      // Set default transition durations and easing when using the transition utilities.
      transitionDuration: {
        DEFAULT: '150ms',
      },
      transitionTimingFunction: {
        DEFAULT: 'cubic-bezier(0.4, 0, 0.2, 1)',
      },
      animation: {
        shake: 'shake 0.2s ease-in-out 0s 2',
      },
      keyframes: {
        shake: {
          '0%, 100%': { transform: 'translateX(0)' },
          '25%': { transform: 'translateX(0.5rem)' },
          '75%': { transform: 'translateX(-0.5rem)' },
        },
      },
    },
    // Remove the font families you don't want to use.
    fontFamily: {
      mono: [
        // Use a custom mono font for this site by changing 'Anonymous' to the
        // font name you want and uncommenting the following line.
        // 'Anonymous',
        ...defaultTheme.fontFamily.mono,
      ],
      sans: [
        // Use a custom sans serif font for this site by changing 'Gaultier' to the
        // font name you want and uncommenting the following line.
        'Mona Sans',
        ...defaultTheme.fontFamily.sans,
      ],
      serif: [
        // Use a custom serif font for this site by changing 'Lavigne' to the
        // font name you want and uncommenting the following line.
        // 'Lavigne',
        ...defaultTheme.fontFamily.serif,
      ],
    },
    // Define your site's type scale.
    // These are all default Tailwind sizes but you should limit your options to 5-8 sizes.
    fontSize: {
      xs: ['0.75rem', { lineHeight: '1rem' }],
      sm: ['0.875rem', { lineHeight: '1.25rem' }],
      base: ['1rem', { lineHeight: '1.5rem' }],
      lg: ['1.125rem', { lineHeight: '1.75rem' }],
      xl: ['1.25rem', { lineHeight: '1.75rem' }],
      '2xl': ['1.5rem', { lineHeight: '2rem' }],
      '3xl': ['1.875rem', { lineHeight: '1.2' }],
      '4xl': ['2.25rem', { lineHeight: '1.2' }],
      '5xl': ['3rem', { lineHeight: '1.2' }],
      '6xl': ['3.75rem', { lineHeight: '1.2' }],
      '7xl': ['4.5rem', { lineHeight: '1' }],
      '8xl': ['6rem', { lineHeight: '1' }],
      '9xl': ['8rem', { lineHeight: '1' }],
    },
    // The font weights available for this site.
    fontWeight: {
      // hairline: 100,
      // thin: 200,
      // light: 300,
      normal: 400,
      medium: 500,
      semibold: 600,
      bold: 700,
      // extrabold: 800,
      // black: 900,
    },
  },
}
