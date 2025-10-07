# Deployment & Monitoring (Iteration 14)

## Состав
- `Dockerfile.prod` — multi-stage, php-fpm 8.3 + amqp + opcache
- `docker/docker-compose.prod.yml` — nginx + php + PostgreSQL + RabbitMQ
- `.env.prod.example` — переменные окружения
- `config/packages/{monolog.php, rate_limiter.php, lexik_jwt_authentication.yaml, security.yaml}`
- Контроллеры: `/metrics`, `/healthz`
- GitHub Actions: `.github/workflows/cd.yml` (build → push → deploy → migrate)

## Быстрый старт (staging)
```bash
cp .env.prod.example .env
docker compose -f docker/docker-compose.prod.yml up -d --build
# открыть http://localhost:8080/healthz и /metrics
```

## JWT
Сгенерируй ключи:
```bash
mkdir -p config/jwt
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
# установи JWT_PASSPHRASE в .env
```

## Rate Limiting
Политика `api` — 60 rps/мин. Применяй в firewall или через custom middleware.

## Мониторинг
- `/metrics` — формат Prometheus
- Логи: `var/log/app.log`, `var/log/order.log`

## CD
- Тегируй релиз `v1.0.0` → GitHub Actions соберёт образ и задеплоит по SSH.
- Переменные: `DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_SSH_KEY`, `DEPLOY_DIR`.
