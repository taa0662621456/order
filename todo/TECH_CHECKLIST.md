# Order Component — Технический чек-лист (α)

## /src
- [ ] **OrderComponentBundle**: регистрация бандла, `Extension`, `CompilerPass` (автоконфиг сервисов/хендлеров/подписчиков)
- [ ] **DependencyInjection/OrderComponentExtension.php**: загрузка base `services.yaml`, импорт `config/imported/**/*.yml`
- [ ] **DependencyInjection/CompilerPass/**:
  - [ ] Тегирование `messenger.message_handler`
  - [ ] Автотег `kernel.event_subscriber` для `EventSubscriberInterface`
  - [ ] Doctrine mapping автоподключение (xml/yaml/attributes)

### /src/Entity
- [ ] **Aggregate Roots**: `Order`, `OrderItem`, `OrderPayment`, `OrderShipment`
- [ ] **Value Objects**: `Money`, `Currency`, `Taxation`, `Discount`, `Quantity`, `Sku`
- [ ] **Связанные сущности/интеграции**: `OrderStorage|OrderInventory`, `OrderAddress`, `OrderVendor` (→ `VendorInterface`)
- [ ] Атрибуты/аннотации Doctrine, индексы, уникальные ключи, `created_at/updated_at`, soft delete (если нужно)
- [ ] Инварианты домена в конструкторах/фабриках

### /src/Repository
- [ ] `OrderRepository`, `OrderItemRepository`, `OrderPaymentRepository`, `OrderShipmentRepository`
- [ ] Спецификации/фильтры (по статусу, по интервалу дат, по Vendor/Customer)
- [ ] Транзакционная согласованность при пакетных апдейтах

### /src/Service
- [ ] `OrderService`, `OrderPaymentService`, `OrderShipmentService`, `OrderAddressService`
- [ ] Идемпотентность (IdempotencyKey), ретраи/бэкофф на внешних интеграциях
- [ ] Outbox-паттерн (запись Domain Events → таблица outbox), консюмер

### /src/Event & /src/Subscriber
- [ ] Domain Events: `OrderPlaced`, `OrderPaid`, `OrderShipped`, `OrderCancelled`, `OrderRefunded`
- [ ] Application Events/Listeners: Email/Inventory/Vendor/Analytics
- [ ] Подписчики с явными зависимостями и idempotent обработкой

### /src/Message (CQRS/Messenger)
- [ ] Команды: `OrderPlacing`, `OrderCancelation`, `OrderRefunding`, `OrderShipment`, `OrderPayment`
- [ ] Хендлеры: `*Handler` (транзакционные границы, валидация, возврат результата)
- [ ] Read models/проекции и синхронизация (`syncReadModels`)

### /src/Workflow
- [ ] Symfony Workflow: Draft → Placed → Paid → Shipped → Completed / Cancelled / Refunded
- [ ] Гварды, действия, эмиссия событий на переходах
- [ ] Аудит-трек

### /src/Pricing
- [ ] Ценовой движок: Base + Discounts + Taxes + Shipping + Fees
- [ ] Стратегии: `PromotionStrategyInterface`, `TaxationStrategyInterface`
- [ ] Денежные расчёты: точность, округления, конверсия валют

### /src/Integration
- [ ] **Inventory**: Reserve/Release Stock listeners
- [ ] **Payment**: интерфейсы шлюзов (Stripe/PayPal/Authorize.Net); webhook listeners; Refund API
- [ ] **Shipment**: Carrier Strategy (DHL/UPS/FedEx); tracking updates; авто-комплит статуса

## /config
- [ ] `Resources/config/services.yaml`: `_defaults` (autowire/autoconfigure), исключения путей
- [ ] `order.yaml` (Configuration Tree): currency, tax-defaults, rounding, feature_flags
- [ ] Doctrine mapping paths (xml/yaml/attributes)

## /tests
- [ ] Unit: VO, сервисы, хендлеры (Mocks/Stubs)
- [ ] Integration: Doctrine транзакции, Messenger, Outbox
- [ ] Functional: HTTP-клиент (если есть контроллеры/API)
- [ ] E2E: полный цикл заказа (Draft→Completed)
- [ ] Фикстуры и тестовые фабрики (например, Zenstruck/Foundry)

## Качество/Процессы
- [ ] PHPStan (max level), Psalm (errLevel 3)
- [ ] Rector (PHP 8.3), CS fixer/PHPCS
- [ ] CI: matrix PHP 8.2/8.3; composer validate; phpunit; phpstan; psalm; rector dry-run
- [ ] Семантическое версионирование, теги `v0.1.0-alpha` и далее
