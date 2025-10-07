#!/usr/bin/env bash
set -euo pipefail

BASE_URL=${BASE_URL:-http://localhost:8080}

echo "[SMOKE] /healthz"
curl -fsS "$BASE_URL/healthz" | jq . >/dev/null

echo "[SMOKE] /metrics"
curl -fsS "$BASE_URL/metrics" | head -n5

echo "[SMOKE] create order"
ORDER_ID=$(curl -fsS -X POST "$BASE_URL/orders" -H 'Content-Type: application/json' -d '{"currency":"USD","items":[{"sku":"SKU-1","quantity":2,"unitPrice":1000}]}' | jq -r '.id')

echo "[SMOKE] pay"
curl -fsS -X POST "$BASE_URL/orders/$ORDER_ID/pay" -H 'Content-Type: application/json' -d '{"amount":2000}' >/dev/null

echo "[SMOKE] ship"
curl -fsS -X POST "$BASE_URL/orders/$ORDER_ID/ship" >/dev/null

echo "[OK] Order flow passed (id=$ORDER_ID)"
