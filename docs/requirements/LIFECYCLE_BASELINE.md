# End-to-End Conference Lifecycle Baseline

**ID:** ICP-REQ-LIFE-001  
**Status:** PHASE 0 — PARTIALLY APPROVED  
**Updated:** 2026-09-23

This document is the lifecycle-specific source used to validate the full conference journey.

## Stage map

```text
4A Registration / Profile / Join Edition / Submission Entry     APPROVED
4B Abstract Draft / Manual Payment / Finance Verification        APPROVED
4C Academic Processing / Decision / Refund                       NEXT
4D Full Paper / Scheduling / Presentation                        PENDING
4E Post-Presentation Revision / Publication Gate                 PENDING
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

## Next — Stage 4C

The next review must decide:
- academic screening/review model;
- decision states and decision authority;
- revision before acceptance, if used;
- rejected-paper behavior;
- refund eligibility and amount;
- withdrawal after payment;
- refund approval and execution;
- notification/LoA timing.
