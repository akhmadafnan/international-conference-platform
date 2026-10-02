# Current Project State

**State ID:** ICHES-STATE-20261003-DEV01-AUTHZ
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

## Verified implementation checkpoint

Latest verified branch:
- `phase/01-foundation`

Latest synchronized remote checkpoint:
- `d0eead6` — edition-scoped authorization and audit foundation
- `dff27ac` — bootstrap cache runtime-artifact hygiene
- `c257985` — UTC timezone foundation
- `a1021c6` — compiled framework view cleanup
- `ce1063e` — UUIDv7 identity foundation

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
- latest engineering regression reported GREEN after migration static-analysis normalization;
- Pint GREEN;
- PHPStan GREEN;
- Git diff check GREEN;
- worktree synchronized with `origin/phase/01-foundation`.

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

Laravel/Vue application baseline, Authentication V1, MySQL baseline, UUIDv7 identity, UTC persistence, Spatie Permission Teams infrastructure, UUID-aware Activity Log, Active Conference Edition permission context, and global superadmin authorization enforcement are GREEN.

Authorization baseline now follows:

`Superadmin → global authorization bypass`

or, for normal users:

`Active Conference Edition → Spatie Permission → Policy/Gate → Domain Action`

Superadmin bypasses ordinary authorization only. Validation, domain invariants, state-transition rules, database constraints, transactions, immutable/versioned history, and audit requirements remain mandatory.

Continue the remaining Phase 01 Foundation scope:
- Vue I18n id/en/ar shell;
- Arabic RTL shell;
- ICHES design-token foundation;
- private/public filesystem foundation;
- foundational seeders;
- final Phase 01 clean-migration and regression gate.

Concrete domain Policies will be implemented with their real domain models beginning in Phase 02 rather than creating placeholder Policies in Foundation.

Do not begin Phase 02 / Day 2 conference business features until the Phase 01 Foundation exit gate is GREEN.
