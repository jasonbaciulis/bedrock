import { slugify } from '../../lib/utils.js'

export default {
  title: 'Slugify',
  quick: true,
  icon: 'regular/text-small',
  run: ({ update, store, storeName }) => {
    const values = store.state.publish[storeName].values
    update(slugify(values.title))
  },
}
