# OrderComponent Release (RC)

## Prerequisites
- git, composer, PHP 8.2/8.3
- gh CLI (`gh auth login`)
- env tokens:
  ```bash
  export GITHUB_TOKEN=ghp_xxx
  export PACKAGIST_TOKEN=pk_xxx
  ```

## Release from CLI
Linux/macOS:
```bash
chmod +x release_beta.sh
./release_beta.sh 0.3.0-rc
```

Windows PowerShell:
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope Process
./release_beta.ps1 -version 0.3.0-rc
```

## GitHub Actions
- CI: `.github/workflows/ci-enhancements.yml`
- Auto Release on tag: `.github/workflows/release.yml`

## Docker / Compose / Helm
```bash
docker compose up -d --build
helm upgrade --install order-component ./helm
```
