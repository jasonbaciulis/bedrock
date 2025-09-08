/**
 * Slugify a string.
 *
 * @param {string} input - The input string.
 * @returns {string} - The slugified string.
 *
 * @see https://github.com/statamic/cms/blob/5.x/resources/js/bootstrap/globals.js
 */
export function slugify(input) {
  if (!input) return ''

  return Statamic.$slug.create(input)
}
