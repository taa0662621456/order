# Security Add-ons

1) **PodSecurityPolicy (legacy)** — `legacy/podsecuritypolicy.yaml` (для кластеров < 1.25).  
2) **Production overrides** — `charts/ordercomponent/values-production.example.yaml` (TLS + PDB + ресурсы).  
3) **Trivy image scan** — `.github/workflows/container-image-scan.yml` (CRITICAL/HIGH -> fail).

Применение:
```bash
# PSP (legacy clusters)
kubectl apply -f legacy/podsecuritypolicy.yaml

# prod overrides
helm upgrade --install ordercomponent charts/ordercomponent -n order-prod   -f charts/ordercomponent/values-production.example.yaml
```
