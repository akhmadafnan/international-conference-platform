# ICHES Conference & Event Experience Platform — Canonical Context

**ID:** ICHES-CANONICAL-001  
**Version:** 1.6-iches2027-operational-rebaseline
**Status:** ACTIVE  
**Updated:** 2026-10-23

> **Current ICHES 2027 operational authority:** Read the [2026-10-23 Operational Rebaseline](../governance/ICHES_2027_OPERATIONAL_REBASELINE_2026_10_23.md), [Decision Register](../governance/DECISION_REGISTER.md) PD-085–PD-096 and [current checkpoint](CURRENT_STATE.md). Technically valid abstracts enter Topic Pool/exclusive COI-aware Self-Claim without routine mandatory screening; SC-approved Room/session mappings control stage-specific conference responsibility. Deep Full Paper review follows presentation; revision, Final ACC, destination and journal acceptance remain separate. This model is approved design, not delivered Self-Claim/Room Mapping. Nonconflicting reusable principles below remain binding.

## Current authority

Authority is intentionally layered.

For current session/gate state:
1. `docs/ai-context/CURRENT_STATE.md`

For current product behavior:
1. [ICHES 2027 Operational Rebaseline](../governance/ICHES_2027_OPERATIONAL_REBASELINE_2026_10_23.md) for its designated Edition scope
2. `docs/governance/PRE_PHASE02_PRODUCT_REBASELINE.md` — binding payment-after-ACCEPT/publication/CMS supersession
3. `docs/governance/DECISION_REGISTER.md`, including later accepted scoped amendments
4. `docs/product/PRODUCT_BLUEPRINT_V1.md` where not superseded
5. `docs/governance/ACCELERATED_V1_DOMAIN_DECISIONS.md` where not superseded by later accepted decisions
6. `docs/requirements/v1/` authoritative V1 baselines where not superseded
7. `docs/metadata/SUBMISSION_SCHOLARLY_METADATA_CONTRACT_V1.md` plus later accepted metadata amendments
8. current NFR baselines where they do not conflict with later accepted V1 decisions

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
→ Technically valid submission enters primary Topic Review Pool
→ Exclusive atomic Self-Claim + COI
→ Single-anonymous Review / advisory recommendation
→ Internal Academic Authority Decision
→ Official results / LoA release under 22 December 2026 policy
→ ACCEPTED paper continues under the SAME Submission ID
→ Parallel: private Full Paper upload/versioning (23–28 December)
  and post-ACCEPT presenter payment (24–28 December), Finance resolution / Event Pass
→ Confirm Presenter
→ SC-approved bulk Room/Topic/Session/Paper/Reviewer mapping, preview-before-apply
→ Publish Schedule
→ My Rooms / assigned Full Papers ordered by presentation schedule
→ Attendance / Presentation
→ Post-presentation deep Full Paper / presentation assessment
→ Revision / No Revision
→ Academic/Publication Final ACC / final approved manuscript
→ Separate authorized Proceedings / Selected Journal routing
→ Finalize for Production
→ OJS/Publication Handoff
→ Certificates / Awards

The official event is 5 January 2027 and proceedings target is 28 January. Journal timelines are independent. Full Paper upload must not wait for the later opening of the payment window. One abstract per submitting owner per Edition; controlled correction/revision preserves identity/history and does not prohibit co-authorship.

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
- ICHES 2027 requires one exclusive active responsible reviewer per paper/stage, single-anonymous. Topic interest/competency creates no per-Topic authorization role. SC conference-stage reassignment preserves abstract review history; only current assignments grant private-file access.
- One normal abstract revision cycle.
- Accelerated V1 has no separate double-anonymous publication-review engine.
- Presentation assessment drives revision/no-revision before Final ACC.
- Final Approved Manuscript is explicitly identified after revision/Final ACC.
- OJS production handoff supports single and bulk bundles from immutable publication snapshots.
- ICHES does not register DOI or deposit Crossref metadata in accelerated V1; OJS/publisher owns final publication operations.
- Proceedings is default publication destination.
- Selected Journal is an authorized override and is not journal acceptance.
- Author-facing unreleased decisions/LoA must remain private until the approved official release. Reviewer outcomes and destination suggestions are advisory, distinct from human final decisions and routing.
- The ten proposed journal candidates and supplied SINTA tiers in the operational document remain pending identity/title/ISSN/accreditation/OJS/partnership/slot verification; do not fabricate publication agreements.

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

For current authenticated UI, Lyra V1.1 + Fluid Workspace remain authoritative. Preserve id/en/ar, Arabic RTL, light/dark and private Edition-scoped data. Google Login is postponed until near-production; do not change authentication now.

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

Use [CURRENT_STATE](CURRENT_STATE.md) for the complete 2026-10-23 checkpoint. Phase03 Product Owner browser UAT PASS is accepted at local checkpoint `d845d24`; active Laptop 2 branch is `agent/phase04-academic-core`. Phase04 Academic Core code exists with incomplete full end-to-end UAT, in a large uncommitted working tree. MySQL Phase04 migration batch 2 and UNIQUE submission registration batch 3 RAN; duplicate groups are zero, Paper #0001 remains SUBMITTED and legacy DRAFT #0002 was removed. Lyra fluid workspace and Academic Workspace/Paper #0001 screening UAT PASS are scoped existing-code evidence.

Existing mandatory screening/email assignment code has not implemented the approved Self-Claim/Room model. Follow the [bounded roadmap](../project-management/ICHES_2027_EXECUTION_ROADMAP.md); the next unit needs its own scoped plan/ready gate. This documentation task authorizes no feature code, destructive database reset, branch switch, merge, push or PR. Preserve all existing local edits; do not infer merged, pushed or production-ready state.

Historical provenance: Phase01/02 integrated into develop; Phase03 authorization and phase-branch delivery rules remain traceable in CURRENT_STATE history. Phase03 metadata scope does not itself deliver Phase04 academic workflows. Remote integration/aggregate PR acceptance still requires explicit Product Owner authorization and evidence, not an inference from local browser UAT.
