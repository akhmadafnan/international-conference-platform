# Phase 01 — Local Environment Audit

**Phase:** 01 — Engineering Foundation  
**Branch:** `phase/01-foundation`  
**Date:** 2026-09-30  
**Status:** TOOLCHAIN GREEN / MYSQL 9.7 DETECTED / TARGET VERSION MISMATCH

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

`mysql --version` is not available through PATH, but the follow-up audit confirms an installed and running MySQL 9.7 instance.

Observed:

- Windows service: `MySQL97`;
- TCP 3306: LISTENING;
- X Protocol 33060: LISTENING;
- executable directory: `C:\\Program Files\\MySQL\\MySQL Server 9.7\\bin`;
- `mysql.exe` and `mysqld.exe` are present there.

Therefore MySQL availability is resolved; the remaining issue is **version alignment**. The frozen ICHES V1 database target is MySQL 8.4 LTS, while this laptop currently runs MySQL 9.7.

## Gate

Do not point the ICHES development environment at MySQL 9.7 as the canonical database target. Align local development with the frozen MySQL 8.4 LTS baseline first.

Frozen database baseline remains:

```text
MySQL 8.4 LTS
utf8mb4
UTC storage
Edition IANA timezone
```

## Next

1. Confirm the installed 9.7 client/server version through its absolute path.
2. Install/configure MySQL 8.4 LTS as a separate Windows service/instance without disturbing MySQL97.
3. Use a unique service name and port for the 8.4 instance (recommended project-local baseline: `MySQL84` on `3307` while MySQL97 remains on `3306`).
4. Verify MySQL 8.4 client/server/connectivity.
5. Bootstrap official Laravel 13 Vue Starter Kit directly into the real repository and point the project `.env` to the 8.4 instance.