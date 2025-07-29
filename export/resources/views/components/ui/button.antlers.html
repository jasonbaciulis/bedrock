{{#
    Params:
    - label: The text to display in the button.
    - as: The HTML element to render as a button. Defaults to `a`.
    - button_type: The type of button. Options: `primary`, `secondary`, `text`.
    - class: Additional classes to apply to the button.
    - unlinkable: Boolean. If a button is not an `a` element.
    - link_type: The type of link. Options: `entry`, `url`, `email`, `tel`, `asset`, `code`.
    - url: If `link_type` is url. The URL to link to.
    - entry: If `link_type` is entry. The entry to link to.
    - email: If `link_type` is email. The email address to link to.
    - tel: If `link_type` is tel. The telephone number to link to.
    - asset: If `link_type` is asset. The asset to link to.
    - code: If `link_type` is code. The JavaScript code to run.
    - target_blank: Whether to open the link in a new tab.
    - slot:attributes: Additional attributes to apply to the button through the slot.
 #}}

{{ if label }}
    <{{ as or 'a' }} {{ slot:attributes }} class="btn
        {{ switch(
            (button_type == 'primary') => 'btn--primary',
            (button_type == 'secondary') => 'btn--secondary',
            (button_type == 'link') => 'btn--link',
            (button_type == 'ghost') => 'btn--ghost',
            (button_type == 'outline') => 'btn--outline',
            (button_type == 'destructive') => 'btn--destructive',
        )}}
        {{ class }}
    "
        {{ unless unlinkable }}
            {{ switch(
                (link_type == 'entry') => 'href="{{ entry:url }}"',
                (link_type == 'url') => 'href="{{ url }}"',
                (link_type == 'email') => 'href="mailto:{{ email | obfuscate_email }}"',
                (link_type == 'tel') => 'href="tel:{{ tel }}"',
                (link_type == 'asset') => 'href="{{ asset }}" download',
                (link_type == 'code') => 'href="" x-data @click.prevent="{{ code }}"',
            )}}
            {{ target_blank ?= 'rel="noopener" target="_blank"' }}
        {{ /unless }}>
        {{ label }}
    </{{ as or 'a' }}>
{{ /if }}
