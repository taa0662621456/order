# Helm — OrderComponent (Full Feng-Shui)

Namespaces:
- staging → `order-staging`
- production → `order-prod`

Deploy:
```bash
kubectl create ns order-staging || true
kubectl create ns order-prod || true
helm dependency update charts/ordercomponent

# staging
helmfile apply -e staging

# production
helmfile apply -e production
```
