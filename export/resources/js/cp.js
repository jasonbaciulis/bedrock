/**
 * When extending the control panel, be sure to uncomment the necessary code for your build process:
 * https://statamic.dev/extending/control-panel
 */

import slugifyAction from './components/actions/slugify'

Statamic.booting(() => {
  // Field actions allow you to modify value for specific fields:
  // https://statamic.dev/extending/field-actions

  // Quickly sync slug with the title field
  Statamic.$fieldActions.add('slug-fieldtype', slugifyAction)
})
