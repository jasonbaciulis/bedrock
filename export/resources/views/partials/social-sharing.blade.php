<ul
    class="{{ $class }} flex items-center gap-4"
    x-data="{
        popupWindow: (url, windowName, win, w, h) => {
            const y = win.top.outerHeight / 2 + win.top.screenY - h / 2
            const x = win.top.outerWidth / 2 + win.top.screenX - w / 2
            return win.open(
                url,
                windowName,
                `toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=${w}, height=${h}, top=${y}, left=${x}`,
            )
        },
    }"
>
    <li>
        <a
            href="#"
            x-on:click.prevent="
                popupWindow(
                    'https://x.com/intent/tweet?url={{ $current_url }}&text={{ Statamic::modify($title)->urlencode() }}&via={{ $seo->twitter_site }}',
                    'X',
                    window,
                    611,
                    435,
                )
            "
            aria-label="X"
            class="block hover:opacity-80"
        >
            <s:svg src="social/x" class="size-5" />
        </a>
    </li>
    <li>
        <a
            href="#"
            x-on:click.prevent="
                popupWindow(
                    'https://www.facebook.com/sharer.php?u={{ $current_url }}',
                    'Facebook',
                    window,
                    611,
                    435,
                )
            "
            aria-label="Facebook"
            class="block hover:opacity-80"
        >
            <s:svg src="social/facebook" class="size-5" />
        </a>
    </li>
    <li>
        <a
            href="#"
            x-on:click.prevent="
                popupWindow(
                    'https://www.linkedin.com/sharing/share-offsite/?url={{ $current_url }}',
                    'Linkedin',
                    window,
                    611,
                    435,
                )
            "
            aria-label="Linkedin"
            class="block hover:opacity-80"
        >
            <s:svg src="social/linkedin" class="size-5" />
        </a>
    </li>
</ul>
