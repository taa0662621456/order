up:
	docker compose -f docker-compose.local.yml --env-file .env.local up -d --build

init:
	@echo "Run DB migrations here (symfony console)"
	@true

ci:
	helm lint order/charts/ordercomponent || true

deploy:
	helmfile -f order/helmfile.yaml apply -e staging || true
	helmfile -f order/helmfile.yaml apply -e production || true

test-mail:
	@echo "Sending test email (requires app and mailpit up)"
	@curl -s -X POST http://localhost:8080/api/test-email \
		-H "Content-Type: application/json" \
		-d '{"to":"test@example.com","subject":"Mailpit CLI Test","message":"Hello from Makefile"}' || true
	@echo "\nCheck http://localhost:8025"

cloud-deploy:
	@echo "Deploying Order to cloud $(env:=staging) cluster..."
	@set -e; \
	NAMESPACE=$${env:-staging}; \
	SLACK_VAR="SLACK_WEBHOOK_URL_$$(echo $$NAMESPACE | tr a-z A-Z)"; \
	helmfile -f deploy/order/helmfile.yaml apply -e $$NAMESPACE || { \
		echo "Deployment failed, attempting rollback..."; \
		RELEASE="order-$$NAMESPACE"; \
		LAST_REV=$$(helm history $$RELEASE -n order-$$NAMESPACE | tail -n 2 | head -n 1 | awk '{print $$1}'); \
		helm rollback $$RELEASE $$LAST_REV -n order-$$NAMESPACE || true; \
		curl -X POST -H 'Content-type: application/json' \
			--data "{\"text\":\"❌ Order $$NAMESPACE deploy failed and rollback triggered.\"}" \
			$${!SLACK_VAR}; \
		exit 1; \
	}; \
	curl -X POST -H 'Content-type: application/json' \
		--data "{\"text\":\"✅ Order $$NAMESPACE deploy succeeded.\"}" \
		$${!SLACK_VAR}
