# CI additions for α-4
Add steps before running PHPUnit:
```yaml
- name: Lint Container
  run: php bin/console lint:container

- name: Doctrine Schema Validate
  run: php bin/console doctrine:schema:validate --skip-sync
```
