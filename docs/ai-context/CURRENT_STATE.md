# Current Project State

**State ID:** ICHES-STATE-20261003-DEV01-SEED
**Status:** DEVELOPMENT — PHASE 01 FOUNDATION IN PROGRESS  
**Implementation authorization:** GRANTED  
**Repository:** akhmadafnan/international-conference-platform  
**Integration branch:** develop  
**Active implementation branch:** phase/01-foundation

## Completed

- Product Discovery ✓
- Pre-Presentation Product Audit ✓
- Accelerated V1 Domain Assumptions ✓
- Product Blueprint v1 ✓
- Frontend Product Specification ✓
- Corrective Phase 0 Re-baseline ✓
- Submission & Scholarly Metadata Contract ✓
- Stack + ERD Freeze ✓
- Two-Week Development Plan ✓
- Implementation Start Gate ✓

- Laravel 13 + Vue application baseline ✓
- Authentication V1 baseline ✓
- MySQL database baseline ✓
- UUIDv7 identity foundation ✓
- Spatie Activitylog UUID-aware audit infrastructure ✓
- UUIDv7 Role/Permission security identities ✓
- Spatie Permission edition-scoped authorization infrastructure ✓
- UTC persistence timezone foundation ✓
- Global superadmin authority contract frozen ✓
- Active Conference Edition authorization context ✓
- Global superadmin authorization enforcement ✓
- Vue I18n id/en/ar application shell ✓
- Arabic RTL application-shell behavior ✓
- ICHES design-token and appearance foundation ✓
- Private/public filesystem foundation ✓
- Foundational access and trusted superadmin seeders ✓

## Verified implementation checkpoint

Latest verified branch:
- `phase/01-foundation`

Latest synchronized remote checkpoint:
- `ff55a69` — foundational access and trusted superadmin seeders
- `ac6d877` — filesystem foundation gate closeout
- `27ef26f` — private/public filesystem foundation
- `9f6c6a6` — ICHES design-token and appearance foundation

Verified implementation state:
- Laravel 13 + Vue/Inertia application bootstrap GREEN;
- Authentication V1 GREEN;
- MySQL connectivity and clean migrations GREEN;
- UUIDv7 User identity GREEN;
- UTC persistence baseline GREEN;
- Spatie Permission 8.0 installed and configured;
- Teams enabled with `conference_edition_id`;
- User, Role, Permission and authorization pivot identities are UUID-compatible;
- Spatie Activitylog 4.12 installed with UUID-compatible subject/causer morphs;
- edition-scoped authorization behavior tested;
- Activity Log UUID causer/subject behavior tested;
- private/public filesystem boundary established with `private` as the canonical default disk;
- protected application files resolve under `storage/app/private` without direct public URL/serve exposure;
- public files remain isolated under `storage/app/public` and are exposed only through `public/storage`;
- Laravel `local` compatibility disk remains private;
- storage foundation focused regression GREEN: 4 passed / 16 assertions;
- full regression GREEN: 61 passed / 215 assertions;
- Pint GREEN;
- PHPStan GREEN with 0 errors;
- Git diff check GREEN;
- operational `storage:link` verification GREEN;
- canonical foundational permission vocabulary seeded idempotently;
- canonical payment verification capability normalized to `payment.verify`;
- edition-scoped business Role rows are intentionally not seeded before real Conference Edition records exist;
- trusted initial global superadmin bootstrap uses `users.is_super_admin`, not a Spatie role;
- initial superadmin bootstrap is disabled by default and accepts credentials only through environment-backed configuration;
- rerunning the bootstrap does not duplicate the account or reset an existing password;
- default Laravel `test@example.com` seeding path removed;
- foundational seeder focused tests GREEN: 6 passed / 35 assertions;
- authorization + superadmin + seeder related regression GREEN: 16 passed / 72 assertions;
- scoped Pint GREEN;
- scoped PHPStan GREEN with 0 errors;
- isolated SQLite seed UAT GREEN with 21 permissions, 0 roles, 1 user, 1 superadmin and 1 bootstrap activity after repeated seeding;
- temporary seed-UAT database cleanup GREEN;
- worktree synchronized with `origin/phase/01-foundation` at `ff55a69`.

Local environment baseline:
- PHP 8.3.33 for current development;
- PHP 8.4 remains the V1 deployment target;
- MySQL 9.7.1 local development instance;
- `APP_TIMEZONE=UTC`;
- `DB_CHARSET=utf8mb4`;
- `DB_COLLATION=utf8mb4_0900_ai_ci`.

## Handoff and no-repeat rule

A new development session must orient from the repository and this file before proposing implementation.

Do not repeat or rebuild already-GREEN foundation work unless repository evidence or regression proves it broken.

Specifically, do not:
- reinstall the Laravel application skeleton;
- recreate Authentication V1;
- revert UUIDv7 to integer IDs;
- republish/reinstall Permission or Activitylog without a package-change reason;
- recreate the already-published authorization/audit migrations;
- reintroduce compiled `storage/framework/views` or `bootstrap/cache` runtime artifacts into Git;
- start Phase 02 business features before the remaining Phase 01 exit gate is GREEN.

When a new chat starts:
1. verify current branch and HEAD;
2. read `CURRENT_STATE.md`;
3. read `IMPLEMENTATION_CONVENTIONS_V1.md`;
4. inspect only repository divergence from this checkpoint;
5. continue from `Current exact action` rather than redoing prior gates.

## Frozen implementation baseline

Backend:
- PHP 8.4 target runtime; local development currently PHP 8.3.33
- Laravel 13 modular monolith
- MySQL 9.7 LTS
- UUIDv7
- edition-scoped authorization
- database queue/cache/session
- private-by-default storage
- audit trail
- PDF/QR support

Frontend:
- official Laravel Vue Starter Kit
- Vue 3 + TypeScript
- Inertia 3
- Tailwind CSS 4
- shadcn-vue
- Vite
- Vue I18n
- Lucide Vue
- id/en/ar + RTL

## Delivery plan

10 working days:

1. Foundation ← CURRENT
2. Registration + Payment
3. Submission + Metadata
4. Review + Decision + LoA
5. Full Article + Scheduling
6. Event Day + Assessment
7. Revision + Publication
8. Awards + Certificates + Documents
9. Integration + Regression
10. Release Candidate + Deployment/UAT

Days 9–10 are protected stabilization days.

## Current sequence

PRODUCT / REQUIREMENTS / META / ARCH ✓
→ TWO-WEEK DEVELOPMENT PLAN ✓
→ IMPLEMENTATION START GATE ✓
→ phase/01-foundation ← CURRENT
→ bounded daily development gates
→ V1 RELEASE CANDIDATE

## Current exact action

Phase 01 Foundation remains in progress.

GREEN implementation baseline now includes:
- Laravel 13 + Vue/Inertia application foundation;
- Authentication V1;
- MySQL + UUIDv7 + UTC persistence;
- edition-scoped authorization and audit infrastructure;
- global superadmin authorization enforcement;
- Vue I18n `id/en/ar`;
- Arabic RTL application shell;
- ICHES design-token and appearance foundation;
- private/public filesystem foundation;
- foundational permission vocabulary and trusted global-superadmin bootstrap seeders.

ICHES appearance baseline:
- first visit defaults to Light mode;
- Light/Dark is selectable without authentication;
- explicit appearance preference persists;
- System appearance remains available as an optional user preference;
- institutional green is the primary UI, interaction, and focus identity;
- orange is reserved for secondary branding/accent use rather than primary interaction;
- semantic design tokens support Light and Dark;
- reduced-motion handling is part of the global UI foundation;
- Browser UAT is GREEN for Light, Dark, responsive shell, locale switching, and Arabic RTL.

Remaining Phase 01 Foundation scope:
- final Phase 01 clean-migration and regression exit gate.

Foundational seeders are now CLOSED_GREEN. The initial global superadmin bootstrap is trusted and idempotent, uses `users.is_super_admin`, is disabled by default, and receives credentials only through environment-backed configuration. Public registration cannot assign `is_super_admin`.

Filesystem foundation gate:
`F01-STORAGE-001 — CLOSED_GREEN`

Foundational seeder gate:
`F01-SEED-001 — CLOSED_GREEN`

Current next gate:
`F01-EXIT-001 — Final Phase 01 Clean Migration & Regression Exit Gate`

Do not begin Phase 02 business features until the remaining Phase 01 exit gate is GREEN.
