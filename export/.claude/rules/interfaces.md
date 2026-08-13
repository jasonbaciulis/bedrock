---
paths:
  - "app/Contracts/**"
---

# Interfaces/Contracts Best Practices

Depend on contracts at system boundaries (payment gateways, notification channels, external APIs) for testability and swappability.

Incorrect (concrete dependency):
```php
class OrderService
{
    public function __construct(private StripeGateway $gateway) {}
}
```

Correct (interface dependency):
```php
interface PaymentGateway
{
    public function charge(int $amount, string $customerId): PaymentResult;
}

class OrderService
{
    public function __construct(private PaymentGateway $gateway) {}
}
```

Bind in a service provider:

```php
$this->app->bind(PaymentGateway::class, StripeGateway::class);
```
