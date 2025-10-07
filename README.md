# OrderComponent — Iteration 1 (Integration pack)

- Doctrine migrations: `migrations/Version20251006_somecomment.php`
- Console: `order:validate-mapping`
- Integration tests with boot Kernel (SQLite file DB):
  - `DoctrineMappingTest`
  - `OrderLifecycleTest` (создание, переход статуса)
