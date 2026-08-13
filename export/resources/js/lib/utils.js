/**
 * Slugify a string.
 *
 * @param {string} input - The input string.
 * @returns {string} - The slugified string.
 */
export function slugify(input) {
  if (!input) return ''

  const asString = String(input)
  const normalized = asString.normalize('NFKD').replaceAll(/[\u{300}-\u{36F}]/gu, '')
  const slug = normalized
    .toLowerCase()
    .replaceAll('&', ' and ')
    .replaceAll('/', '-')
    .replaceAll(/[\s_]+/g, '-')
    .replaceAll(/[^a-z0-9-]/g, '')
    .replaceAll(/-+/g, '-')
    .replaceAll(/^-+|-+$/g, '')

  return slug
}
