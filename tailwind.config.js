//--------------------------------------------------------------------------
// Tailwind configuration
//--------------------------------------------------------------------------
//
// Use the Tailwind configuration to completely define the current sites
// design system by adding and extending to Tailwinds default utility
// classes. Various aspects of the config are split inmultiple files.
//

import defaultConfig from 'tailwindcss/defaultConfig'
import typographyConfig from './tailwind.config.typography.js'
import presetConfig from './tailwind.config.preset.js'
import siteConfig from './tailwind.config.site.js'

export default {
  // The various configurable Tailwind configuration files.
  presets: [defaultConfig, typographyConfig, presetConfig, siteConfig],
  mode: 'jit',
  // Configure Purge CSS.
  content: [
    './resources/views/**/*.html',
    './resources/js/**/*.js',
    './resources/**/*.vue',
    './content/**/*.md',
  ],
  safelist: [],
}
