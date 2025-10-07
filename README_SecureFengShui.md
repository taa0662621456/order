# Secure Feng-Shui — OrderComponent Helm

## Namespaces & Policies
```bash
kubectl apply -f namespace-staging.yaml
kubectl apply -f namespace-prod.yaml
# (OpenShift only)
kubectl apply -f securitycontextconstraints.yaml
```

## CI: Kubernetes Security Scan
GitHub Actions workflow: `.github/workflows/k8s-security-scan.yml` (Kubescape + Trivy, отчёты SARIF).

## Deploy (staging)
```bash
helm dependency update charts/ordercomponent
helm upgrade --install ordercomponent charts/ordercomponent -n order-staging
```
