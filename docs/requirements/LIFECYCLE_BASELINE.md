# End-to-End Conference Lifecycle Baseline

**ID:** ICP-REQ-LIFE-001  
**Status:** PHASE 0 — PARTIALLY APPROVED  
**Updated:** 2026-09-23

This document is the lifecycle-specific source used to validate the full conference journey.

## Stage map

```text
4A Registration / Profile / Join Edition / Submission Entry     APPROVED
4B Abstract Draft / Manual Payment / Finance Verification        APPROVED
4C Academic Processing / Decision / Refund                       APPROVED
4D Full Paper / LoA / Scheduling / Presentation                  APPROVED
4E Post-Presentation Revision / Publication Gate                 NEXT
4F OJS / Publication / Certificate / Archive                     PENDING
```

## Stage 4A — Approved

### Entry

```text
Visitor
→ choose edition
→ Register / Join
→ email
→ existing account authenticate OR new account verify email
→ complete/update global profile
→ join edition
→ edition membership active
→ Participant Only OR Submit Paper
→ when Submit Paper: create DRAFT submission
```

### Approved rules

1. **Account reuse across editions.** The same verified account persists across editions.
2. **Verified email before active membership.**
3. **Global profile.** Current identity/contact/current affiliation/preferred locale may be reused; ORCID remains optional.
4. **Edition membership.** Joining an edition creates edition-specific membership and roles, not a duplicate account.
5. **Flexible participation path.** Participant-only may later become Author while the edition still permits submission.
6. **Multiple submissions are policy-driven.** Edition configuration determines limits.
7. **Draft is not official.** Starting a paper creates a DRAFT submission only.
8. **Historical integrity.** Later profile changes must not retroactively rewrite accepted historical edition/submission metadata.
9. **No payment yet.** Account creation, profile completion, edition membership, and draft creation do not by themselves trigger payment.

## Conceptual identity/history model

```text
PERSON / CURRENT PROFILE
├── reusable account
├── verified email
├── current contact/profile
└── ORCID optional
        │
        ├── EDITION 2027 MEMBERSHIP
        │    ├── roles
        │    └── historical snapshots where required
        │
        └── EDITION 2029 MEMBERSHIP
             ├── roles
             └── historical snapshots where required
```

Exact snapshot tables/fields are deferred to domain/data design.

## Stage 4B — Approved: Manual Payment to Official Submission

```text
DRAFT
→ Submit & Proceed to Payment
→ validation
→ AWAITING_PAYMENT
→ manual bank transfer
→ upload proof
→ PAYMENT_SUBMITTED
→ Finance cross-check
   ├─ verified → PAID → OFFICIAL_SUBMISSION
   └─ not verified → PAYMENT_ACTION_REQUIRED → author correction/re-upload
```

### Approved rules

1. V1 payment is manual bank transfer.
2. No payment gateway is required for V1.
3. Payment starts only after draft validation/submit intent.
4. Amount due comes from edition policy/configuration.
5. Uploading proof is not payment verification.
6. Only authorized Finance personnel may verify payment.
7. Finance cross-checks the actual incoming transfer outside the platform.
8. Finance must record the verification result and reason.
9. Only `PAID` transitions to `OFFICIAL_SUBMISSION`.
10. `PAID` never means academically accepted.
11. Before successful payment, submit intent may return to draft subject to deadline/policy.
12. After payment, withdrawal is a formal workflow.
13. Official submission creates a stable submitted-version snapshot.
14. Substantive post-submission edits require controlled revision/correction.
15. Payment proof and verification history are auditable.
16. FO may assist with status/guidance but cannot verify payment.
17. Sensitive bank/mutation data is Finance-restricted.
18. The payment domain remains future-provider-ready without requiring a gateway in V1.

## Stage 4C — Approved: Academic Decision and Refund

```text
OFFICIAL_SUBMISSION
→ ADMINISTRATIVE_SCREENING
   ├─ correction required
   ├─ administratively ineligible
   └─ pass
        → ACADEMIC_PROCESSING
           ├─ ACCEPTED
           ├─ REVISION_REQUIRED → new abstract version → re-check
           └─ REJECTED → REFUND_ELIGIBLE → Finance manual refund → REFUNDED
```

### Approved rules

1. Administrative screening precedes academic processing.
2. Administrative and scholarly judgments are separate.
3. Academic-processing method is configurable by edition.
4. Abstract decisions: ACCEPTED, REVISION_REQUIRED, REJECTED.
5. Abstract revision creates a new traceable version.
6. Academic authority and Finance authority are separate.
7. Academic rejection automatically creates refund eligibility.
8. V1 academic rejection refund = **100% of conference fee actually paid**.
9. Refund is executed manually by Finance.
10. Refund execution and proof/history are auditable.
11. Author withdrawal follows separate edition refund policy.
12. Administrative ineligibility follows separate edition refund policy.
13. FO cannot change decisions or mark refunds complete.
14. Payment/submission/decision history remains preserved after refund.

### Review-model note

Abstract review does **not** need to be double-blind at platform level. Exact first-edition mode remains open/configurable.

Candidate later publication workflow:
```text
Presentation
→ Full Paper / Final Manuscript
→ separate publication-quality review (mode still OPEN)
→ revision/approval
→ publication eligibility
```

The later full-paper review may be single-anonymous, double-anonymous, committee review, or another documented model. This is intentionally not locked yet.

## Next — Stage 4D

The next review must decide:
- full-paper requirement/timing;
- Letter of Acceptance timing;
- schedule/session allocation;
- presenter designation;
- attendance/presentation evidence;
- no-show behavior;
- whether non-presenting accepted papers can proceed;
- relationship between presentation and later publication-quality review.


## Stage 4D — Approved: Full Paper, LoA, Scheduling, Presentation

```text
ABSTRACT_ACCEPTED
→ LoA
→ FULL_PAPER_PENDING
→ FULL_PAPER_SUBMITTED
→ FULL_PAPER_VALIDATION
→ PRESENTER_CONFIRMED
→ PRESENTATION_READY
→ SCHEDULED
→ CHECKED_IN
→ PRESENTED | NO_SHOW
```

### Approved rules

1. Abstract acceptance/LoA = acceptance for presentation, not publication.
2. Full Paper is required after acceptance by an edition-configurable deadline.
3. Pre-conference Full Paper validation is administrative/format validation.
4. Full Paper versions are traceable.
5. Presenter is explicitly designated and confirmed.
6. Session and Presentation Slot are distinct scheduling concepts.
7. Schedule has draft/published lifecycle.
8. Attendance and presentation statuses are separate.
9. Presentation is verified by an authorized event/session role.
10. NO_SHOW blocks publication by default.
11. Makeup/waiver exceptions require authority and audit trail.
12. Attendance/presentation evidence supports simple V1 operation and future QR/barcode readiness.

### Certificate integrity / manual issuance

Manual certificate issuance is supported as an authorized exception/ad-hoc capability.

It must:
- use a truthful certificate type;
- record recipient, reason, authority, timestamp, and issuance mode;
- not rewrite the underlying attendance/presentation state.

A Presenter Certificate requires documented PRESENTED status or an authorized qualifying presentation exception/makeup outcome.

For people who did not present, edition-defined alternatives may include Participant, Committee, Guest, Supporting Contributor, or another truthful recognition category.

## Next — Stage 4E

Post-presentation revision/publication review/publication eligibility remains to be validated.
