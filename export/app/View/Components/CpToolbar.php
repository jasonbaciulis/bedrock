<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Statamic\Facades\Preference;
use Illuminate\Contracts\View\View;

class CpToolbar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public function shouldRender(): bool
    {
        return (Preference::get('show_toolbar_local') && config('app.env') === 'local') ||
            (Preference::get('show_toolbar_staging') && config('app.env') === 'staging') ||
            (Preference::get('show_toolbar_production') && config('app.env') === 'production');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cp-toolbar');
    }
}
