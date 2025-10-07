# OrderComponent — Iteration 1 (Symfony 7 + Doctrine ORM 3.x)

- Entities в единственном числе в `Order`: `$orderItem`, `$orderPayment`, `$orderShipment`
- Вложенные сущности: `src/Entity/Order/OrderItem.php`, `OrderPayment.php`, `OrderShipment.php`
- ValueObjects: Money, Currency, Taxation, Discount, Quantity, Sku, OrderStatus, VendorId
- Repository + Service (CRUD, транзакции)
