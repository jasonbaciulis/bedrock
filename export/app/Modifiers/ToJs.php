<?php

namespace App\Modifiers;

use Illuminate\Support\Js;
use Statamic\Modifiers\Modifier;

class ToJs extends Modifier
{
    /**
     * Modify a value.
     *
     * @param mixed  $value The value to be modified
     * @return mixed
     */
    public function index($value)
    {
        return Js::from($value)->toHtml();
    }
}
