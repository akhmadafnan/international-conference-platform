# Permission Model — Part 9 Assignment, Revocation, COI, Overrides & Break-glass

**ID:** ICP-REQ-PERM-001-P9  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Assignment is a record

A role/capability assignment is a scoped, auditable record.

Minimum conceptual attributes:
- subject/user;
- role/capability;
- scope;
- assigned_by;
- assigned_at;
- effective_from;
- optional expires_at;
- status;
- revoked_by/revoked_at;
- reason/reference where applicable.

## Role vs resource assignment

Role assignment:
- Finance → Edition 2027;
- Conference Admin → Edition 2027.

Resource assignment:
- Reviewer → Submission / Round / Version;
- Session Chair → Session;
- Presenter → Submission/presentation;
- Submission Collaborator → Submission.

Resource assignment must not grant broad edition authority.

## Assignment lifecycle

Possible conceptual states:
- INVITED;
- ACTIVE / ACCEPTED;
- COMPLETED;
- EXPIRED;
- REVOKED;
- CANCELLED.

Exact implementation enum is deferred.

## Temporary authority

Assignments may be time-bounded.

Expiry removes future permission while preserving the historical record.

## Revocation

```text
REVOKE AUTHORITY
≠
DELETE HISTORICAL ATTRIBUTION
```

Past actions retain the original actor.

## Active responsibilities

Revocation may require:
- cancelling active assignment;
- assigning a replacement;
- preserving original history;
- immediately blocking further access.

The system should surface dependent responsibilities but must allow urgent revocation.

## Protected authorities

Protected governance applies at least to:
- Super Admin;
- Technical Admin;
- Finance;
- Academic Decision Authority;
- Publication authority;
- privileged certificate issuance/revoke/reissue;
- historical.correct;
- edition.archive / edition.unarchive;
- break-glass authority.

## Anti-self-escalation

Protected authority cannot be self-assigned through normal administration.

A separate authorized assigner is required.

## V1 approval rule

V1 baseline:
- one authorized assigner is sufficient;
- self-assignment blocked;
- assignment scoped;
- audit required.

Architecture remains future-ready for four-eyes/dual approval.

## Generic COI layer

```text
NORMAL ALLOW
+
CONFLICT / RESTRICTION
=
DENY
```

Required direct self-conflict cases:
- Reviewer → own/conflicted submission;
- Academic Decision Authority → own/conflicted submission;
- Finance → own payment/refund;
- Event verifier → own presentation;
- privileged certificate operator → own manual privileged certificate.

Reviewer COI declaration remains required.

## Override model

No global override-everything permission.

Overrides are domain-specific, for example:
- payment correction;
- refund override;
- academic decision override;
- presentation exception;
- certificate exception;
- historical correction;
- edition unarchive.

## Override audit

Every override records:
- actor;
- authority/capability;
- target resource;
- original state/value;
- new state/value;
- reason;
- timestamp;
- reference/evidence where applicable.

Original facts/history remain preserved.

## Break-glass

Break-glass is technical/security emergency access, not an ordinary business override.

Use cases may include:
- critical incident;
- security investigation;
- emergency recovery;
- technical remediation.

Break-glass must be:
- reasoned;
- scoped;
- temporary;
- fully auditable;
- expired/revoked after use.

It does not permanently grant business authority.

## Break-glass audit

Preserve:
- actor;
- incident/reason;
- temporary scope/privileges;
- resources accessed;
- actions performed;
- start;
- expiry/revocation.

## Impersonation

Silent impersonation/account takeover is denied.

V1 has no requirement for impersonation.

If added later, it must be a separate privileged audited capability with explicit reason and visible impersonation context.

Front Office receives no informal account-takeover authority.

## Delegation revocation

Delegated permissions may be revoked prospectively.

Actions performed while delegation was valid retain original attribution.

## Attribution immutability

A replacement role holder never becomes the historical actor for prior actions.

## Sensitive revocation

Sensitive-role/capability revocation must stop new authorization immediately at product-policy level.

Authentication/session invalidation mechanics are deferred to security/NFR specification.

## Dependency warning

Before ordinary revocation, system may warn about:
- active reviews;
- future session duties;
- pending Finance work;
- submission delegations;
- other assigned work.

Warning does not block emergency revocation.

## Audit events

At minimum:
- ASSIGN;
- ACCEPT / ACTIVATE;
- REVOKE;
- EXPIRE;
- EXTEND;
- CHANGE_SCOPE;
- REASSIGN.

## Next

Part 10 performs the full permission-matrix consistency audit and closes REQ-PERM-001 if GREEN.
