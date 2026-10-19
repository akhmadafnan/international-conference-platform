# ICHES — Local Edition Personas & Superadmin Workspace View

Status: implementation candidate; **no real MySQL/demo user provisioning has been performed**.

## Canonical role semantics

The canonical role vocabulary is `config/foundation.php`. Spatie Roles are Edition-scoped for: front_office, conference_admin, finance, academic_committee, academic_decision_authority, event_operations, publication_team, certificate_authority.

Participant/Author/Presenter/Reviewer/Session Chair/Moderator/Invited Speaker are NOT unconditionally granted Edition-wide authority. Their roles arise from membership, related submission, assigned review/session or speaker record. The demo fixture creates identifiable accounts and *only* six membership identities, without fabricating academic review, submission, event-day facts, payment or privileged role assignments.

## Opt-in fixture only (Laptop 2 local or isolated testing)

The fixture is NOT part of `DatabaseSeeder`. It will refuse to execute unless all requirements are met:

- `APP_ENV` is `local` or `testing`;
- `ICHES_DEMO_ROLE_SEED_ENABLED=true`;
- `ICHES_DEMO_EDITION_CODE` names an Edition that **already exists**;
- `ICHES_DEMO_ROLE_PASSWORD` is a deliberately supplied strong password, never stored in repository files.

PowerShell example for an *authorized test database only*, after confirming MySQL target and taking a backup if needed:

```powershell
$env:ICHES_DEMO_ROLE_SEED_ENABLED = 'true'
$env:ICHES_DEMO_EDITION_CODE = 'ICHES-2027'
$secret = Read-Host 'One-time demo password (12+ mixed case, digits, symbols)' -AsSecureString
$env:ICHES_DEMO_ROLE_PASSWORD = [System.Net.NetworkCredential]::new('', $secret).Password
php artisan db:seed --class=EditionPersonaDemoSeeder

Remove-Item Env:ICHES_DEMO_ROLE_PASSWORD
Remove-Item Env:ICHES_DEMO_ROLE_SEED_ENABLED
Remove-Item Env:ICHES_DEMO_EDITION_CODE
```

Do not run blindly against production or a real participant database. If the existing configuration is cached, inspect it first; do not change configuration or migration settings solely to execute this fixture. Fixture email pattern: `demo+<persona>.<edition-code>@example.test`. Names are `ICHES Demo · <persona>`. The seeder is idempotent and refuses to commandeer an existing differently named account at a reserved address.

No fixed passwords, Bank credentials, real participant records or actual academic assignments are included.

## Superadmin view switch

The existing global Superadmin account (e.g. `superadmin@unisya.ac.id`) can choose **Switch workspace** from the sidebar footer. This is *not impersonation*. It does not change their account ID, database roles or Gate/Policy authority. The original session remembers the visual mode; changing it creates a security audit activity. Ordinary users cannot call the switch endpoint.

- Superadmin mode goes to the operational Lyra admin dashboard.
- Participant, Author, Co-author and Presenter modes use the existing own-account participant Dashboard (no fabricated own submissions).
- Other actor modes show an explicit non-operational overview until their role-specific modules exist. This is a truthful preview, not a completed feature.
- Real permission/UAT for staff/reviewer must still use the respective scoped demo login and an actual authorized Edition/assignment. The Superadmin view switch is **not** a substitute for least-privilege actor testing.

## Before integration

Run scoped tests, admin Lyra contract check, Vue TypeScript, Vite build and PHPStan. Confirm UI in id/en/ar, dark/light, RTL, and logout/login/session boundary. No merge or demo seeding to canonical MySQL until PO signs the change.
