---
paths:
  - "routes/**"
---

# Routing Best Practices

## Use Resource Controllers

Use `Route::resource()` or `apiResource()` for RESTful endpoints.

```php
Route::resource('posts', PostController::class);
// In routes/api.php — the /api prefix is applied automatically
Route::apiResource('posts', Api\PostController::class);
```

- A controller with all seven actions gets a bare `Route::resource()` — never a list of individual verb routes.
- Partial resources pick the shorter list: 3 or fewer actions kept → `->only([...])`, 4 or more kept → `->except([...])`.
- Keep the URIs and route names the resource generates. Don't re-declare a resource verb as an individual route to bend its URI (`GET messages/{message}` for `edit` was this mistake); frontend and tests reach routes through Wayfinder and `route()`, so the generated shape costs nothing.

## Middleware on Resources

- Middleware shared by every action goes on the resource: `->middleware('throttle:60,1')`.
- Middleware for a subset of actions goes through `->middlewareFor()` / `->withoutMiddlewareFor()`. Never split a resource's verbs across route groups to vary middleware per action — that's what dissolved the `projects` resource into seven route declarations.

```php
Route::resource('projects', ProjectController::class)
    ->middlewareFor(['show', 'edit'], ShareProjectRoomState::class);

Route::resource('messages', MessageController::class)
    ->only(['edit', 'update', 'destroy'])
    ->middleware('throttle:60,1');
```

## Grouping

- Routes sharing a URI prefix and name prefix are declared in one group that states both once — repeating `projects/{project}/…` and `->name('projects.…')` per route is the smell:

```php
Route::prefix('projects/{project}')->name('projects.')->group(function (): void {
    Route::post('cancel', CancelProjectController::class)
        ->name('cancel');

    Route::post('hire/{developer}', HireDeveloperController::class)
        ->name('hire');
});
```

- Two routes are enough to group when the set will grow (project state transitions will); don't wait for a third.
- Group by what routes share, not by theme: a middleware group for shared middleware, a prefix group for a shared path. Nesting one inside the other is fine; a group whose members share nothing is not a group.

## Route Model Binding

- A value that identifies the resource acted on is a route segment, not a body field: `POST hire/{developer}`, never a validated `developer_id` payload. Identity failures then speak HTTP — unknown id → 404 from binding, not-permitted target → 403 from `authorize()`; validation rules guarding an identity field are the smell that it belongs in the URL.
- A child segment that must belong to its parent gets `->scopeBindings()`, and the parent model exposes the relationship the param name implies: `{statementOfWorkVersion}` resolves through `Workroom::statementOfWorkVersions()` — a `HasManyThrough` added for exactly that route.
- A singular 1:1 sub-resource keeps its id-less URL (`projects/{project}/workroom`) — never add a redundant `{workroom}` segment just to get binding. The model resolves through a custom contextual attribute instead (`#[CurrentWorkroom]`; see the controllers rules).
- Param names spell the concept out like any other name: `{statementOfWorkVersion}`, `{developer}` — never `{version}` or `{user}` when the segment means something narrower.

## Throttling

- Every write endpoint a user can trigger rapid-fire (chat sends, read receipts, message edits) is throttled at `throttle:60,1` — the shared group for invokable-controller routes, `->middleware()` on resources.
- One-shot state transitions (cancel, hire, reveal) don't need it; the policy and state machine already make repeats a no-op.
- A new endpoint that genuinely needs a different limit gets a named limiter via `RateLimiter::for()`, not a second magic number in the routes file.
