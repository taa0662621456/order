# OrderComponent — Iteration 1 (GeneratorPlus)

Commands:
  php bin/console order:generate [count] [--status=<status>] [--seed=<int>] [--with-payment]
  php bin/console order:list

- Generates orders (optionally with payments of $1000 each).
- Bilingual totals: "Payment total: $N (Общий платёж: $N)".
- Integration tests included.
