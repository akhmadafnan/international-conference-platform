# ICHES Conference & Event Experience Platform — Canonical Context

**ID:** ICHES-CANONICAL-001  
**Version:** 1.5-phase03-start
**Status:** ACTIVE  
**Updated:** 2026-10-06

## Current authority

Authority is intentionally layered.

For current session/gate state:
1. `docs/ai-context/CURRENT_STATE.md`

For current product behavior:
1. `docs/governance/PRE_PHASE02_PRODUCT_REBASELINE.md` — supersedes conflicting older payment/publication/CMS statements
2. `docs/governance/DECISION_REGISTER.md`
3. `docs/product/PRODUCT_BLUEPRINT_V1.md` where not superseded by the Pre-Phase 02 rebaseline
4. `docs/governance/ACCELERATED_V1_DOMAIN_DECISIONS.md` where not superseded by later accepted decisions
5. `docs/requirements/v1/` authoritative V1 baselines where not superseded
6. `docs/metadata/SUBMISSION_SCHOLARLY_METADATA_CONTRACT_V1.md` plus later accepted metadata amendments
7. current NFR baselines where they do not conflict with later accepted V1 decisions

For implementation:
1. `docs/architecture/ARCHITECTURE_DECISIONS_V1.md`
2. `docs/architecture/IMPLEMENTATION_CONVENTIONS_V1.md`
3. `docs/architecture/PRE_PHASE02_ARCHITECTURE_AMENDMENT.md` — supersedes only conflicting portions of ERD v1
4. `docs/architecture/ERD_V1.md` where not superseded by the amendment
5. accepted architecture decisions in `docs/governance/DECISION_REGISTER.md`

The root-level `docs/requirements/REQUIREMENT_REGISTER.md` and superseded Phase 0 requirement documents are provenance/history, not current V1 implementation authority when they conflict with the hierarchy above.

Later accepted decisions explicitly supersede earlier conflicting statements. Historical documents are preserved for traceability rather than silently rewritten.

## Product identity

Reusable multi-edition Conference & Event Experience Platform.

Not an ERP, OJS replacement, hotel/travel engine, contract lifecycle engine, or automatic academic/award decision system.

## Accelerated V1 lifecycle

Register / establish Edition participation intent
→ Select intended Package
→ Submit Abstract
→ Administrative Screening
→ Single-anonymous Review
→ Academic Decision
→ ACCEPT
→ Payment Obligation
→ Finance Verify
→ Registration Confirmed
→ Event Pass
→ Presentation LoA
→ Full Article
→ Confirm Presenter
→ Scheduling
→ Publish Schedule
→ Attendance / Presentation
→ Presentation Assessment
→ Revision / No Revision
→ Final ACC
→ Finalize for Production
→ OJS/Publication Handoff
→ Certificates / Awards

Participant-only path:
Register
→ Select Package
→ Payment Obligation
→ Finance Verify
→ Registration Confirmed
→ Event Pass

## Critical changed rules

- Presenter/author payment happens only after an authorized abstract ACCEPT decision.
- Participant-only users may pay without entering the academic submission path.
- Rejected abstract creates no presenter payment obligation; participant-only continuation remains possible when permitted.
- Payment verification confirms participation and Event Pass entitlement; it does not create academic acceptance or LoA.
- Payment destination and amount are Edition/package configurable and snapshotted when the obligation is created.
- Participation Package billing mode is explicit FREE or PAID; FREE skips payment obligation/proof/Finance verification rather than creating a fake zero-value payment.
- Authorized complimentary/fee exemption may waive a normally PAID registration with explicit reason and audit history.
- Abstract review defaults to one single-anonymous reviewer.
- One normal abstract revision cycle.
- Accelerated V1 has no separate double-anonymous publication-review engine.
- Presentation assessment drives revision/no-revision before Final ACC.
- Final Approved Manuscript is explicitly identified after revision/Final ACC.
- OJS production handoff supports single and bulk bundles from immutable publication snapshots.
- ICHES does not register DOI or deposit Crossref metadata in accelerated V1; OJS/publisher owns final publication operations.
- Proceedings is default publication destination.
- Selected Journal is an authorized override and is not journal acceptance.

## Scholarly metadata contract

- one Submission identity persists from abstract through publication;
- primary scholarly locale is separate from UI locale;
- additional scholarly translations are optional;
- contributors need not have accounts;
- legitimate single-name authors are preserved canonically;
- ORCID is optional and verification state is explicit;
- affiliation is ROR-first with truthful manual fallback;
- the canonical affiliation/ROR match is the institution or organization name;
- faculty/department/study program is optional subdivision metadata and never replaces the institutional affiliation identity;
- raw reference citation is always preserved;
- manuscript files are immutable/versioned;
- Finalize for Production creates immutable publication snapshot;
- external identifiers never become internal primary keys;
- OJS-oriented export/handoff consumes an adapter-specific projection from the canonical snapshot; direct Crossref/DOI publication operations are downstream in accelerated V1;
- readiness uses READY / WARNING / BLOCKED.

## Preserved core rules

- LoA = accepted for presentation.
- Full Article before Scheduling Pool.
- Actual Presenter explicit.
- Attendance != Presented.
- QR lookup != attendance.
- Reviewer recommendation != final decision.
- System award evidence != Committee winner.
- Eligibility != Generated != Issued.
- OJS remains downstream.
- Publication snapshot protects history.
- id/en/ar + Arabic RTL.
- server-side authorization and auditability.

## Frontend direction

Public + participant/presenter/reviewer experience may be developed in parallel with typed mocks only when authorized by the current delivery gate.

Frozen technical direction:
- Laravel 13 official Vue Starter Kit;
- Vue 3;
- TypeScript;
- Inertia;
- Tailwind;
- shadcn-vue;
- Vite;
- Vue I18n;
- Lucide Vue;
- optional Inertia SSR for public pages.

Admin/backoffice may use modern shadcn-admin-like interaction patterns, but must use ICHES domain behavior and shadcn-vue rather than importing React/template architecture.

## Frozen architecture

- Laravel 13 modular monolith
- PHP 8.4 target
- official Laravel Vue Starter Kit
- Inertia 3 + Vue 3 + TypeScript
- Tailwind CSS 4 + shadcn-vue
- MySQL 9.7 LTS
- UUIDv7 CHAR(36) internal IDs
- database queue/cache/session
- private-by-default files
- edition-scoped Spatie Permission Teams + Policies/Gates
- immutable/versioned file and publication history
- no Redis/microservices/internal REST requirement for V1

ERD contract:
- docs/architecture/ERD_V1.md

## Current project gate

Phase 01 Foundation is CLOSED_GREEN and merged into `develop`.

Phase 02 — Registration + Payment is CLOSED_GREEN and integrated into `develop` through Product Owner-approved PR #61.

The Product Owner explicitly authorized **Phase 03 — Submission + Scholarly Metadata** on 2026-10-06.

Current:
`PHASE 03 — SUBMISSION + SCHOLARLY METADATA — IN PROGRESS`

Active branch:
`phase/03-submission-metadata`

Phase 03 implements the frozen Submission & Scholarly Metadata Contract: persistent Submission identity, translations, keywords, contributors, affiliation/ROR-ready metadata, optional ORCID, references, immutable/versioned submission files, official submission snapshots, readiness boundaries, author-facing five-step abstract workflow, DRAFT/official submit transition, and server-side authorization.

The accepted Pre-Phase 02 lifecycle remains authoritative: presenter/author payment does **not** precede abstract submission. A valid Edition participation intent and selected package provide the context for abstract submission; payment obligation and Registration Confirmed occur only after authorized ACCEPT when applicable.

Phase 04 screening/review/academic decision/LoA is not part of Phase 03.

No Phase 03 aggregate PR may merge to `develop` without Phase 03 exit evidence GREEN and explicit Product Owner approval.
