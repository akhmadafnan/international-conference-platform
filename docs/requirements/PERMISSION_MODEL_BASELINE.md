# Permission Model Baseline — Part 1

**ID:** ICP-REQ-PERM-001-P1  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-23

## Authorization formula

```text
ROLE
+ SCOPE
+ RESOURCE RELATIONSHIP
+ DOMAIN AUTHORITY
+ RESOURCE STATE
+ RESTRICTIONS / COI
= EFFECTIVE PERMISSION
```

The platform must not rely on role name alone.

## Core principles

1. **Default deny** — absence of an explicit allow means deny.
2. **Least privilege** — each role receives only the access needed for its responsibility.
3. **Visibility ≠ authority** — being able to see status/data does not imply permission to change authoritative state.
4. **Technical authority ≠ business authority** — platform/system administration does not automatically grant Finance, Academic, Event, Publication, or Certificate decision rights.
5. **Server-side enforcement** — hidden buttons are not authorization; protected actions must be checked on the server.
6. **Need-to-know data access** — sensitive details may be narrower than general status visibility.
7. **Assignment ≠ decision** — assigning/monitoring work does not automatically confer final decision authority.
8. **Override is explicit** — exception/bypass authority is a separate permission with reason and audit trail.
9. **Historical correction is controlled** — archived/historical correction uses dedicated authority and audit.
10. **Restriction wins** — conflict-of-interest or explicit restriction overrides a normal allow from another role.

## Scope hierarchy

Minimum conceptual scopes:

```text
GLOBAL
└── EDITION
    ├── SESSION
    ├── PARTICIPANT / MEMBERSHIP
    └── SUBMISSION
        └── REVIEW ASSIGNMENT
```

Examples:
- Super Admin: global platform scope.
- Finance: edition-scoped.
- Session Chair/Moderator: session-scoped.
- Author/Presenter: submission/resource relationship scoped.
- Reviewer: review-assignment scoped.

A role in one edition does not automatically apply to another edition.

## Permission domains

The detailed matrix will group permissions by domain:

- PLATFORM;
- EDITION CONFIGURATION;
- IDENTITY / ACCOUNT;
- PARTICIPANTS / MEMBERSHIPS;
- SUBMISSIONS;
- PAYMENT / REFUND;
- ACADEMIC REVIEW;
- ACADEMIC DECISION;
- EVENT / SCHEDULING;
- PRESENTATION / ATTENDANCE;
- PUBLICATION / OJS;
- CERTIFICATES;
- ARCHIVE / HISTORICAL CORRECTION;
- AUDIT / SECURITY.

## Action vocabulary

The detailed matrix should distinguish actions such as:

- VIEW;
- CREATE;
- EDIT;
- ASSIGN;
- SUBMIT;
- VERIFY;
- DECIDE;
- PUBLISH;
- REVOKE;
- ARCHIVE;
- OVERRIDE.

A broad `manage` permission should be avoided for sensitive domains when finer authority boundaries are required.

## Multi-role behavior

A person may hold several roles. Normal permissions may combine, but the result is still constrained by:
- scope;
- resource relationship;
- resource state;
- COI;
- explicit restriction.

Example:

```text
Author + Reviewer
→ reviewer capability exists generally
→ assignment targets own/conflicted paper
→ COI restriction
→ DENY
```

## Administrative-role boundary

### Super Administrator

May administer platform-level capabilities and access governance.

Does not automatically receive authority to:
- verify payment;
- approve refunds;
- accept/reject submissions;
- submit reviewer recommendations;
- mark presentations complete;
- approve publication.

Business-domain authority requires the corresponding additional role/assignment.

### Technical Administrator

Technical access is limited to system operations/diagnostics/configuration by default.

It does not automatically grant access to confidential review, restricted Finance data, or business decisions.

### Conference Administrator

Coordinates/configures edition operations.

It does not automatically inherit authoritative Finance, Academic, Event-verification, or Publication decisions unless another role grants them.

## Sensitive status vs sensitive detail

Example:

```text
Front Office:
payment.status.view        ALLOW
bank.mutation.view         DENY
payment.verify             DENY

Publication Team:
payment.requirement.view   ALLOW
payment.proof.view         DENY by default
```

The detailed matrix will specify these boundaries.

## Override governance

Any exceptional override must record:
- actor/authority;
- scope/resource;
- reason;
- before state;
- after state;
- timestamp;
- optional evidence/reference.

## Detailed matrix structure

Each permission row should capture:

```text
Actor / Role
Domain
Action
Scope
Condition / Resource Relationship
Sensitive Data Boundary
Authority Level
Audit Requirement
```

## Review sequence

- Part 1 — Authorization Foundation: APPROVED
- Part 2 — Super Admin / Technical Admin / Conference Admin: NEXT
- Part 3 — Participant / Author / Presenter
- Part 4 — Finance / Refund
- Part 5 — Academic Committee / Reviewer / Decision Authority
- Part 6 — Event Operations / Session Chair / Moderator
- Part 7 — Publication / OJS
- Part 8 — Certificate / Archive
- Part 9 — Assignment / Revocation / COI / Overrides
- Part 10 — Full Matrix Consistency Audit
