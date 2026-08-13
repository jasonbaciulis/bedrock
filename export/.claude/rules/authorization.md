---
paths:
  - "app/Policies/**"
  - "app/Http/Requests/**"
  - "app/Actions/**"
  - "app/Http/Controllers/**"
  - "app/Exceptions/**"
---

# Authorization Separation of Concerns

Always adhere to the following structural separation when designing or refactoring authorization, validation, and business logic layers.

## Policies (The User-Initiated Gate)
* **Purpose:** Answer the full question — "may this user do this to this resource *right now*?" Identity, permission, and model state all belong here.
* **Non-CRUD abilities are welcome.** Name the transition verb: `cancel`, `hire`, `revealPrice`, `finalize`. One ability per user-facing operation gives each verb a single named, testable home.
* **Status codes:** Identity/permission failures deny plainly (403). State failures deny with the accurate status and a message: `Response::denyWithStatus(Response::HTTP_LOCKED, 'The statement of work is no longer editable.')`.
* **UI hints come from the same ability** (`can()` props, Filament visibility). Never hand-compose identity + state in a resource or controller (`can('update') && $model->isEditable()`) — that composition drifts from enforcement.

## Form Requests (The "What")
* **Purpose:** Validate incoming HTTP payload structures and bind the endpoint to its policy ability.
* **`authorize()` must preserve the deny status:** use `Gate::inspect(...);` when policy ability returns `Response`; use `$user->can(...)`, when a policy method returns `bool`.
* **Field-anchored, user-fixable feedback is validation, not a policy denial** — duplicate estimate, outlier consent, price-must-differ stay in `rules()`/`after()` where the form can render them.

## Actions (The Mutation)
* **Purpose:** Execute the workflow. Actions run ungated — trusted, system-initiated callers (scheduled commands, listeners, other actions) invoke them directly and skip the policy by design.
* **The one check an action keeps is a race-sensitive invariant:** one whose answer can change between the policy read and the write, where acting on the stale answer corrupts state. Re-check it under the row lock inside the transaction. This duplication is intended: the policy supplies the friendly status for the common case; the lock protects integrity.
* **Failure Result:** An under-lock re-check that fails throws a dedicated domain exception: `throw_unless($statementOfWork->isEditable(), StatementOfWorkLockedException::class)`. Pass a message argument only when the throw site needs to override the exception's default.

## Domain Exceptions
* **Exist only for under-lock re-checks** — the ordinary state denial happens in the policy via `denyWithStatus`.
* **Location & shape:** `app/Exceptions`, `final`, extend Symfony's `HttpException`.
* **Why `HttpException`:** the status and default message live in the constructor, so the exception *is* its HTTP translation — no handler mapping to drift, and Laravel treats these as expected flow rather than log-worthy errors.
* **Status codes are never hardcoded** — always the `Symfony\Component\HttpFoundation\Response` constants.
* **Naming:** Suffix with `Exception`, and name the illegal state, not the endpoint: `StatementOfWorkLockedException`, `StatementOfWorkHasNoVersionsException`.
* **One exception per illegal state, not per endpoint.** The same state violation thrown from several actions reuses one class.
* **Docblock:** 1 sentence naming the illegal state.
* **Reserve `LogicException` for programmer errors** — states no user request can legitimately produce (e.g. adjusting a price on a project that has none). Those surface as 500 and are reported.

## Entry Points
* **Every user-initiated entry point runs the policy** — HTTP via the FormRequest's `Gate::inspect`, an MCP tool via `Gate::authorize()` before it calls the action. The trusted/untrusted line is user-initiated vs. system-initiated, not HTTP vs. CLI.

## Controllers (The Orchestrator)
* **Purpose:** Glue the request, action, and response together.
* **Rule:** Do not inline validation, authorization logic, or raw database queries. No `try/catch` around action calls: policy denials and domain exceptions carry their own status, so the framework's handler renders them like `abort()` would. Call the action, return the response.

## Choosing the Response Code
Applies to both `denyWithStatus` in policies and domain exceptions in actions:
* **409 Conflict** — the transition doesn't apply to the resource's current state; nothing is locked, the request is just stale (e.g. reverting a finalization that was already reverted). The typical double-click/stale-tab code.
* **422 Unprocessable** — the payload or the resource's content cannot support the operation (finalizing a draft with zero versions).
* **423 Locked** — the resource is in a deliberately locked/immutable state and a reverse transition (or nothing) unlocks it: finalized, accepted, lifecycle stage passed.
