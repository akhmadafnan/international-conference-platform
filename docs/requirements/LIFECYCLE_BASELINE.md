# End-to-End Conference Lifecycle Baseline

**ID:** ICP-REQ-LIFE-001  
**Status:** PHASE 0 — PARTIALLY APPROVED  
**Updated:** 2026-09-23

This document is the lifecycle-specific source used to validate the full conference journey.

## Stage map

```text
4A Registration / Profile / Join Edition / Submission Entry     APPROVED
4B Abstract Draft / Payment / Verification / Official Submit     NEXT
4C Academic Processing / Decision / Refund                       PENDING
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

## Next — Stage 4B

The next review must decide:
- what action means "submit abstract";
- exactly when payment becomes due;
- whether abstract metadata locks before payment;
- payment pending/failed/expired behavior;
- verification model;
- when the paper becomes an **Official Submission**;
- whether an official submission can still be edited;
- withdrawal before/after payment;
- submission code/receipt timing.

No Stage 4B behavior is approved merely because it appears as a candidate in PRD.
