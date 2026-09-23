# Permission Model — Part 4 Finance & Refund

**ID:** ICP-REQ-PERM-001-P4  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-23

## Finance scope

Finance is an EDITION-scoped authoritative financial role.

A Finance role in one edition does not automatically apply to another edition.

## Payment verification authority

Only authorized Finance may make authoritative payment-state changes including:
- PAYMENT_ACTION_REQUIRED;
- PAID;
- controlled financial correction states/actions.

Payment verification must be server-authorized and auditable.

## Payment evidence access

### Related Author / payer
May access:
- amount due;
- destination account information intended for payer;
- own uploaded proof;
- payer-facing payment status;
- safe action-required reason.

May not access:
- bank mutation/reconciliation;
- internal Finance notes;
- other participants' payments.

### Finance
May access:
- payment queue;
- payment/submission reference;
- amount due;
- payer information needed for verification;
- uploaded proof;
- actual received amount where recorded;
- reconciliation evidence;
- internal Finance notes/history.

### Other roles
Default to derived status only, for example:
- payment satisfied / not satisfied;
- refund status.

Raw proof/reconciliation data is denied unless a separate justified permission exists.

## Reconciliation

Bank/reconciliation detail is Finance-restricted by default.

Potential data:
- transaction reference;
- actual received amount;
- received timestamp;
- matching evidence;
- internal reconciliation note.

## Verification audit

Record at least:
- payment;
- verifier;
- result;
- verified_at;
- actual amount where recorded;
- reason/note;
- evidence/reference where policy requires.

## Action-required reasons

Examples:
- amount mismatch;
- proof unreadable;
- transfer not found;
- wrong destination account;
- duplicate proof;
- reference mismatch;
- other.

Only safe author-facing information is exposed outside Finance.

## Finance vs Academic

```text
PAID
≠
ACCEPTED
```

Finance cannot perform academic acceptance/rejection/revision decisions.

## Refund eligibility

```text
Policy / Business Event
→ REFUND_ELIGIBLE
→ Finance Execution
```

Academic rejection:
- Academic authority records rejection;
- system/policy creates eligibility;
- Finance processes refund.

Finance does not freely create academic refund eligibility.

## Refund execution

Finance may:
- view refund queue;
- view eligibility source;
- view refundable amount;
- view recipient/bank data necessary for refund;
- record processing status;
- record/upload refund evidence;
- mark REFUND_ACTION_REQUIRED;
- mark REFUNDED;
- perform controlled correction.

## Refund amount authority

Baseline formula:
```text
applicable policy
+ amount actually paid
→ refund amount
```

A policy exception requires a separate auditable override.

## Refund recipient privacy

Refund bank/recipient details are restricted to:
- the participant/recipient entering their own data where applicable;
- authorized Finance;
- exceptional controlled access if later approved.

Front Office and other domains receive safe status only.

## Financial corrections

Financial state/history is never silently rewritten.

Correction records:
- before;
- after;
- reason;
- actor;
- timestamp;
- evidence/reference.

## Record preservation

Operational payment/refund records are not freely hard-deleted.

Correction/void/supersession concepts may be used later in the physical state model.

## Overpayment and partial payment

The system must not assume:
```text
actual received = amount due
```

Overpayment/partial/mismatch:
- is preserved in Finance reconciliation;
- does not automatically become normal PAID;
- may route to PAYMENT_ACTION_REQUIRED in V1.

No installment feature is implied by this baseline.

## Finance self-conflict

Normal rule:
```text
Finance actor
+ own submission/payment/refund case
→ financial decision permission DENIED
```

Resolution:
- another authorized Finance actor; or
- later-defined controlled exceptional override.

## Future Finance separation

V1 may use one Finance role.

Architecture must permit later roles such as:
- Payment Verifier;
- Refund Processor;
- Finance Approver.

## Summary matrix

| Action | Finance | Front Office | Conference Admin | Academic | Related Author |
|---|---|---|---|---|---|
| View safe payment status | Allow | Support view | Operational view | Requirement status | Own |
| View payment proof | Allow | Deny default | Deny default | Deny | Own only |
| View reconciliation/bank detail | Allow | Deny | Deny | Deny | Deny |
| Verify PAID | Allow except self-conflict | Deny | Deny | Deny | Deny |
| Set payment action-required | Allow | Deny | Deny | Deny | Deny |
| View safe refund status | Allow | Support view | Operational view | Eligibility context | Own |
| View refund bank data | Allow | Deny | Deny | Deny | Own input only |
| Process REFUNDED | Allow except self-conflict | Deny | Deny | Deny | Deny |
| Controlled financial correction | Finance authority | Deny | Deny | Deny | Deny |

## Next

Part 5 defines Academic Committee, Reviewer, and Academic Decision Authority.
