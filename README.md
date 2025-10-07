# OrderComponent — Release Pipeline (Iteration K)

## 🚀 CI/CD Auto-Release
This iteration introduces **full release automation** for GitHub and Packagist.

### 🧩 Workflow summary
- Trigger: `push tag v*.*.*` or manual dispatch with `version` input.
- Steps:
  1. Install dependencies and run tests.
  2. Package source into `OrderComponent.zip`.
  3. Create GitHub Release with notes and artifact.
  4. Notify Packagist for update.

### 🔐 Required Secrets
| Secret | Description |
|---------|-------------|
| `GITHUB_TOKEN` | Default GitHub Actions token (auto-provided) |
| `PACKAGIST_USERNAME` | Your Packagist username |
| `PACKAGIST_TOKEN` | API token from https://packagist.org/profile |

### 🧰 Usage
```bash
git tag v0.4.0-beta
git push origin v0.4.0-beta
```
or trigger manually via:
```yaml
workflow_dispatch:
  inputs:
    version: 'v0.4.0-beta'
```

### 🧠 Notes
- Composer metadata reflects release version.
- PHPStan, Psalm, Rector configured in composer scripts.
- Coverage and logs saved as artifacts for CI audit.

---

**Version:** v0.4.0-beta  
**Maintained by:** your-org / OrderComponent Team
