#!/usr/bin/env bash
set -euo pipefail

VERSION="${1:-0.3.0-rc}"
BRANCH="release/v$VERSION"
REPO="YOURUSER/OrderComponent"

echo "==> Preparing release $VERSION"
git checkout -B "$BRANCH"

# Auto bump version and changelog
echo "Updating composer.json version → $VERSION"
composer config version "$VERSION"

echo "Generating CHANGELOG.md section..."
DATE=$(date +%Y-%m-%d)
echo -e "## [v$VERSION] - $DATE
### Changes
" > tmp_changelog
LAST_TAG=$(git describe --tags --abbrev=0 2>/dev/null || echo "")
if [ -n "$LAST_TAG" ]; then
  git log --pretty=format:"- %s" "$LAST_TAG"..HEAD >> tmp_changelog
else
  git log --pretty=format:"- %s" >> tmp_changelog
fi
cat tmp_changelog CHANGELOG.md 2>/dev/null > CHANGELOG.tmp || cat tmp_changelog > CHANGELOG.tmp
mv CHANGELOG.tmp CHANGELOG.md
rm -f tmp_changelog

git add .
git commit -m "Release $VERSION"
git tag -a "v$VERSION" -m "OrderComponent $VERSION"
git push origin "$BRANCH" --tags

# GitHub Release
if command -v gh >/dev/null && [ -n "${GITHUB_TOKEN:-}" ]; then
  gh release create "v$VERSION"     --title "OrderComponent $VERSION"     --notes "Automated release for $VERSION"     --generate-notes --verify-tag --repo "$REPO"
fi

# Packagist notify
if [ -n "${PACKAGIST_TOKEN:-}" ]; then
  curl -sS -X POST -H "Authorization: token $PACKAGIST_TOKEN"        -d '{"repository":{"url":"https://github.com/'"$REPO"'"}}'        https://packagist.org/api/update-package || true
fi

echo "==> Done."
