{{#
    Lightweight picture component that uses Glide to generate images. It supports lazy loading and object-cover.
    Notice that the component doesn't use sizes attribute, because in our view it's a waste of time.
    Just pass the dimensions of the largest image size that needs rendered. Images are lazy loaded and saving a few kilobytes
    on the initial load is not worth the extra complexity.

    Docs: https://statamic.dev/tags/glide

    Params:
    - w: Width of the image.
    - h: Height of the image.
    - class: Additional classes for the image.
    - cover: Whether to use object-cover. Defaults to `false`.
    - not_lazy: Whether to disable lazy loading. Defaults to `false`.
 #}}

{{ if image }}
    <picture>
        {{ asset :url="image" }}
            {{ if extension === 'svg' || extension === 'gif' }}
                <img
                    {{ if cover }}
                        class="object-cover size-full {{ class }}"
                        style="object-position: {{ focus | background_position }}"
                    {{ else }}
                        {{ class | attribute:class }}
                    {{ /if }}
                    src="{{ url }}"
                    alt="{{ alt }}"
                    {{ unless not_lazy }}
                        loading="lazy"
                    {{ /unless }}
                    decoding="async"
                    width="{{ width }}"
                    height="{{ height }}"
                />
            {{ elseif w && h }}
                <source srcset="
                    {{ glide:url :width='w' :height='h' fit='crop_focal' format='webp' }} 1x,
                    {{ glide:url :width='w' :height='h' fit='crop_focal' format='webp' dpr='2' }} 2x" type="image/webp">
                <source srcset="
                    {{ glide:url :width='w' :height='h' fit='crop_focal' }} 1x,
                    {{ glide:url :width='w' :height='h' fit='crop_focal' dpr='2' }} 2x" type="{{ image.mime_type }}">
                <img {{ if cover }}
                        class="object-cover size-full {{ class }}"
                        style="object-position: {{ focus | background_position }}"
                    {{ else }}
                        {{ class | attribute:class }}
                    {{ /if }}
                    {{ glide :src="url" :width='w' :height='h' fit='crop_focal' }}
                        src="{{ url }}"
                        alt="{{ alt }}"
                        width="{{ width }}"
                        height="{{ height }}"
                    {{ /glide }}
                    {{ unless not_lazy }}
                        loading="lazy"
                    {{ /unless }}
                    decoding="async"
                >
            {{ /if }}
        {{ /asset }}
    </picture>
{{ /if }}
