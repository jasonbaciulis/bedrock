{{#
    Configure Banner inside Control Panel in /cp/globals/banner
    Relies on a tiny `js-cookie` npm package for setting and getting cookies.
 #}}

<div
    x-cloak
    x-show="visible"
    x-data="{
        visible: false,
        cookieName: 'hide_header_banner',
        initBannerDisplay() {
            const bannerCookie = !!Cookies.get(this.cookieName);
            if (bannerCookie) {
                this.visible = false;
            } else {
                this.visible = true;
            }
        },
        dismiss() {
            this.visible = false;
            Cookies.set(this.cookieName, true, { expires: {{ banner:cookie_expires }} });
      },
    }"
    x-init="initBannerDisplay()"
    class="flex items-center gap-x-6 bg-foreground px-6 py-2.5 sm:px-3.5 sm:before:flex-1"
>
    <div class="text-sm/6 text-white">
        <a href="{{ banner:link_url }}">
            {{ banner:text }}
        </a>
    </div>
    <div class="flex flex-1 justify-end">
        <button type="button" class="btn -m-2 p-2 hover:opacity-80" @click="dismiss()">
            <span class="sr-only">Dismiss</span>
            {{ icon:lucide-x class="size-4 text-white" }}
        </button>
    </div>
</div>
