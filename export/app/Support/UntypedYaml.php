<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Arr;

/**
 * Narrowing boundary for YAML that Statamic hands back untyped. Everything read
 * off disk passes through here once, so callers work with known types instead of
 * checking mixed at each access.
 */
final class UntypedYaml
{
    /**
     * Non-string keys cannot come from a YAML map, so they are dropped rather
     * than carried further.
     *
     * @return array<string, mixed>
     */
    public static function toMap(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->filter(static fn (mixed $item, mixed $key): bool => is_string($key))
            ->all();
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function toMapOfMaps(mixed $value): array
    {
        return collect(self::toMap($value))
            ->map(static fn (mixed $item): array => self::toMap($item))
            ->all();
    }

    /**
     * Write into the tree by dot path. Arr::set() takes its array by reference
     * and drops the key type, so the result is narrowed again here.
     *
     * @param  array<string, mixed>  $map
     * @return array<string, mixed>
     */
    public static function withValueAt(array $map, string $path, mixed $value): array
    {
        Arr::set($map, $path, $value);

        return self::toMap($map);
    }
}
