# OrderComponent — α-3 Billing API (Symfony 7 / Doctrine ORM 3.x)

Содержимое:
- **Entities**: OrderInvoice, OrderPaymentIntent, OrderTransaction
- **Services**: BillingService, PaymentProcessor
- **Repositories**: OrderInvoiceRepository, OrderTransactionRepository
- **ValueObjects**: InvoiceNumber, TaxRate, PaymentStatus
- **API**: ресурсы для инвойсов, платежных интентов и транзакций (API Platform)

Установка:
1. Скопируй `src/*` в компонент, соблюдая контракт неймспейсов.
2. Подключи `config/services/billing.yaml` в контейнер DI.
3. Создай миграции и проверь схему:
   ```bash
   php bin/console doctrine:migrations:diff
   php bin/console doctrine:migrations:migrate --no-interaction
   php bin/console doctrine:schema:validate
   ```

Тесты (минимум):
- генерация инвойса
- создание payment intent
- capture → transaction

Дата сборки: 2025-10-07
