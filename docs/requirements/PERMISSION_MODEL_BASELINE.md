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
- Part 2 — Super Admin / Technical Admin / Conference Admin: APPROVED
- Part 3 — Participant / Author / Presenter: APPROVED
- Part 4 — Finance / Refund: NEXT
- Part 5 — Academic Committee / Reviewer / Decision Authority
- Part 6 — Event Operations / Session Chair / Moderator
- Part 7 — Publication / OJS
- Part 8 — Certificate / Archive
- Part 9 — Assignment / Revocation / COI / Overrides
- Part 10 — Full Matrix Consistency Audit


## Part 2 — Approved Administrative Role Matrix

### Super Administrator

Scope: `GLOBAL`.

Primary authority:
- platform governance;
- conference-series and edition lifecycle administration;
- global account/access governance;
- role infrastructure and protected-role administration;
- global security/audit visibility;
- emergency platform controls;
- platform/integration configuration.

Default business-domain restrictions:
- no automatic payment verification;
- no automatic refund approval/execution;
- no automatic academic decision;
- no automatic reviewer recommendation;
- no automatic presentation verification;
- no automatic publication approval.

A Super Administrator who also performs a business-domain function must receive the corresponding additional role/assignment.

### Technical Administrator

Primary authority:
- application/system configuration;
- system health/diagnostics;
- queue/job/notification diagnostics;
- storage and technical integration operations;
- backup/restore and maintenance operations as later specified;
- technical audit subset.

Default restrictions:
- no Finance authority;
- no Academic decision authority;
- no Publication decision authority;
- no unrestricted confidential-review access;
- no unrestricted bank/finance-sensitive access.

Technical diagnostics should expose only the minimum business data needed to diagnose the technical problem.

### Conference Administrator

Scope: assigned `EDITION`.

Primary authority:
- edition configuration;
- dates/deadlines;
- tracks/subthemes;
- forms/templates;
- fee/refund policy configuration;
- review-stage configuration;
- event/presentation configuration;
- publication/certificate configuration;
- public/CMS configuration;
- cross-domain operational status dashboards;
- edition-role coordination subject to protected-role rules.

Default restrictions:
- viewing a payment state does not permit payment verification;
- configuring academic workflow does not permit accepting/rejecting a paper;
- configuring presentation workflow does not permit marking PRESENTED;
- configuring publication workflow does not permit publication approval.

### Configuration vs execution

```text
CONFIGURE POLICY / WORKFLOW
≠
EXECUTE AUTHORITATIVE DOMAIN DECISION
```

Example:
- Conference Admin may configure conference fee and destination account;
- Finance verifies an actual transfer.
- Conference Admin may configure review mode/deadline;
- Academic Decision Authority records acceptance/rejection.

### Protected-role assignment

Protected roles cannot be self-assigned merely because a person is an administrator.

Global protected roles include at minimum:
- Super Administrator;
- Technical Administrator.

Edition-level sensitive roles may include:
- Finance;
- Academic Decision Authority;
- Publication authority;
- other roles later classified as protected.

Assignment/revocation must be auditable.

Conference Admin cannot assign a global Super Administrator or Technical Administrator role.

### Anti-self-escalation

No administrator may use ordinary role-management capability to self-grant a protected role or bypass separation-of-duties controls.

Any exceptional privilege elevation must use a dedicated, auditable privileged-access mechanism.

### Break-glass / emergency access

The platform should support a controlled emergency-access concept for serious technical/security incidents.

Minimum conceptual requirements:
- explicit reason;
- actor identity;
- scope/resource;
- temporary/elevated access record;
- start/end or revocation;
- sensitive-access audit trail.

Break-glass does not silently convert the administrator into the normal business-domain authority and must not erase the original business history.


## Part 3 — Approved Participant-Side Role Matrix

### Visitor

Scope: public.

Allowed:
- public conference pages;
- public CFP/guidelines;
- published schedule;
- public speaker information;
- public FAQ/announcements;
- public certificate verification;
- published proceedings/publication links.

Denied:
- participant workspace;
- protected submission files;
- payment evidence;
- internal review data;
- draft/internal schedules;
- private participant data.

### Prospective Participant

May:
- register/activate account;
- verify email;
- complete current profile;
- read policies;
- request/join edition.

Private edition capabilities require active edition membership.

### Participant

Scope: own account + own edition membership.

May:
- view/update current profile;
- view own edition membership and registration status;
- view own announcements/agenda;
- view own attendance/check-in status;
- access own eligible certificates;
- access own participant-relevant communication/history.

Participant membership alone does not grant submission-management, review, Finance, Event-authority, or Publication-authority permissions.

### Author / Corresponding Author

`Author` is a scholarly contributor relationship.

`Corresponding Author` is the primary submission manager/contact.

For an owned/managed submission, Corresponding Author may, subject to lifecycle state:
- create/manage draft;
- edit submission metadata while permitted;
- manage contributor list while permitted;
- upload abstract;
- submit for payment;
- upload own payment proof;
- view payer-facing payment status/action-required reason;
- view academic decision;
- view author-facing reviewer comments;
- respond to revision request;
- upload revised abstract;
- upload Full Paper/final manuscript;
- designate/modify presenter where policy permits;
- complete publication metadata/declarations;
- request formal withdrawal where permitted;
- view LoA and submission correspondence.

Corresponding Author does not receive:
- anonymous reviewer identity;
- confidential editor/reviewer comments;
- internal COI notes;
- internal academic deliberation;
- other authors' unrelated submissions;
- bank mutation/reconciliation details;
- internal Finance notes.

### Other Author / Co-author / Contributor

Contributor relationship does not automatically grant submission-management permission.

A Co-author may exist without an account.

No account means:
```text
contributor metadata exists
→ no authenticated workspace permission
```

If linked to an account, baseline access is still not equivalent to Corresponding Author management authority.

### Submission Collaborator / Delegation

The architecture should support a submission-scoped delegated relationship rather than creating a broad global role.

Candidate delegated permissions:
- VIEW;
- EDIT_METADATA;
- UPLOAD_FILES;
- RESPOND_REVISION.

Sensitive actions such as:
- WITHDRAW;
- CHANGE_CORRESPONDING_AUTHOR;
- sensitive contributor changes;
may require stronger authority/confirmation.

Detailed delegation/assignment governance is finalized in Part 9.

### Presenter

Presenter permission derives from the specific presentation/submission relationship.

May:
- view assigned presentation;
- confirm presenter designation;
- view session/room/date/time;
- view presentation instructions;
- access presentation-material workflow if enabled;
- view own presenter status;
- access presenter certificate when eligible.

Presenter cannot self-set authoritative `PRESENTED` status.

### Presenter who is also Author

Normal permissions combine subject to scope/resource-state rules.

### Exceptional non-author Presenter

Receives only presentation-related access needed for the assignment.

Does not automatically receive:
- manuscript editing;
- payment visibility;
- reviewer comments;
- publication-revision rights;
- contributor-management rights.

### Non-presenting Participant

Receives participant/member capabilities only, including own registration, check-in/attendance, announcements, and eligible participant certificates.

No submission/review/presentation-authority permissions arise from non-presenting attendance.

### Invited Speaker / Keynote

Edition-scoped special participant role.

May manage relevant own speaker data:
- bio/profile;
- affiliation;
- photo where supported;
- talk title/description;
- availability/confirmation;
- assigned session;
- presentation information;
- speaker certificate where eligible.

Speaker/Keynote status does not imply Conference Admin, Academic Committee, Reviewer, Finance, Event-authority, or Publication-authority permissions.

### Current profile vs historical records

```text
EDIT CURRENT PROFILE
≠
REWRITE HISTORICAL SNAPSHOT
```

Participants may edit current profile data, but historical submission/publication/certificate/archive snapshots are controlled records.

### Submission deletion / withdrawal

```text
DRAFT
→ cancel/delete may be allowed by policy

OFFICIAL_SUBMISSION+
→ no unrestricted hard delete
→ formal withdrawal workflow
```

Audit/history remains preserved.

### Contributor-list mutation

Before official submission:
- Corresponding Author may manage contributor list according to policy.

After official submission:
- add/remove/reorder/change contributors becomes a controlled correction;
- later lifecycle stages may impose stricter authority/evidence requirements.

### Participant-side privacy boundary

Participation in the same edition does not grant access to another participant's protected submission/workspace.

Publicly published information remains governed by public-content rules, not protected workspace permissions.
