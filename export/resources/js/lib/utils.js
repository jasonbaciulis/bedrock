/**
 * Slugify a string.
 *
 * @param {string} input - The input string.
 * @returns {string} - The slugified string.
 */
export function slugify(input) {
  if (!input) return ''

  const asString = String(input)
  const normalized = asString.normalize('NFKD').replace(/[\u0300-\u036f]/g, '')
  const slug = normalized
    .toLowerCase()
    .replace(/&/g, ' and ')
    .replace(/\//g, '-')
    .replace(/[\s_]+/g, '-')
    .replace(/[^a-z0-9-]/g, '')
    .replace(/-+/g, '-')
    .replace(/^-+|-+$/g, '')

  return slug
}
