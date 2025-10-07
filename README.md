# OrderComponent — Iteration 2 (Generator Extended)

Commands:
  php bin/console order:generate [count]
      [--status=<status>] [--seed=<int>] [--amount=<int>] [--currency=<ISO>] [--vendor=<id>] [--with-payment] [--with-shipment]
  php bin/console order:list
  php bin/console order:stats
  php bin/console order:clear

- Payments with amount/currency/method/status.
- Shipments with carrier/tracking/shippedAt.
- Stats show totals and average per order.
