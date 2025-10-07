# Order — Observability Pack

## What’s inside
- ServiceMonitor for `order-app` and `order-worker` (expects `/metrics`).
- PrometheusRule: HPA capacity, pod availability, CPU/memory, HTTP 5xx ratio.
- Grafana dashboard: `order-overview.json`.

## Install (kube-prometheus-stack assumed)
```bash
kubectl apply -f observability/prometheus/servicemonitor-order-app.yaml
kubectl apply -f observability/prometheus/servicemonitor-order-worker.yaml
kubectl apply -f observability/prometheus/prometheusrule-order.yaml
# Import Grafana dashboard JSON
```

## Notes
- If your app doesn’t expose `/metrics`, add Symfony Prometheus bundle or sidecar exporter.
- Labels assume `app: order-app` and `app: order-worker` on Pods/Services.
