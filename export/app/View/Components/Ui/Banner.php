<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\View\Component;
use Statamic\Globals\Variables as GlobalVariable;
use Illuminate\Contracts\View\View;

class Banner extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public GlobalVariable $banner) {}

    public function shouldRender(): bool
    {
        return $this->banner->show;
    }

    public function data(): array
    {
        return [
            'cookie_expires' => $this->banner->cookie_expires,
            'link_url' => $this->banner->link_url,
            'text' => $this->banner->text,
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.banner');
    }
}
