# ICHES Conference & Event Experience Platform — Canonical Context

**ID:** ICHES-CANONICAL-001  
**Version:** 1.2-meta-contract  
**Status:** ACTIVE  
**Updated:** 2026-09-30

## Current authority

The current product truth is:
- Product Blueprint v1;
- Accelerated V1 Domain Decisions;
- V1 requirement baselines in docs/requirements/v1/;
- Submission & Scholarly Metadata Contract in docs/metadata/;
- current non-conflicting NFR baselines.

Initial Phase 0 lifecycle/payment/publication documents are historical when they conflict.

## Product identity

Reusable multi-edition Conference & Event Experience Platform.

Not an ERP, OJS replacement, hotel/travel engine, contract lifecycle engine, or automatic academic/award decision system.

## Accelerated V1 lifecycle

Register
→ Select Package
→ Pay
→ Finance Verify
→ Registration Confirmed
→ Event Pass
→ Submit Abstract
→ Administrative Screening
→ Single-anonymous Review
→ Academic Decision
→ Presentation LoA
→ Full Article
→ Confirm Presenter
→ Scheduling
→ Publish Schedule
→ Attendance / Presentation
→ Presentation Assessment
→ Revision / No Revision
→ Final ACC
→ Ready for Production
→ Proceedings / Selected Journal
→ Certificates / Awards

## Critical changed rules

- Payment happens before the academic submission path.
- Rejected abstract remains participant.
- Academic rejection has no automatic refund.
- Abstract review defaults to one single-anonymous reviewer.
- One normal abstract revision cycle.
- Accelerated V1 has no separate double-anonymous publication-review engine.
- Presentation assessment drives revision/no-revision before Final ACC.
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
- OJS and Crossref consume adapter-specific projections from the canonical snapshot;
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

Public + participant/presenter/reviewer experience may be developed in parallel with typed mocks.

Preferred technical direction, pending formal Stack Freeze:
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

## Current next gate

Stack + ERD Freeze.