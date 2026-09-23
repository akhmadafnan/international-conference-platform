# Payment Baseline — V1 Manual Bank Transfer

**ID:** ICP-REQ-PAY-001  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-23

## V1 scope

V1 uses **manual bank transfer**. The platform does not require a payment gateway.

## Lifecycle

```text
DRAFT
→ Submit & Proceed to Payment
→ validation
→ AWAITING_PAYMENT
→ manual bank transfer
→ upload proof
→ PAYMENT_SUBMITTED
→ Finance cross-checks actual receipt
   ├─ VERIFIED
   │    → PAID
   │    → OFFICIAL_SUBMISSION
   └─ NOT VERIFIED
        → PAYMENT_ACTION_REQUIRED
        → author corrects / re-uploads proof
```

Optional policy state:
- `PAYMENT_EXPIRED` when the edition enforces a payment deadline.

## Authority

### Author
May:
- view payment instructions;
- view amount due;
- transfer outside the system;
- upload/re-upload payment proof;
- view non-sensitive payment status.

May not:
- mark payment as verified;
- edit authoritative payment amount;
- see bank mutation/reconciliation information.

### Finance
May:
- review submitted proof;
- cross-check actual incoming funds outside the platform;
- verify payment;
- mark action required;
- record reason;
- view Finance-restricted information.

### Front Office
May:
- explain workflow/status;
- create/escalate a support case.

May not:
- verify payment;
- change Finance status;
- expose bank mutation information.

## Core rules

1. Upload proof ≠ PAID.
2. PAYMENT_SUBMITTED ≠ VERIFIED.
3. Only authorized Finance verification can set PAID.
4. Finance verification requires cross-check against actual receipt.
5. Only PAID can transition a paper into OFFICIAL_SUBMISSION.
6. PAID ≠ ACCEPTED.
7. Verification actions are auditable.
8. Action-required decisions include a reason.
9. Before payment succeeds, submit intent may return to DRAFT when edition policy/deadline permits.
10. After payment, withdrawal is a formal lifecycle event.
11. Official submission produces a stable submitted-version snapshot.
12. Sensitive bank/mutation data is Finance-restricted.
13. Fee amount/category is edition-configurable, not typed freely by the Author.
14. V1 remains architecture-ready for a future payment provider without implementing one now.

## Deferred technical decisions

Not yet locked:
- exact bank-account configuration storage;
- proof file format/size;
- verification screen design;
- duplicate-payment detection rules;
- unique transfer amount/reference strategy;
- receipt/invoice numbering;
- future gateway/provider;
- refund execution mechanism.

These will be defined in later feature/data specifications.
