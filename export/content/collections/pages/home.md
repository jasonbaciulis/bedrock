---
id: home
blueprint: pages
title: Home
template: default
updated_by: 6d26d0a8-ff9c-4c3e-a25e-7e036508908c
updated_at: 1755852380
blocks:
  -
    id: ZbruxJDq
    type: hero_home
    enabled: true
    title: 'The Foundation for your Statamic projects'
    text: 'shadcn/ui style Alpine.js components that you can customize, extend, and build on. Plus page builder and complete SEO setup.'
    buttons:
      -
        id: KdAsb0Ad
        label: 'Get started'
        link_type: url
        target_blank: false
        url: 'https://statamic.com/starter-kits/jasonbaciulis/bedrock'
        variant: primary
      -
        id: ALbCABEq
        label: 'View components'
        link_type: url
        target_blank: false
        url: '#components'
        variant: ghost
  -
    id: m3e8vdlz
    title: 'Project inquiry'
    text: 'Demo contact form with sections and conditional fields.'
    form: contact
    type: contact
    enabled: true
    success_message: 'Thanks! We got your inquiry and will get back to you within 24 hours.'
    submit_label: 'Submit the inquiry'
  -
    id: m3h0yigt
    type: newsletter
    enabled: true
  -
    id: s343JCRf
    type: style_guide
    enabled: true
  -
    id: m5mqwjtr
    title: 'Our team'
    text: 'Made up team for this demo website'
    query: ordered
    limit: 6
    type: team
    enabled: true
  -
    id: m4seyrjx
    title: 'Recent posts'
    text: 'Some description for the blog'
    query: latest
    limit: 3
    type: blog_excerpt
    enabled: true
  -
    id: m5mjha3w
    title: 'Testimonials demo'
    text: 'Option to use a "Load more" button to load more entries. Under the hood a tiny and flexible JS script can handle loading entries from any collection.'
    query: latest
    limit: 6
    type: testimonials
    enabled: true
  -
    id: m5nrhfol
    title: 'Search demo'
    text: 'Search within posts collection'
    type: search_form
    enabled: true
  -
    id: UXTueUcZ
    block_type: collapsed
    title: 'Frequently asked questions'
    type: faqs
    enabled: true
    items:
      -
        id: m3e8xdmv
        title: 'What is this Statamic starter kit anyway?'
        text:
          -
            type: paragraph
            content:
              -
                type: text
                text: 'Think of it as the "Hello World" of Statamic, but with more bells, whistles, and hidden Easter eggs you''ll probably never find.'
        type: item
        enabled: true
      -
        id: 8fEzuNYK
        title: 'Is there documentation available?'
        text:
          -
            type: paragraph
            content:
              -
                type: text
                text: "Sure is! It's written in invisible ink. Or you can check the official "
              -
                type: text
                marks:
                  -
                    type: link
                    attrs:
                      href: 'https://statamic.dev/'
                      rel: noopener
                      target: _blank
                      title: null
                text: 'Statamic docs'
              -
                type: text
                text: '—whichever you find first.'
        type: item
        enabled: true
      -
        id: m3ctoy43
        title: 'Is this starter kit suitable for production?'
        text:
          -
            type: paragraph
            content:
              -
                type: text
                text: 'Absolutely! If by "production" you mean your local environment where you''re the only user. Otherwise, proceed with the usual developer optimism.'
        type: item
        enabled: true
      -
        id: eGOG3jlZ
        title: 'What if I encounter bugs?'
        text:
          -
            type: paragraph
            content:
              -
                type: text
                text: 'Report them on GitHub, and we''ll address them faster than you can say "cache cleared." Or, you know, turn it off and on again.'
        type: item
        enabled: true
      -
        id: m3csin0x
        title: 'Can I contribute to this project?'
        text:
          -
            type: paragraph
            content:
              -
                type: text
                text: 'We welcome pull requests, bug reports, and dad jokes. Bonus points if you include all three.'
        type: item
        enabled: true
      -
        id: m3csj20j
        title: 'Does it come with unit tests?'
        text:
          -
            type: paragraph
            content:
              -
                type: text
                text: "Yes, they're located somewhere between the TODO comments and the commented-out console logs."
        type: item
        enabled: true
      -
        id: m3csjdp0
        title: 'Will this make me a better developer?'
        text:
          -
            type: paragraph
            content:
              -
                type: text
                text: 'Absolutely. Using this starter kit adds +10 to your Stack Overflow reputation. Disclaimer: This is not legally binding.'
        type: item
        enabled: true
      -
        id: m3csjp9r
        title: 'Is there a dark mode?'
        text:
          -
            type: paragraph
            content:
              -
                type: text
                text: 'Our code is dark mode by default. Light mode was deprecated after too many developers complained about the glare.'
        type: item
        enabled: true
      -
        id: m3cskr7k
        title: 'Can I customize the components?'
        text:
          -
            type: paragraph
            content:
              -
                type: text
                text: "Of course! This kit is as flexible as your code after a three-coffee streak. Just don't blame us if it becomes self-aware."
        type: item
        enabled: true
      -
        id: m3csk1z8
        title: 'How often is this starter kit updated?'
        text:
          -
            type: paragraph
            content:
              -
                type: text
                text: 'We operate on a quantum schedule—updates both have and have not been made until you check the commit history.'
        type: item
        enabled: true
seo_noindex: false
seo_nofollow: false
seo_canonical_type: entry
sitemap_change_frequency: weekly
sitemap_priority: 0.5
seo_title: 'The Foundation for Statamic projects'
seo_description: 'Features shadcn/ui style Alpine.js components that you can customize, extend, and build on. Plus a page builder with CLI tools and complete SEO setup'
---
