# Current Project State

**State ID:** ICHES-STATE-20260930-ARCH-GREEN
**Status:** PRE-DEVELOPMENT — STACK + ERD FREEZE COMPLETE
**Implementation authorization:** NOT YET GRANTED
**Repository:** akhmadafnan/international-conference-platform
**Integration branch:** develop

## Completed

- Product Discovery ✓
- Pre-Presentation Product Audit ✓
- Accelerated V1 Domain Assumptions ✓
- Product Blueprint v1 ✓
- Frontend Product Specification ✓
- Corrective Phase 0 Re-baseline ✓
- Submission & Scholarly Metadata Contract ✓
- Stack + ERD Freeze ✓

## Frozen architecture

Application:
- Laravel 13 modular monolith
- PHP 8.4 target
- official Laravel Vue Starter Kit
- Inertia 3
- Vue 3 + TypeScript
- Tailwind CSS 4
- shadcn-vue
- Vue I18n
- Lucide Vue

Data/infrastructure:
- MySQL 8.4 LTS
- UUIDv7 CHAR(36) internal IDs
- human edition-scoped display/document codes
- database queue/cache/session
- private-by-default Laravel Filesystem
- Spatie Permission with Teams/Edition scope + Laravel Policies
- append-only activity audit
- Spatie Laravel PDF abstraction, Browsershot default
- server-side QR
- no Redis dependency
- no microservices
- no separate internal REST SPA architecture

## ERD result

The frozen V1 relational contract covers:
- identity/profile/membership/authorization
- series/editions/public configuration
- packages/activities/registration
- payment/proof/exceptional refunds
- canonical submissions/translations/keywords
- submission contributors/institution-first ROR affiliations
- references/files/snapshots
- screening/review/academic decisions
- rooms/sessions/presentation slots
- presentation reviewer assignment/assessment
- activity attendance/community service groups
- publication outlets/records/snapshots/handoffs/identifiers
- award candidates/finalization/recipients
- certificates/generated documents/public verification
- CMS/news/FAQ/edition documents
- notifications/jobs/audit
- number sequences

## Current sequence

PRODUCT BLUEPRINT v1 ✓
→ CORRECTIVE PHASE 0 RE-BASELINE ✓
→ SUBMISSION & SCHOLARLY METADATA CONTRACT ✓
→ STACK + ERD FREEZE ✓
→ TWO-WEEK DEVELOPMENT PLAN ← CURRENT
→ IMPLEMENTATION START GATE
→ CODING

## Architecture documents

- docs/architecture/TECH_STACK_FREEZE_V1.md
- docs/architecture/ARCHITECTURE_DECISIONS_V1.md
- docs/architecture/IMPLEMENTATION_CONVENTIONS_V1.md
- docs/architecture/ERD_V1.md
- docs/architecture/ARCH_FREEZE_AUDIT_V1.md

## Next exact action

Produce PLAN-001 — the bounded ten-working-day / two-week development plan.

No feature coding before PLAN-001 is complete.
