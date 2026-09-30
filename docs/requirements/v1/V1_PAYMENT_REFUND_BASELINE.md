# V1 Payment & Refund Baseline

**ID:** ICHES-V1-PAY-001  
**Status:** AUTHORITATIVE V1 BASELINE  
**Updated:** 2026-09-30

## Payment position

Payment belongs to conference participation registration, not paper acceptance.

```text
REGISTER
→ SELECT PACKAGE
→ PAYMENT_DUE
→ manual transfer
→ PROOF_SUBMITTED
→ Finance verification
   ├─ valid → PAID / REGISTRATION_CONFIRMED
   └─ correction → CORRECTION_REQUIRED
```

Only after registration confirmation does the academic abstract-submission path open.

## V1 payment statuses

- UNPAID
- PROOF_SUBMITTED
- PAID
- CORRECTION_REQUIRED
- CANCELLED

## Core rules

1. Uploading proof does not mean PAID.
2. Only authorized Finance may verify payment.
3. Finance verification is auditable.
4. Re-upload preserves prior proof/history.
5. Amount comes from edition package/category configuration.
6. Payment and academic acceptance are separate facts.
7. A rejected abstract remains a valid participant registration.
8. Academic rejection does not automatically create refund eligibility.
9. V1 refund handling is exceptional/manual.
10. Duplicate payment, event cancellation, or authorized administrative correction may create refund handling according to policy.
11. Finance-restricted bank/refund details remain need-to-know.
12. No payment gateway is required in accelerated V1.

## Pricing model

`Participation Package + optional simple Participant Category`

No coupons, dynamic pricing, tax engine, or complex discount stack.

## Exceptional refund record

If an exceptional refund is processed, preserve:
- original payment;
- reason/basis;
- amount;
- recipient/bank details with restricted access;
- Finance actor;
- timestamps;
- proof/reference;
- resulting status.

Refund does not erase registration/payment history.
