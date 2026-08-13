# PHP / Laravel Code Style

## Collections Over Plain PHP

- Prefer Laravel collections over plain PHP array functions
  - `collect()->where()` not `array_filter()`
  - `collect()->pluck()` not `array_map()`
  - `collect()->contains()` not `in_array()`
  - `collect()->each()` / `->map()` / `->mapWithKeys()` not `foreach`
  - Prefer collection pipelines (`map`, `filter`, `reject`, `flatMap`, `mapWithKeys`) over imperative loops.
With **exception** for a single array operation, PHP functions are fine. For example, no needs to call `collect()->each()->all()` if all we did was take array, wrap in collection, iterate and convert back to an array.
