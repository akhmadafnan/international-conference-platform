# End-to-End Conference Lifecycle Baseline

**ID:** ICP-REQ-LIFE-001  
**Status:** PHASE 0 — END-TO-END LIFECYCLE APPROVED  
**Updated:** 2026-09-23

This document is the lifecycle-specific source used to validate the full conference journey.

## Stage map

```text
4A Registration / Profile / Join Edition / Submission Entry     APPROVED
4B Abstract Draft / Manual Payment / Finance Verification        APPROVED
4C Academic Processing / Decision / Refund                       APPROVED
4D Full Paper / LoA / Scheduling / Presentation                  APPROVED
4E Post-Presentation Revision / Publication Review / Gate        APPROVED
4F OJS / Publication / Certificate / Archive                     APPROVED
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

## Stage 4E — Approved: Post-Presentation Publication Quality Gate

```text
PRESENTED
→ post-presentation assessment/feedback
→ FINAL_MANUSCRIPT_PENDING
→ final/revised manuscript
→ PUBLICATION_REVIEW
→ REVISION_REQUIRED | PUBLICATION_APPROVED | PUBLICATION_REJECTED
→ if approved: PUBLICATION_ELIGIBILITY_GATE
→ PUBLICATION_ELIGIBLE or BLOCKED
```

### Approved rules

1. Presentation does not equal publication approval.
2. Session/presentation feedback is distinct from formal Publication Review.
3. V1 Publication Review defaults to **double-anonymous**.
4. Publication Review remains configurable by edition/stage using the common Review Stage engine.
5. Double-anonymous assignments receive anonymized identity-safe review packets.
6. Reviewer count remains policy-driven, not globally fixed.
7. Multiple review rounds and versioned manuscript revisions are supported.
8. Reviewer recommendations are advisory; authorized decision authority records the final decision.
9. Publication decisions: REVISION_REQUIRED, PUBLICATION_APPROVED, PUBLICATION_REJECTED.
10. PUBLICATION_APPROVED does not bypass the Publication Eligibility Gate.
11. NO_SHOW remains blocked unless an authorized qualifying exception/makeup applies.
12. Publication rejection preserves legitimate presenter/conference history.
13. Publication rejection does not automatically trigger conference-fee refund.
14. Review, revisions, decisions, gate checks, overrides, and blocking reasons are auditable.
15. Conference platform controls PUBLICATION_ELIGIBLE before OJS/proceedings handoff.

## Next — Stage 4F

Proceedings/OJS handoff, publication status, certificate completion, archive, and historical record remain to be validated.


## Stage 4F — Approved: Publication Handoff, Certificates, Closeout, Archive

```text
PUBLICATION_ELIGIBLE
→ PUBLICATION_QUEUE
→ FINAL METADATA SNAPSHOT
→ READY_FOR_TRANSFER
→ MANUAL/ASSISTED OJS HANDOFF
→ TRANSFERRED
→ IN_PUBLICATION_PROCESS
→ PUBLISHED
→ CERTIFICATE/VERIFICATION
→ EDITION CLOSEOUT
→ ARCHIVED
```

### Approved rules

1. PUBLICATION_ELIGIBLE ≠ PUBLISHED.
2. Publication Team owns the Publication Queue.
3. V1 OJS/proceedings handoff is manual/assisted.
4. OJS remains downstream and does not become conference source of truth.
5. Final publication metadata is snapshotted and protected from silent current-profile changes.
6. OJS/DOI/URL/ISBN/ISSN references are external identifiers, not internal primary keys.
7. Publication transfer/status history is auditable.
8. Certificates have unique records/verification identities and support public verification + QR.
9. Manual Certificate Builder supports individual and bulk issuance using configured activity/event date.
10. Certificate corrections use controlled revoke/reissue/versioned correction.
11. Edition closeout uses a formal checklist.
12. Closeout warnings/blockers are policy-driven.
13. ARCHIVED editions are preserved and primarily read-only.
14. Historical corrections require authority/reason/audit trail.
15. Archive retains the complete conference lifecycle history subject to access policy.

## Lifecycle completion

Stages 4A–4F are approved. `REQ-LIFE-001` is complete at product-requirement level.

This does **not** mean application implementation is authorized; detailed permissions, integrations, NFRs, domain/data design, and Phase 0 final gate remain outstanding.


## Permission Ownership Closure — REQ-PERM-001 Part 10

The final permission audit explicitly assigns previously implicit lifecycle ownership:

- `ADMINISTRATIVE_SCREENING` is performed through `submission.admin_screen` by an authorized edition screening authority; it is not an academic decision.
- Official-submission withdrawal separates Author request from `submission.withdraw.approve` by an authorized edition authority.
- Protected post-submission contributor/authorship changes use stage-aware `submission.contributor_change.approve`.
- Front Office is edition-scoped support only: it can surface safe status and escalate, but cannot execute Finance/Academic/Event/Publication decisions.
- Normal `PUBLICATION_ELIGIBILITY` evaluation is policy/system based on authoritative lifecycle facts.
- `publication.eligibility.override` is a protected exception and never rewrites source facts merely to force PASS.

These closures complete the lifecycle-to-authority mapping for stages 4A–4F.
