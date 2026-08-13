<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;
use Statamic\Facades\Config;

/**
 * Naming conventions for one scaffolded set: the display name, the fieldset
 * handle (snake_case), and the view slug (kebab-case).
 */
final readonly class ScaffoldName
{
    private function __construct(
        public string $display,
        public string $fieldset,
        public string $view,
    ) {}

    public static function fromDisplay(string $display): self
    {
        $locale = Config::getShortLocale();

        return new self(
            display: $display,
            fieldset: Str::slug($display, '_', $locale),
            view: Str::slug($display, '-', $locale),
        );
    }

    public static function viewSlugForFieldset(string $fieldsetHandle): string
    {
        return Str::replace('_', '-', $fieldsetHandle);
    }
}
