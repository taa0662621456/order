$ErrorActionPreference = "Stop"
param([string]$version = "0.3.0-rc")
$branch = "release/v$version"
$repo = "YOURUSER/OrderComponent"

Write-Host "==> Preparing release $version"
git checkout -B $branch

Write-Host "Updating composer.json version → $version"
composer config version $version

Write-Host "Generating CHANGELOG.md section..."
$date = Get-Date -Format 'yyyy-MM-dd'
$header = "## [v$version] - $date`n### Changes`n"
$lastTag = git describe --tags --abbrev=0 2>$null
if ($lastTag) { $log = git log --pretty=format:"- %s" "$lastTag"..HEAD } else { $log = git log --pretty=format:"- %s" }
"$header$log" | Out-File tmp_changelog -Encoding utf8
if (Test-Path CHANGELOG.md) { Get-Content tmp_changelog, CHANGELOG.md | Set-Content CHANGELOG.md } else { Get-Content tmp_changelog | Set-Content CHANGELOG.md }
Remove-Item tmp_changelog -Force

git add .
git commit -m "Release $version"
git tag -a "v$version" -m "OrderComponent $version"
git push origin $branch --tags

if (Get-Command gh -ErrorAction SilentlyContinue) {
  if ($env:GITHUB_TOKEN) {
    gh release create "v$version" `
      --title "OrderComponent $version" `
      --notes "Automated release for $version" `
      --generate-notes `
      --verify-tag `
      --repo $repo
  }
}

if ($env:PACKAGIST_TOKEN) {
  Invoke-RestMethod -Uri "https://packagist.org/api/update-package" `
    -Headers @{ Authorization = "token $env:PACKAGIST_TOKEN" } `
    -Method Post `
    -Body (@{repository=@{url="https://github.com/$repo"}} | ConvertTo-Json)
}
Write-Host "==> Done."
