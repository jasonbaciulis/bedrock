<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class FormField extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public array $field) {}

    public function fieldsWithoutLabels(): array
    {
        return ['toggle', 'radio', 'checkboxes', 'stepper'];
    }

    public function containerClass(): string
    {
        $width = (int) ($this->field['width'] ?? 100);
        return match ($width) {
            25 => 'md:col-span-3',
            33 => 'md:col-span-4',
            50 => 'md:col-span-6',
            66 => 'md:col-span-8',
            75 => 'md:col-span-9',
            default => 'md:col-span-12',
        };
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form-field', [...$this->field]);
    }
}
