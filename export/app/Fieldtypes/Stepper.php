<?php

namespace App\Fieldtypes;

use Statamic\Fields\Fieldtype;

class Stepper extends Fieldtype
{
    protected $categories = ['number'];
    protected $selectableInForms = true;
    protected $icon = 'integer';

    protected function configFieldItems(): array
    {
        return [
            [
                'display' => __('Stepper Settings'),
                'fields' => [
                    'min' => [
                        'display' => __('Minimum Value'),
                        'instructions' => __('The minimum value allowed'),
                        'type' => 'integer',
                        'default' => 0,
                    ],
                    'max' => [
                        'display' => __('Maximum Value'),
                        'instructions' => __('The maximum value allowed'),
                        'type' => 'integer',
                        'default' => 9999,
                    ],
                    'step' => [
                        'display' => __('Step Size'),
                        'instructions' => __('The amount to increment or decrement'),
                        'type' => 'integer',
                        'default' => 1,
                    ],
                    'default' => [
                        'display' => __('Default Value'),
                        'instructions' => __('The default starting value'),
                        'type' => 'integer',
                        'default' => 0,
                    ],
                ],
            ],
            [
                'display' => __('Appearance'),
                'fields' => [
                    'show_input' => [
                        'display' => __('Show Input Field'),
                        'instructions' => __('Allow direct input in addition to +/- buttons'),
                        'type' => 'toggle',
                        'default' => true,
                    ],
                ],
            ],
        ];
    }

    public function preProcess($value)
    {
        if ($value === null) {
            return (int) $this->config('default', 0);
        }

        return (int) $value;
    }
}
