# PHP / Laravel Code Style

## Collections Over Plain PHP

- Prefer Laravel collections over plain PHP array functions
  - `collect()->where()` not `array_filter()`
  - `collect()->pluck()` not `array_map()`
  - `collect()->contains()` not `in_array()`
  - `collect()->each()` / `->map()` / `->mapWithKeys()` not `foreach`
- Prefer collection pipelines (`map`, `filter`, `reject`, `flatMap`, `mapWithKeys`) over imperative loops. Exception is for a single array operation, PHP functions are fine. For example, no needs to call `collect()->each()->all()` if all we did was take array, wrap in collection, iterate and convert back to an array.

## String Helpers

- Prefer `Str::` / `Str::of()` helpers over PHP string functions
  - `Str::startsWith()` not `str_starts_with()`
  - `Str::after()` not `str_replace()` for extracting substrings
  - `Str::contains()` not `str_contains()`

## Naming

- Never use single-letter variable names in closures — use descriptive names
  - `fn (array $field) =>` not `fn (array $f) =>`
  - `fn (Entry $entry) =>` not `fn (Entry $e) =>`

## Type hint
- Always type hint and add return types.
- Instead of using @phpstan-ignore, always try to resolve issue with typehinting/importing/pointing to the used class for IDE to discover it

## Readability

- If a code block needs a comment to be understood, extract it into a named method instead
- Always type-hint closure parameters when the type is known
