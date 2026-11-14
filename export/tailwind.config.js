/**
@type {import('tailwindcss').Config}
*/
export default {
    theme: {
        extend: {
            // Each key merges into the plugin's default styles: https://github.com/tailwindlabs/tailwindcss-typography/blob/main/src/styles.js
            typography: () => ({
                DEFAULT: {
                    css: {
                        '--tw-prose-body': 'var(--color-foreground)',
                        '--tw-prose-headings': 'var(--color-foreground)',
                        '--tw-prose-counters': 'var(--color-primary)',
                        '--tw-prose-bullets': 'var(--color-foreground)',
                        '--tw-prose-quotes': 'var(--color-foreground)',
                        '--tw-prose-quote-borders': 'var(--color-primary)',
                        '--tw-prose-bold': 'var(--color-foreground)',
                        '--tw-prose-links': 'var(--color-primary)',
                        maxWidth: 'none',
                        fontSize: 'var(--text-lg)',
                        lineHeight: 'var(--text-lg--line-height)',
                        p: {
                            marginTop: '0',
                            marginBottom: '0',
                        },
                        a: {
                            textDecorationThickness: 'max(1px, 0.0625rem)',
                            textUnderlineOffset: '0.1578em',
                            '&:hover': {
                                textDecorationThickness: 'max(3px, 0.1875rem, 0.12em)',
                                textDecorationSkipInk: 'none',
                            },
                        },
                        'h1, h2, h3, h4, h5, h6': {
                            marginTop: '1em',
                            marginBottom: '0',
                        },
                        'ul, ol': {
                            marginTop: '0',
                            marginBottom: '0',
                        },
                        li: {
                            marginTop: '0',
                            marginBottom: '0',
                        },
                        'ul > li + li, ol > li + li': {
                            marginTop: '1em',
                        },
                        'ul > li p, ol > li p': {
                            marginTop: '0',
                            marginBottom: '0',
                        },
                    },
                },
            }),
        },
    },
}
