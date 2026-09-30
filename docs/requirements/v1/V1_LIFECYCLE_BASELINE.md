# V1 Lifecycle Baseline

**ID:** ICHES-V1-LIFE-001  
**Status:** AUTHORITATIVE V1 BASELINE  
**Updated:** 2026-09-30

## End-to-end lifecycle

```text
VISITOR
→ REGISTER
→ PROFILE
→ SELECT PARTICIPATION PACKAGE
→ PAYMENT_DUE
→ PROOF_SUBMITTED
→ FINANCE_VERIFICATION
→ REGISTRATION_CONFIRMED
→ EVENT_PASS_AVAILABLE

Optional academic path:
REGISTRATION_CONFIRMED
→ ABSTRACT_DRAFT
→ ABSTRACT_SUBMITTED
→ ADMINISTRATIVE_SCREENING
→ UNDER_REVIEW
→ ACCEPTED | REVISION_REQUIRED | REJECTED

If REVISION_REQUIRED:
→ ABSTRACT_REVISION
→ FINAL_ACADEMIC_DECISION

If ACCEPTED:
→ PRESENTATION_LOA_AVAILABLE
→ FULL_ARTICLE_PENDING
→ FULL_ARTICLE_SUBMITTED
→ ACTUAL_PRESENTER_CONFIRMED
→ READY_FOR_SCHEDULING
→ SCHEDULE_DRAFT
→ SCHEDULE_PUBLISHED
→ ATTENDANCE / CHECK-IN
→ PRESENTED | NO_SHOW
→ PRESENTATION_ASSESSMENT
→ NO_REVISION_REQUIRED | REVISION_REQUIRED

If article revision:
→ REVISED_ARTICLE_SUBMITTED
→ FINAL_ACADEMIC_CHECK

Then:
→ FINAL_ACC
→ READY_FOR_PRODUCTION
→ PROCEEDINGS | SELECTED_JOURNAL
→ PRODUCTION / HANDOFF
→ PUBLISHED or downstream external process

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
- Payment confirmation is required before starting the academic submission path.
- LoA is acceptance for presentation only.
- Full Article is required before Scheduling Pool.
- Actual Presenter is explicit and may differ from Corresponding Author.
- Draft schedule is not participant-visible.
- QR lookup is not attendance.
- Attendance is not PRESENTED.
- Reviewer recommendation is not final academic decision.
- Award candidate ranking is not Committee final decision.
- READY FOR PRODUCTION is not PUBLISHED.
- Selected for Journal is not Accepted by Journal.
- Certificate eligibility is not issuance.
