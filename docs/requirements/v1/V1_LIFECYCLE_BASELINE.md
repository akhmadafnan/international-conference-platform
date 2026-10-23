# V1 Lifecycle Baseline

**ID:** ICHES-V1-LIFE-001  
**Status:** AUTHORITATIVE V1 BASELINE  
**Updated:** 2026-10-23

> **Current supersession:** The [ICHES 2027 operational rebaseline](../../governance/ICHES_2027_OPERATIONAL_REBASELINE_2026_10_23.md) governs the designated 2027 academic/review/scheduling/publication model. The [Pre-Phase 02 rebaseline](../../governance/PRE_PHASE02_PRODUCT_REBASELINE.md) remains binding: presenter payment follows ACCEPT, never abstract DRAFT or official submission. Earlier pay-before-abstract and routine mandatory-screening sequences are superseded. This describes approved behavior, not completed implementation; see [CURRENT_STATE](../../ai-context/CURRENT_STATE.md).

## End-to-end lifecycle

```text
VISITOR
→ REGISTER
→ PROFILE
→ SELECT PARTICIPATION PACKAGE

Author path (valid Edition participation intent + package; payment not required):
→ ABSTRACT_DRAFT
→ technically valid ABSTRACT_SUBMITTED with one primary Topic
→ TOPIC_REVIEW_POOL
→ exclusive atomic SELF_CLAIM + COI declaration
→ UNDER_REVIEW
→ reviewer recommendation
→ internal Academic Authority decision: ACCEPTED | REVISION_REQUIRED | REJECTED
→ official author-facing announcement/release (22 December 2026)

If REVISION_REQUIRED:
→ ABSTRACT_REVISION
→ FINAL_ACADEMIC_DECISION

If ACCEPTED:
→ PRESENTATION_LOA_AVAILABLE under approved release policy
→ parallel academic and Finance paths:
   Academic: private versioned FULL_PAPER_SUBMITTED (23–28 December)
   Finance: post-ACCEPT payment obligation / payment window (24–28 December)
            → proof / Finance verification when payable
            → REGISTRATION_CONFIRMED / EVENT_PASS as financially eligible
→ continue academic path using the SAME Submission ID:
→ ACTUAL_PRESENTER_CONFIRMED
→ READY_FOR_SCHEDULING
→ SC-approved bulk Room/Topic/Session/Presenter/Paper/Reviewer mapping
→ preview-before-apply / SCHEDULE_DRAFT
→ SCHEDULE_PUBLISHED
→ reviewer MY_ROOMS / assigned private Full Papers in presentation order
→ ATTENDANCE / CHECK-IN
→ PRESENTED | NO_SHOW recorded independently by authorized operators
→ after presentation: deep FULL_PAPER / PRESENTATION_ASSESSMENT
→ advisory NO_REVISION_REQUIRED | MINOR_REVISION_REQUIRED
   | MAJOR_REVISION_REQUIRED | NOT_RECOMMENDED

If article revision:
→ REVISED_ARTICLE_SUBMITTED
→ FINAL_ACADEMIC_CHECK

Then:
→ Academic/Publication Authority FINAL_ACC + final approved manuscript validation
→ separate authorized publication destination: PROCEEDINGS | SELECTED_JOURNAL
→ READY_FOR_PRODUCTION
→ protected publication snapshot / production bundle
→ PROCEEDINGS production or SELECTED_JOURNAL manual/assisted OJS handoff
→ downstream editorial/publication process; PUBLISHED only as an established fact

Participant-only path:
Register / Edition intent → select package → financial resolution
→ payment/proof/Finance verification when payable
→ REGISTRATION_CONFIRMED → EVENT_PASS_AVAILABLE

Parallel:
→ AWARD_CANDIDATE_EVIDENCE
→ COMMITTEE_FINAL_DECISION
→ AWARD_FINALIZED

Certificates:
ELIGIBLE
→ GENERATED
→ ISSUED
→ optional REVOKED / SUPERSEDED
```

## Core invariants

- Registration/payment status is separate from academic status.
- Rejected abstract does not cancel participant registration.
- Presenter payment is not required before abstract acceptance. Valid Edition intent and selected package permit abstract entry; post-ACCEPT payment is separate from Full Paper upload and its earlier opening date. FREE/authorized waiver remains a distinct financial resolution, not a fake payment.
- One abstract per submitting owner per Edition; only an owned DRAFT without academic evidence may be edited/deleted. Official submissions cannot be freely edited/deleted or replaced with a second Submission. Controlled revisions preserve the same identity/history; co-authorship is unaffected.
- Valid 2027 abstracts enter Topic Pool without routine mandatory Administrative PASS. Technical/scope exceptions require reasons.
- One exclusive active reviewer per paper/stage; COI-aware Self-Claim for abstracts. SC may reassign conference-stage responsibility with audit and preserve previous abstract evidence.
- Internal decisions may precede announcement; official author results and LoA follow the 22 December release policy.
- LoA is acceptance for presentation only.
- Full Article is required before Scheduling Pool.
- Actual Presenter is explicit and may differ from Corresponding Author.
- Draft schedule is not participant-visible.
- QR lookup is not attendance.
- Attendance is not PRESENTED.
- Reviewer recommendation is not final academic decision.
- Deep Full Paper review follows presentation; no revision does not create an unnecessary upload. Revision recommendation, final approval and publication destination are separate facts.
- Award candidate ranking is not Committee final decision.
- READY FOR PRODUCTION is not PUBLISHED.
- Selected for Journal is not Accepted by Journal.
- Certificate eligibility is not issuance.
