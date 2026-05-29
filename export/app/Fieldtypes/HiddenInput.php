<?php

namespace App\Fieldtypes;

use Statamic\Fields\Fieldtype;

class HiddenInput extends Fieldtype
{
    protected static $title = 'Hidden Input';

    protected $selectable = false;

    protected $selectableInForms = true;

    protected $categories = ['text'];

    protected $icon = 'fieldtype-hidden';

    public function component(): string
    {
        return 'text';
    }

    public function configFieldItems(): array
    {
        return [
            'antlers_expression' => [
                'display' => 'Antlers Expression',
                'instructions' => 'Antlers expression for the hidden field value, e.g. {{ registered_number }}',
                'type' => 'text',
            ],
        ];
    }
}
