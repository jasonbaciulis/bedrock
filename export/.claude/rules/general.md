# General Guidelines

- Only communicate in ASD-STE100 Simplified Technical English.
- Before writing a guard, name the concrete caller or state that produces the condition (schema-nullable column, race window, user input). Can't name one → no guard; types and upstream gates count as proof.
- Docblock lines are either an `@`-annotation adding type info the signature can't express (array shapes, generics, `list<>`) or prose within the comment cap below. Stub prose from `make:` commands ("Execute the action.", "Create a new event instance.", "Get the validation rules that apply to the request.") is deleted on sight.

## Naming

- Never use single-letter variable names in closures — use descriptive names
  - `fn (array $field) =>` not `fn (array $f) =>`
  - `fn (Entry $entry) =>` not `fn (Entry $e) =>`
- Spell a multi-word concept out wherever the name *is* the concept — models, enum cases and their values, PHP classes, components, filenames, database tables and columns. Abbreviate only in lookup keys, which are grepped rather than read: route names, config keys, `data-test` attributes.

## Self-documenting code
Code should be readable on its own. Use descriptive method and variable names instead of comments.
Extract multi-line conditions into a method whose name states the business rule, instead of making the reader decode each clause at the call site.

Incorrect:
```php
// Check if a user can join
if ($user->can(Permission::JoinWorkspace)
    && ! $workspace->isMember($user)
    && config()->integer('workspace.slots') - $workspace->membersCount() > 0)
```

Correct:
```php
if ($workspace->isJoinableBy($user))
```

Not just conditions — any statements that together perform one nameable step get extracted, so the calling method reads as a sequence of business events instead of mechanics.

## Comment Style

Doc blocks and comments are capped at 1–2 sentences and allowed only for an invariant the code cannot show (lock ordering, after-commit semantics, a deliberate non-obvious choice). If a sentence describes what the code below does, delete it. Existing files with longer docblocks are legacy, not license — do not match their density.

Comment placement decides the syntax, not the comment's length:

- Use a doc block (`/** */` in TS/JS, PHPDoc in PHP) to document the declaration directly below it — a function, type, constant. It explains *what the symbol is*.
- Use line comments (`//`) only for rationale attached to a statement inside a body. They explain *why a specific line does what it does*.
- A long comment is not automatically a doc block: a multiline explanation inside a function body still uses `//`.
