---
paths:
  - "app/Models/**"
  - "app/Builders/**"
---

# Models Best Practices

## Query Scopes

- A model with **3 or more query scopes** moves them into a custom Eloquent Builder instead of `#[Scope]` methods. Fewer than 3 stay on the model as `#[Scope]` methods.
- The builder lives in `app/Builders/{Model}Builder.php` and is attached with `#[UseEloquentBuilder({Model}Builder::class)]` on the model.
- Shape: `final class ProjectBuilder extends Builder` with a class-level `@extends Builder<Project>` docblock — no per-method `@param`/`@return Builder<...>` annotations. Methods are plain `public`, take only their own arguments, and return `self`.
- Call sites type against the builder, not the generic `Builder`: `Project::query()->visibleTo($user)`, and `->when(..., fn (ProjectBuilder $query, string $value): ProjectBuilder => $query->search($value))`.
- Builder methods are for constraints a single model owns and composes. Query logic with no single builder owner — constraints shared across models (e.g. spanning a polymorphic contract), or a read that executes the query and returns another value — belongs in an invokable `app/Scopes` class applied via `tap()`/`pipe()` instead.

```php
/**
 * @extends Builder<Project>
 */
final class ProjectBuilder extends Builder
{
    public function visibleTo(User $user): self
    {
        return $this->whereBelongsTo($user);
    }
}
```
