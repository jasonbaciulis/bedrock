{{ seo:collection_defaults | where('collection', {collection}) }}
    {{ if fallback == 'field' }}
        {{ scope:field }}
            {{ field[field_handle] | strip_tags | entities | trim | safe_truncate(157, '…') }}
        {{ /scope:field }}
    {{ elseif fallback == 'custom_text' }}
        {{ custom_text }}
    {{ elseif fallback == 'blocks' }}
        {{ if blocks:0:text }}
            {{ if blocks:0:text | is_array }}
                {{ blocks:0:text | raw | where('type', 'paragraph') | bard_text | strip_tags | entities | trim | safe_truncate(157, '…') }}
            {{ else }}
                {{ blocks:0:text | strip_tags | entities | trim | safe_truncate(157, '…') }}
            {{ /if }}
        {{ else }}
            {{ blocks where="type:article" limit="1" }}
                {{ if article }}
                    {{ article | raw | where('type', 'paragraph') | bard_text | entities | trim | safe_truncate(157, '…') }}
                {{ /if }}
            {{ /blocks }}
        {{ /if }}
    {{ /if }}
{{ /seo:collection_defaults }}
