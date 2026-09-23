# Refund Baseline — V1

**ID:** ICP-REQ-REF-001  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-23

## Academic rejection

```text
REJECTED
→ REFUND_ELIGIBLE
→ Finance processing
→ REFUNDED
```

- Refund amount: **100% of conference fee actually paid**.
- Refund execution: manual.
- Authorized executor: Finance.
- Academic authority determines the rejection; Finance executes the resulting refund workflow.

## Other causes

These do **not** automatically inherit the academic-rejection rule:
- author withdrawal;
- administrative ineligibility;
- exceptional cancellation cases.

Their refund eligibility/amount is edition-configurable and remains to be defined.

## Audit

The platform must preserve:
- original payment record;
- academic/admin decision;
- refund eligibility basis;
- refund amount;
- destination/recipient details with restricted access;
- Finance processor;
- timestamps;
- proof/record of refund;
- failure/action-required reason where applicable.

Refund does not erase payment/submission/decision history.

## Front Office boundary

FO may explain refund status and escalate support.
FO may not:
- declare eligibility contrary to policy;
- approve or execute a refund;
- mark a refund as completed;
- expose restricted banking data.
