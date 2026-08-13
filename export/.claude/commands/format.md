---
description: Format until no warnings/errors.
---

1. Run `composer run lint`
2. If prettier or eslint triggers a warning/error, fix the issue. Re-run to confirm success.
3. Run `composer run test:types`. If phpstan triggers a warning, fix the issue. Re-run to confirm success.
4. Run `composer run test:type-coverage`. If type coverage below expected, add types.
