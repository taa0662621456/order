.PHONY: ci test worker up down

up:
	docker compose -f docker/docker-compose.yml up -d

down:
	docker compose -f docker/docker-compose.yml down -v

test:
	APP_ENV=test MESSENGER_TRANSPORT_DSN=amqp://guest:guest@localhost:5672/%2f/messages \	MESSENGER_FAILURE_TRANSPORT_DSN=amqp://guest:guest@localhost:5672/%2f/messages.dlx \	vendor/bin/phpunit -v

worker:
	php bin/console messenger:consume async -vv --time-limit=120 --memory-limit=256M

ci:
	composer validate --strict && composer install --no-interaction --no-progress --prefer-dist && vendor/bin/phpunit -v
