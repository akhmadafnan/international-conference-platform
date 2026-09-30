# Corrective Phase 0 Re-baseline — Accelerated V1

**Document ID:** ICHES-GOV-CORR-001  
**Status:** COMPLETE  
**Updated:** 2026-09-30  
**Basis:** Product Blueprint v1 + Accelerated V1 Domain Decisions

## Purpose

The initial Phase 0 baseline contained several decisions that were later changed during Product Discovery. This corrective re-baseline establishes the current V1 product truth without deleting useful historical work.

Historical Phase 0 documents remain in Git for traceability. Where they conflict with the V1 baseline documents in `docs/requirements/v1/`, the V1 documents are authoritative.

## Reconciliation result

| Domain | Old baseline | Current V1 baseline | Result |
|---|---|---|---|
| Payment timing | Draft abstract → payment → official submission | Registration/package → payment → confirmed participant → abstract submission | SUPERSEDED |
| Academic rejection refund | Automatic 100% refund | Participant remains active; no automatic academic-rejection refund | SUPERSEDED |
| Submission entry | Draft may exist before payment | Academic submission begins after registration/payment confirmation | SUPERSEDED |
| Abstract review | Flexible stage/round engine; variable reviewer architecture | V1 simple single-anonymous, 1 reviewer default, 1 normal revision cycle | SIMPLIFIED |
| Full Article | After abstract acceptance | Same | PRESERVED |
| LoA | Presentation acceptance | Same | PRESERVED |
| Scheduling | After Full Article/presenter readiness | Same, explicitly only Full Article submissions enter pool | PRESERVED/CLARIFIED |
| Attendance vs Presented | Separate facts | Same | PRESERVED |
| Publication review | Separate post-presentation double-anonymous publication review default | Presentation assessment → revision/no revision → Final ACC; no separate formal publication peer-review engine in accelerated V1 | SUPERSEDED |
| Publication gate | Formal publication eligibility concept with review engine | READY/WARNING/BLOCKED metadata/readiness + Final ACC + destination | SIMPLIFIED |
| OJS | Downstream handoff | Same | PRESERVED |
| Awards | Not central in old baseline | Candidate evidence + Committee final decision | ADDED/CLARIFIED |
| Certificates | Strong audit/reissue model | Same, plus explicit eligibility rules | PRESERVED/CLARIFIED |
| Authorities | Detailed permission model | Preserved in principle; simplified authority map used for V1 | PRESERVED/SIMPLIFIED |
| NFR | Security/privacy/reliability/accessibility/RTL/etc. | Still valid unless directly coupled to superseded lifecycle behavior | PRESERVED |

## Current authoritative V1 requirement set

1. `docs/product/PRODUCT_BLUEPRINT_V1.md`
2. `docs/governance/ACCELERATED_V1_DOMAIN_DECISIONS.md`
3. `docs/requirements/v1/V1_LIFECYCLE_BASELINE.md`
4. `docs/requirements/v1/V1_PAYMENT_REFUND_BASELINE.md`
5. `docs/requirements/v1/V1_ACADEMIC_REVIEW_BASELINE.md`
6. `docs/requirements/v1/V1_PUBLICATION_BASELINE.md`
7. `docs/requirements/v1/V1_AUTHORITY_BASELINE.md`
8. current NFR baseline documents that do not conflict with the above
9. historical Phase 0 requirement documents for provenance only

## Historical documents explicitly superseded for V1 behavior

- `docs/requirements/PAYMENT_BASELINE.md`
- `docs/requirements/REFUND_BASELINE.md`
- `docs/requirements/LIFECYCLE_BASELINE.md`
- `docs/requirements/ACADEMIC_REVIEW_BASELINE.md`
- `docs/requirements/REVIEW_STAGE_BASELINE.md`
- `docs/requirements/PUBLICATION_REVIEW_ELIGIBILITY_BASELINE.md`

## Preserved requirements

The corrective re-baseline preserves earlier work concerning security, server-side authorization, least privilege, COI protection, audit trails, privacy/PII, Finance data separation, accessibility, id/en/ar localization, Arabic RTL, reliability, file/document integrity, maintainability/testing, external identifiers, OJS-downstream boundaries, publication snapshots, and certificate revoke/reissue integrity.

## V1 simplification

Accelerated V1 does not implement the old generic multi-stage/multi-round publication review engine.

V1 uses:
- abstract review: single-anonymous;
- one reviewer default;
- one normal revision cycle;
- presentation assessment after presentation;
- revision/no revision;
- final academic approval;
- publication readiness/handoff.

The architecture may remain extensible, but V1 implementation must not pay the complexity cost of unused workflow flexibility.

## Gate result

Corrective Phase 0 Re-baseline: **GREEN / COMPLETE**

Next:
→ Submission & Scholarly Metadata Contract.
