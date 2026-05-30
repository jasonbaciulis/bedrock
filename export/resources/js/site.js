import Alpine from 'alpinejs'
import Anchor from '@alpinejs/anchor'
import Collapse from '@alpinejs/collapse'
import Persist from '@alpinejs/persist'
import Focus from '@alpinejs/focus'
import Form from './plugins/form'
import Intersect from '@alpinejs/intersect'
import Mask from '@alpinejs/mask'
import Cookies from 'js-cookie'

window.Cookies = Cookies
window.Alpine = Alpine

Alpine.plugin([Anchor, Collapse, Focus, Persist, Intersect, Mask, Form])
Alpine.start()
