---
description: Invoke the pest-testing skill and enforce 100% test coverage
---

Invoke the `/pest-testing` skill before doing any test work.

Then make sure the codebase passes 100% test coverage:

```bash
composer run test:unit
```

(equivalent to `pest --parallel --coverage --exactly=100.0`)

If coverage falls below 100%, the report lists the uncovered line numbers per file (e.g. `Models/Project ... 90 / 98.0%` means line 90 is uncovered). Write tests that exercise those exact lines and re-run until it passes.
