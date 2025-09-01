---
id: home
blueprint: pages
title: Home
template: default
updated_by: 6d26d0a8-ff9c-4c3e-a25e-7e036508908c
updated_at: 1756634033
blocks:
  -
    id: ZbruxJDq
    type: hero_demo
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
    id: s343JCRf
    type: style_guide
    enabled: true
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
    id: m5mqwjtr
    title: 'Our team'
    text: 'Made up team for this demo website'
    query: ordered
    limit: 6
    type: team
    enabled: true
  -
    id: m5nrhfol
    title: 'Search demo'
    text: 'Search within posts collection'
    type: search_form
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
    id: m3h0yigt
    type: newsletter
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
                text: 'My goal is to make this the go-to Statamic starter kit for Laravel devs. It’s Blade-first (not Antlers), includes reusable Alpine.js components that cover ~90% of projects, and ships with block/set scaffolding commands to speed up your process.'
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
                text: 'There is no separate docs site but you can check '
              -
                type: text
                marks:
                  -
                    type: link
                    attrs:
                      href: 'https://github.com/jasonbaciulis/bedrock/blob/main/export/README.md'
                      rel: noopener
                      target: _blank
                      title: null
                text: README.md
              -
                type: text
                text: ' and the code is pretty well commented with relevant links to officials '
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
                text: .
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
                text: 'Absolutely! I use it for all client projects and actively maintain it.'
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
                text: 'Report them on '
              -
                type: text
                marks:
                  -
                    type: link
                    attrs:
                      href: 'https://github.com/jasonbaciulis/bedrock/issues'
                      rel: noopener
                      target: _blank
                      title: null
                text: GitHub
              -
                type: text
                text: ', and I''ll address them faster than you can say "cache cleared." Or, you know, turn it off and on again.'
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
                text: 'I welcome pull requests, bug reports or suggestions. Let me know what you think on '
              -
                type: text
                marks:
                  -
                    type: link
                    attrs:
                      href: 'https://github.com/jasonbaciulis/bedrock/discussions'
                      rel: noopener
                      target: _blank
                      title: null
                text: 'GitHub discussions'
              -
                type: text
                text: .
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
                text: 'Yes, some heaver parts of code like Block/Set scaffolding commands have tests.'
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
                text: 'Absolutely. You can learn how to fully utilize what Statamic has to offer and reduce your build time.'
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
                text: 'The code is dark mode by default. Light mode was deprecated after too many developers complained about the glare.'
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
                text: "Of course! This kit is as flexible as your code after a three-coffee streak. Just don't blame me if it becomes self-aware."
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
                text: 'Bedrock operates on a quantum schedule—updates both have and have not been made until you check the commit history. On a more serious note, I’m constantly refining Bedrock—just look at the '
              -
                type: text
                marks:
                  -
                    type: link
                    attrs:
                      href: 'https://github.com/jasonbaciulis/bedrock/releases'
                      rel: noopener
                      target: _blank
                      title: null
                text: 'release history'
              -
                type: text
                text: .
        type: item
        enabled: true
seo_noindex: false
seo_nofollow: false
seo_canonical_type: entry
sitemap_change_frequency: weekly
sitemap_priority: 0.5
seo_title: 'The Foundation for Statamic projects'
seo_description: 'Features shadcn/ui style Alpine.js components that you can customize, extend, and build on. Plus a page builder with CLI tools and complete SEO setup'
og_image: images/bedrock-screenshot-social.png
twitter_image: images/bedrock-screenshot-social.png
---
