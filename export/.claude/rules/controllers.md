---
paths:
  - "app/Http/Controllers/**"
  - "app/Http/Requests/**"
  - "app/Http/Concerns/**"
  - "app/Attributes/**"
  - "app/Scopes/**"
---

# Controllers Best Practices

## Two Shapes

- Which of the two shapes a controller takes depends on whether it manages a **model**:
  - **A model of that name exists** → resource controller, named for the model, using REST verbs: `ProjectController@show`, `EstimateController@store`.
  - **No model of that name exists** → single-action invokable controller, named `<Verb><Subject>Controller`: `ReadWorkroomController`, `HireDeveloperController`, `SendWorkspaceMessageController`.
- Invokable controllers still get their FormRequest, same as any other action. **The request mirrors the controller name**, swapping the `Controller` suffix for `Request`: `CancelProjectController` → `CancelProjectRequest`. Never prefix it with a REST verb the controller doesn't have — `StoreProjectCancellationRequest` reinvents the resource noun the invokable name exists to avoid.

## Authorization Lives in FormRequests

- Never call `$this->authorize()`, `abort_if()`, or `abort_unless()` inside a controller method. Authorization belongs in that method's FormRequest `authorize()`.
- Every method that authorizes gets its own FormRequest, even when it has nothing to validate and the class holds only `authorize()`.
- `authorize()` runs before validation, so an unauthorized request with a bad payload returns 403 rather than 422. That ordering is intended.

## Models Are Injected, Never Hand-Resolved

- A request never reads `$this->route()`. A route-bound model is injected where it's needed: `authorize(#[RouteParameter] Workspace $workspace)`. `authorize()`, `rules()`, and `after()` are all container-called and accept contextual attributes; `withValidator()` is **not** — logic there that needs an injected model converts to `after()` returning an array of closures. Rule closures capture the injected model with `use ($model)`.
- The controller signature type-hints every route-bound model, even one its body never touches — the type-hint is what triggers implicit binding, and both the request's `#[RouteParameter]` injection and scoped child bindings depend on it.
- A derived model with no route segment gets a custom contextual attribute in `app/Attributes`. The attribute resolves from the bound parent, owns the 404 and the page's eager loads, and memoizes via `$route->setParameter(...)` so the policy and the controller share one instance.
- The authenticated user is injected the same way: `#[CurrentUser] User $user` — `$this->user()` never appears in a request. A possession of the current user gets its own `Current*` attribute resolving through the auth guard.

## Keep Controllers Thin

- Controllers only route HTTP: read the validated request, call an Action, shape the response. Domain logic lives in `app/Actions`.

## Where Query Logic Lives

- No inline query logic in controllers — no `where()`/`orWhere()`/`whereIn()`/`join()`/raw clauses, and no `when()` closures that build query constraints. Controllers may only chain **named** builder methods, `tap(new …)`/`pipe(new …)` applications, and terminal calls (`latest()`, `paginate()`, `get()`, `load()`).
- Actions are writes-only. A read has no side effects, so it never becomes an Action; it goes to one of these homes instead, in order of reach:
  1. **Custom Eloquent Builder** — composable constraints that return `self` (`visibleTo`, `search`). Page filters compose them via a single `filterBy(array $filters)` builder method fed from the index FormRequest's typed `submittedFilters()` accessor.
  2. **Reusable query component in `app/Scopes`** — an invokable class for query logic with no single builder owner: constraints shared across models, or a read that executes the query and returns another value (a paginator, an aggregate). Parameters arrive via the constructor; `__invoke(Builder $query)` does the work; named for what it answers (no suffix). Apply it with the builder's `tap()` when it only constrains and the chain continues, or `pipe()` when it executes the query and ends the chain with a result.
