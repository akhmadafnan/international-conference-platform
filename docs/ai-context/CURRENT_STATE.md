# Current Project State

**State ID:** ICHES-STATE-20261010-PHASE03-P03F-GREEN-P03G-PLAN-01
**Updated:** 2026-10-10
**Status:** PHASE 03 — SUBMISSION + SCHOLARLY METADATA — IN_PROGRESS
**Implementation authorization:** PHASE 01 COMPLETE; PHASE 02 CLOSED_GREEN; PHASE 03 AUTHORIZED / IN PROGRESS
**Repository:** akhmadafnan/international-conference-platform
**Release integration branch:** `develop` (Phase 01–02 completed)
**Active Phase 03 integration branch:** `phase/03-submission-metadata` (aggregate PR #101 targets `develop`, OPEN/DRAFT)

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

Latest integrated implementation baseline:
- `develop @ ecfb86d8624ce70ed49c08adf9e9774d14549ca1` — Product Owner-approved Phase 02 Registration + Payment integration through PR #61.
- Phase 02 source head integrated: `468425b696b1fb1c314f98db3424cf806cb7aca7`.
- Phase 02 technical exit evidence is GREEN; subsequent docs-only closeout may advance `develop` without changing this application checkpoint.

Current Phase 03 checkpoint on the **active phase branch**, not yet merged to `develop`:
- `phase/03-submission-metadata @ 4f74b67e890d84b06ba803948d1bc6719e71b887` after Product Owner-approved P03-F PR #115 merge; local Laptop 2 synchronized and clean at checkpoint.
- P03-A Issue #102 — canonical Submission/metadata persistence foundation ✓ GREEN / integrated;
- P03-B Issue #104 — DRAFT creation with Edition-scoped Paper ID ✓ GREEN / integrated;
- P03-C Issue #106 — DRAFT Details, scholarly translations and keywords ✓ GREEN / integrated;
- P03-D Issues #108 and corrective #110 — contributors, ordering, affiliations, ORCID and presenter guard ✓ GREEN / integrated;
- P03-E Issue #112 — ordered draft scholarly references ✓ GREEN / integrated;
- P03-F Issue #114, PR #115 — private PDF/DOCX abstract file ingestion and immutable versions ✓ CLOSED_GREEN / integrated;
- P03-F post-merge SQLite full regression: 260 passed, 2 MySQL-only skipped (1,686 assertions); Pint PASS and PHPStan 0 errors. Pre-merge isolated MySQL full regression 262 passed (1,699 assertions) and independent Codex review APPROVED.
- Phase 03 aggregate PR #101 remains OPEN / DRAFT / unmerged; Phase 04 remains NOT AUTHORIZED.
- P03-G Issue #116 is the next backend planning work unit; Product Owner readiness policy accepted and technical proposal recorded for review, but **no evaluator implementation or official submit/wizard UI exists yet**.
- P03-G docs-only prerequisite Issue #117 reconciles agent handoff and records accepted Edition-specific readiness policy.

Phase 01 source checkpoint:
- `d269534` — final Phase 01 branch closeout

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
- Phase 01 exit clean migration from a fresh isolated MySQL database GREEN;
- all Phase 01 migrations reported Ran from zero;
- clean-database foundational seed GREEN with 21 permissions, 0 roles, 0 users and 0 superadmins when trusted bootstrap is disabled;
- temporary Phase 01 exit database cleanup GREEN;
- frontend formatting/lint check GREEN;
- Vue TypeScript check GREEN;
- full Phase 01 CI regression GREEN: 67 passed / 250 assertions;
- full-project Pint GREEN;
- full-project PHPStan GREEN with 0 errors;
- production frontend build GREEN;
- final repository hygiene GREEN with clean worktree;
- Phase 01 implementation is integrated and synchronized on `develop` at `e8141e9`.

Local environment baseline:
- PHP 8.3.33 for current development;
- PHP 8.4 remains the V1 deployment target;
- MySQL 9.7.1 local development instance;
- `APP_TIMEZONE=UTC`;
- `DB_CHARSET=utf8mb4`;
- `DB_COLLATION=utf8mb4_0900_ai_ci`.

## Operational workflow baseline

Canonical operational workflow is defined in:

`docs/governance/ENGINEERING_WORKFLOW.md`

Current workstation convention:
- Laptop 2 application repo: `D:\PINJAM-AFNAN\Herd\international-conference-platform`;
- Laptop 2 infrastructure/tooling root: `D:\PINJAM-AFNAN\AfnanForge`;
- application development is workstation/VSCode-first and GitHub-governed;
- VPS is runtime/infrastructure, not the normal application editing surface;
- AGENT-05 is a bounded autonomous worker and must not become a non-critical blocker for ordinary product development;
- substantial checkpoints report **PRODUCT PROGRESS** separately from **INFRA / AGENT PROGRESS**.

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
- reopen Phase 01/02 foundations or start a new Phase 02 implementation without new evidence and explicit authorization; these phases are already CLOSED_GREEN.

When a new chat starts:
1. verify current branch and HEAD;
2. read `docs/ai-context/CURRENT_STATE.md`;
3. read `docs/governance/WORKING_PROTOCOL.md`;
4. read `docs/governance/ENGINEERING_WORKFLOW.md`;
5. read `docs/ai-context/INTERNATIONAL_CONFERENCE_PROJECT_CANONICAL_CONTEXT.md`;
6. load only the additional authoritative domain documents required by the current gate;
7. inspect repository divergence and active GitHub Issue/PR/Project state;
8. continue from `Current exact action` without repeating already-GREEN work.

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

1. Foundation ✓ CLOSED_GREEN
2. Registration + Payment ✓ CLOSED_GREEN / INTEGRATED
3. Submission + Metadata ← AUTHORIZED / IN PROGRESS
4. Review + Decision + LoA — NOT AUTHORIZED
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
→ PHASE 01 FOUNDATION ✓ CLOSED_GREEN
→ PR #57 / merge to develop ✓
→ META-AUDIT-001 ✓ CLOSED_GREEN
→ PRODUCT OWNER DISCUSSION / LECONFE BENCHMARK ✓
→ PRE-PHASE02 PRODUCT + ARCHITECTURE REBASELINE ✓ CLOSED_GREEN
→ EXPLICIT PRODUCT OWNER PHASE 02 GO ✓
→ PHASE 02 — REGISTRATION + PAYMENT ✓ CLOSED_GREEN
→ PR #61 / merge to develop ✓ PRODUCT OWNER APPROVED
→ PHASE 02 POST-MERGE CANONICAL HANDOFF ✓
→ EXPLICIT PRODUCT OWNER PHASE 03 GO ✓
→ PHASE 03 — SUBMISSION + SCHOLARLY METADATA ← CURRENT
→ P03-A through P03-F child tasks ✓ GREEN / integrated to Phase 03 branch
→ P03-G readiness policy ✓ PRODUCT OWNER ACCEPTED; technical plan and docs handoff in progress
→ Phase 03 submission readiness / official submit / author wizard / UAT pending
→ Phase 03 exit / integration
→ Phase 04 only after explicit new Product Owner GO
→ V1 RELEASE CANDIDATE

## Current exact action

PHASE 03 — Submission + Scholarly Metadata is AUTHORIZED and IN PROGRESS.

Authorization:
- Product Owner explicitly gave Phase 03 GO on 2026-10-06;
- Phase 02 remains CLOSED_GREEN and integrated into `develop`;
- Phase 03 active branch: `phase/03-submission-metadata`;
- aggregate Phase 03 PR #101 targets `develop`, remains OPEN / DRAFT / unmerged and human-gated;
- umbrella Issue: #63 `[P03] Submission + Scholarly Metadata`;
- Phase 04 is NOT AUTHORIZED.

Canonical Phase 03 scope:
- persistent Submission/Paper identity and Edition-scoped Paper ID;
- scholarly translations independent from UI locale;
- ordered keywords;
- contributors with explicit order and exactly one corresponding author before official submission;
- institution-first affiliation identity with ROR/manual fallback and optional subdivision;
- optional ORCID with explicit verification state;
- ordered references with raw citation preserved;
- immutable/versioned submission files and abstract-file policy;
- official submission snapshots;
- metadata readiness boundaries;
- abstract submission workflow window;
- server-side authorization;
- five-step abstract wizard;
- DRAFT → officially SUBMITTED transition;
- id/en/ar and Arabic RTL for new participant-facing surfaces.

Critical lifecycle rule:
- presenter/author abstract submission requires a valid Edition participation intent/package context, not prior payment or Registration Confirmed;
- canonical path is participation intent + selected package → Submit Abstract → Screening/Review/Decision → ACCEPT → payment obligation → Finance verification → Registration Confirmed;
- participant-only payment flow remains separate;
- administrative screening/review/academic decision/LoA are Phase 04 and must not be implemented in Phase 03.

Current bounded action:
- P03-G Issue #116 — submission metadata readiness evaluator (Step 5 backend dependency): **PRODUCT POLICY LOCKED / TECHNICAL PLAN REVIEW PENDING / NO BUILD**.
- ICHES 2027 initial Edition policy accepted: 3–5 keywords in the primary scholarly locale; Track required when that Edition uses Tracks; PDF/DOCX abstract file optional; initial V1 requires at least one affiliation per contributor (independent-author exception deferred); Edition-specific rules belong in `ConferenceEdition.settings_json`.
- The exact settings JSON keys, DTO and validation implementation remain proposed technical details, not existing code. READY/WARNING/BLOCKED has no numeric readiness score.
- Author path requires valid Edition participation intent and chosen package, **not payment or Registration Confirmed before abstract submission**; presenter fee obligation follows authorized academic ACCEPT when applicable.
- Docs-only Issue #117 updates `AGENTS.md`, `docs/ai-context/CURRENT_STATE.md`, and `docs/governance/DECISION_REGISTER.md` through a bounded child PR before P03-G backend BUILD.
- After approved handoff, review the P03-G technical scope/quality matrix, then authorize a separate bounded implementation branch. Later P03-H covers official DRAFT → SUBMITTED + snapshot/window; P03-I covers five-step author wizard with id/en/ar RTL.
- Each child returns to `phase/03-submission-metadata` only after required gates are GREEN and separate Product Owner merge approval; do not merge Phase 03 aggregate PR #101 to `develop` without its exit GREEN and explicit Product Owner approval.
- Phase 04 screening/review/decision/LoA is NOT AUTHORIZED.
