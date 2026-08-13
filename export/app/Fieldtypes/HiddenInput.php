<?php

declare(strict_types=1);

namespace App\Fieldtypes;

use Statamic\Fields\Fieldtype;

final class HiddenInput extends Fieldtype
{
    /** @var string */
    protected static $title = 'Hidden Input';

    /** @var bool */
    protected $selectable = false;

    /** @var bool */
    protected $selectableInForms = true;

    /** @var list<string> */
    protected $categories = ['text'];

    /** @var string */
    protected $icon = 'fieldtype-hidden';

    public function component(): string
    {
        return 'text';
    }

    /**
     * @return array<string, array<string, string>>
     */
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
