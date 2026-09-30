# Phase 01 — Local Environment Audit

**Phase:** 01 — Engineering Foundation  
**Branch:** `phase/01-foundation`  
**Date:** 2026-09-30  
**Status:** TOOLCHAIN GREEN / MYSQL AVAILABILITY BLOCKED

## Verified local toolchain

| Component | Result |
|---|---|
| PHP | 8.4.24 — GREEN |
| Composer | 2.10.1 — GREEN |
| Node.js | 26.4.0 — GREEN |
| npm | 11.17.0 — GREEN via `npm.cmd` |
| npx | 11.17.0 — GREEN via `npx.cmd` |
| Git | 2.55.0.windows.2 — GREEN |
| Working branch | `phase/01-foundation` — GREEN |
| Working tree | clean — GREEN |

## PHP runtime

Loaded PHP configuration:

```text
C:\Users\MCN Broadcast\.config\herd\bin\php84\php.ini
```

Required/relevant extensions observed:

- bcmath
- curl
- fileinfo
- gd
- intl
- mbstring
- mysqli
- mysqlnd
- openssl
- PDO
- pdo_mysql
- pdo_pgsql
- pdo_sqlite
- zip

PHP MySQL connectivity support is therefore present.

## MySQL status

`mysql --version` is not currently available and `where.exe mysql` returns no executable.

This does **not** prove that no MySQL server exists; it proves only that no MySQL client executable is currently discoverable through PATH.

Before installation, verify:

- Windows MySQL services;
- port 3306 listeners;
- Herd-managed services if Herd Pro is active;
- common MySQL installation paths.

## Gate

Do not scaffold the production Laravel application until MySQL 8.4 availability is resolved or deliberately installed/configured.

Frozen database baseline remains:

```text
MySQL 8.4 LTS
utf8mb4
UTC storage
Edition IANA timezone
```

## Next

1. Audit existing MySQL service/install.
2. If absent, install/configure MySQL 8.4 LTS.
3. Verify client/server/version/connectivity.
4. Bootstrap official Laravel 13 Vue Starter Kit directly into the real repository.
