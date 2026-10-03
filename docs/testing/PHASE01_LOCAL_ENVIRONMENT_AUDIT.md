# Phase 01 — Local Environment Audit

**Phase:** 01 — Engineering Foundation  
**Branch:** `phase/01-foundation`  
**Date:** 2026-09-30  
**Status:** TOOLCHAIN GREEN / MYSQL 9.7 LTS ACCEPTED

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

Therefore MySQL availability and version alignment are both resolved. The installed MySQL 9.7.0 server is the accepted ICHES V1 local database baseline.

## Gate

Use the existing MySQL97 service on 127.0.0.1:3306 for ICHES local development. A second MySQL service/port is not required.

Frozen database baseline remains:

```text
MySQL 9.7 LTS
utf8mb4
UTC storage
Edition IANA timezone
```

## Next

1. Verify application credentials/connectivity to the existing MySQL97 service on 127.0.0.1:3306.
2. Create a dedicated ICHES database (and preferably a dedicated database user) in the existing server.
3. Bootstrap official Laravel 13 Vue Starter Kit directly into the real repository and point the project `.env` to the existing MySQL97 instance.