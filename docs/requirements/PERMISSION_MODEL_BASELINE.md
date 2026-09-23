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
- Part 4 — Finance / Refund: APPROVED
- Part 5 — Academic Committee / Reviewer / Decision Authority: APPROVED
- Part 6 — Event Operations / Session Chair / Moderator: APPROVED
- Part 7 — Publication / OJS: APPROVED
- Part 8 — Certificate / Archive: NEXT
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


## Part 4 — Approved Finance & Refund Role Matrix

### Finance authority

Finance is an `EDITION`-scoped financial authority.

Only an authorized Finance role may make authoritative payment/refund state changes such as:
- verify submitted payment;
- set PAYMENT_ACTION_REQUIRED;
- record PAID;
- execute eligible refunds;
- record REFUND_ACTION_REQUIRED;
- record REFUNDED;
- perform controlled financial corrections.

### Payment evidence visibility

- Corresponding Author: own uploaded payment proof and payer-facing status only.
- Finance: payment proof + finance-required verification/reconciliation data.
- Front Office: payment status/support view; payment proof denied by default.
- Academic roles: derived payment requirement satisfied/not satisfied where needed; no raw proof by default.
- Publication Team: derived payment requirement satisfied/not satisfied; no raw proof by default.
- Event Operations: eligibility/payment requirement status only; no raw proof/amount/reconciliation by default.
- Conference Admin: operational status visibility; raw payment proof/reconciliation denied by default.

### Bank mutation / reconciliation

Restricted to Finance by default.

May include:
- transaction/reference;
- actual amount received;
- received date/time;
- matching/reconciliation evidence;
- internal Finance notes.

Other domains receive derived financial state, not raw reconciliation evidence.

### Payment verification audit

Authoritative verification records:
- payment/submission reference;
- verifier;
- result;
- verified_at;
- amount actually received where recorded;
- reason/note;
- evidence/reference as policy requires.

Action-required reasons may include:
- amount mismatch;
- proof unreadable;
- transfer not found;
- wrong destination;
- duplicate proof;
- reference mismatch;
- other.

Only the author-facing reason is exposed to the Author.

### Finance vs Academic authority

```text
FINANCIAL STATE
≠
ACADEMIC STATE
```

Finance cannot accept/reject/revise a submission academically merely because payment is verified.

### Refund eligibility vs refund execution

```text
BUSINESS EVENT / POLICY
→ REFUND_ELIGIBLE
→ Finance executes refund
```

Finance does not freely invent academic refund eligibility.

For V1 academic rejection:
- Academic authority records REJECTED;
- system/policy creates refund eligibility;
- Finance processes the refund.

### Refund data

Finance may access the bank/recipient data required to execute a refund.

Front Office and unrelated roles may view safe refund status only.

### Refund amount

Refund amount derives from:
- applicable refund policy;
- amount actually paid.

Any exception/override to computed policy amount requires dedicated authority and audit.

### Financial correction

Verified/paid/refunded history is not silently overwritten.

Correction must preserve:
- before state;
- after state;
- reason;
- actor;
- timestamp;
- evidence/reference where applicable.

### No hard delete

Payment/refund records are not freely hard-deleted after they become operational records.

Invalid/incorrect records use controlled void/correction/supersession semantics as later specified.

### Overpayment / partial payment

The system must not assume:
```text
actual received amount = amount due
```

Overpayment or partial/mismatched receipt does not automatically become normal PAID.

V1 may route mismatches to PAYMENT_ACTION_REQUIRED while preserving reconciliation detail.

### Finance self-conflict

A Finance actor cannot normally verify/process:
- payment for their own submission;
- refund payable to themselves/their own submission.

Restriction/COI overrides the Finance role allow.

Resolution:
- another authorized Finance actor; or
- a controlled exceptional override defined later.

### V1 Finance role vs future split

V1 may use one Finance role for operational simplicity.

Architecture must remain compatible with future separation such as:
- Payment Verifier;
- Refund Processor;
- Finance Approver.

The physical permission design must not make future separation impossible.


## Part 5 — Approved Academic Committee / Reviewer / Decision Authority Matrix

### Academic Committee

Academic Committee manages academic review operations and may:
- screen academic eligibility where assigned;
- discover/select reviewers;
- create/manage review assignments;
- configure/manage review rounds and deadlines within edition policy;
- send reminders;
- screen COI;
- replace/cancel/reassign reviewers;
- monitor review progress;
- view relevant reviewer identity;
- view relevant reviewer recommendations;
- view author-facing comments;
- view confidential editor comments;
- view COI declarations and assignment history.

Academic Committee does not automatically hold final academic decision authority.

### Reviewer

Reviewer access is scoped to the specific review assignment.

Reviewer may:
- accept/decline assignment;
- declare COI;
- view assigned manuscript/version;
- view assignment instructions/form;
- draft and submit review;
- submit author-facing comments;
- submit confidential editor comments where enabled;
- submit recommendation;
- view own submitted review/history.

Reviewer may not:
- browse unrelated submissions;
- self-select arbitrary submissions;
- assign/replace other reviewers;
- access raw Finance data;
- edit Author manuscript/metadata;
- make final academic decision.

### Assigned version

Reviewer access is tied to the assigned manuscript version/round.

A new manuscript version does not automatically become visible to the Reviewer unless a new/continued assignment explicitly grants access.

### Anonymity enforcement

Anonymity is enforced per review assignment.

For `single_anonymous`:
- Reviewer may see Author identity;
- Author may not see Reviewer identity.

For `double_anonymous`:
- Reviewer receives anonymized manuscript/content;
- Reviewer cannot access Author identity, affiliation, email, profile/account, ORCID, contributor identity, or identity-bearing original files where those reveal identity;
- Author cannot access Reviewer identity.

Mixed anonymity across different assignments on the same submission must not leak identity between assignments.

### Reviewer-to-reviewer visibility

Default V1:
- Reviewer cannot see another Reviewer's report/identity merely because both review the same submission.
- Academic Committee/Decision Authority may view all relevant reports according to role.

Future post-completion peer-review visibility may be configurable, but default is deny.

### Conflict of Interest

Before substantive review access, Reviewer must declare COI/assignment acceptance status.

COI may result in:
- decline;
- blocked assignment;
- cancelled assignment;
- reassignment.

Academic Committee may also screen COI.

Restriction/COI overrides normal role permission.

### Reviewer recommendation

Reviewer recommendation is advisory and never automatically determines final outcome.

No automatic majority-vote decision is permitted unless a future explicit edition policy is approved.

### Academic Decision Authority

Academic Decision Authority records authoritative academic decisions for the applicable review stage.

Abstract stage outcomes may include:
- ACCEPTED;
- REVISION_REQUIRED;
- REJECTED.

Publication-review outcomes may include:
- REVISION_REQUIRED;
- PUBLICATION_APPROVED;
- PUBLICATION_REJECTED.

Decision Authority may access the academic evidence required for decision-making:
- relevant submission/manuscript;
- review assignments;
- reviewer identity where authorized;
- reviewer recommendations;
- author-facing comments;
- confidential comments;
- COI status;
- revision/version history;
- relevant academic history.

This role does not automatically receive Finance, Event-verification, or OJS-transfer authority.

### Stage-scoped decision authority

Decision authority may differ by stage.

Example:
- Abstract Review → Scientific Committee Chair;
- Publication Review → Proceedings/Academic Editor.

The implementation must not assume one universal decision authority for the entire lifecycle.

### Decision rationale

Where the final decision materially diverges from normal review synthesis or uses an exceptional/override path, a rationale must be recorded and auditable according to policy.

### Submitted review lock

```text
REVIEW_DRAFT
→ Reviewer editable

REVIEW_SUBMITTED
→ locked by default
```

Correction requires controlled reopen/return workflow with audit.

### Assignment history preservation

Reviewer assignments are not silently deleted.

Invitation/acceptance/decline/cancel/reassign/completion history remains traceable.

### Confidential comment boundary

Author-facing comments and confidential editor comments are separate channels.

Confidential comments must not be exposed to Authors through:
- UI;
- API;
- export;
- decision letter;
- email;
- generated document.

### Anonymous identity leakage prevention

Reviewer/Author identity restrictions must apply beyond visible UI fields, including:
- download/file access;
- file names where identity-bearing;
- URLs;
- exports;
- notifications;
- email templates;
- document metadata;
- author-visible audit/history.

### Multi-role academic COI

Academic Committee may also act as Reviewer, but Reviewer-assignment anonymity rules remain assignment-specific.

Academic Decision Authority who is also an Author/Contributor on the same submission is conflicted and cannot make the final decision for that submission.

### Decision correction

Final academic decisions are not silently overwritten.

Correction/supersession preserves:
- prior decision;
- new decision;
- reason;
- authority;
- timestamp;
- relevant reference/evidence.


## Part 6 — Approved Event Operations / Session Chair / Moderator Matrix

### Event Operations

Scope: `EDITION`.

Primary event-domain authority:
- session/room/slot operations;
- presenter readiness;
- check-in/attendance operations;
- presentation verification;
- no-show recording;
- rescheduling;
- operational notes.

Event Operations receives only operationally necessary information.

Allowed derived indicators may include:
- payment/eligibility requirement satisfied;
- Full Paper/presentation readiness;
- presenter confirmation.

Raw Finance data, reviewer identity/comments, confidential academic deliberation, and publication-review confidential notes remain denied by default.

### Scheduling

Authorized Event Operations may:
- create/edit sessions;
- create/edit presentation slots;
- assign eligible presentations;
- change room/date/time;
- publish schedule;
- revise/unpublish schedule where policy permits.

Changes to already-published schedules are auditable and may trigger notifications.

### Session Chair

Scope: assigned `SESSION`.

May:
- view assigned session;
- view assigned presentation slots;
- view presenter/paper information needed for session operation;
- view session-relevant attendance/presentation status;
- record operational notes;
- verify PRESENTED/NO_SHOW for presentations in the assigned session.

No authority over unrelated sessions unless separately assigned.

### Moderator

Scope: assigned `SESSION`.

May:
- view assigned session/order/timing;
- view presenter information needed for moderation;
- record operational notes;
- assist attendance/presentation verification.

Moderator authority to set PRESENTED/NO_SHOW is edition/session-policy configurable rather than universally enabled.

### Attendance vs presentation

```text
CHECKED_IN / ATTENDED
≠
PRESENTED
```

Participant self check-in or QR check-in does not create authoritative PRESENTED status.

### Presentation verification

Authoritative presentation verification may be performed by:
- Event Operations;
- Session Chair for assigned session;
- Moderator when explicitly authorized by policy;
- another explicitly authorized event verifier.

Verification must preserve:
- presentation;
- verifier;
- status;
- verified_at;
- session;
- note/reason;
- optional evidence/reference.

### No-show

Event authority records factual NO_SHOW status.

Business rules then derive downstream consequences such as PUBLICATION_BLOCKED.

Event roles do not themselves make academic publication decisions.

### Presentation exception / makeup

Normal presentation verification permission does not imply exception approval.

`presentation.exception.approve` is a distinct, auditable authority finalized in Part 9.

A normal Session Chair/Moderator cannot silently convert a completed NO_SHOW into a qualifying exception.

### Presenter substitution

Presenter changes are traceable.

Exceptional non-author presenter substitution requires the previously approved authorization/exception path.

Post-event historical presenter changes require controlled correction.

### Rescheduling boundary

Event Operations may perform authoritative schedule changes.

Session Chair/Moderator may report/request adjustments or operational delays but do not automatically control the full edition schedule.

### Event-domain privacy

Event roles do not automatically receive:
- payment proof/bank reconciliation;
- reviewer identity/reports;
- confidential academic comments;
- academic decision authority;
- publication approval authority.

### Operational notes

Operational notes may be participant-visible or internal according to field/policy.

Event notes must not be used as a substitute store for restricted Finance or confidential review data.

### Evidence

Presentation/attendance evidence is policy-driven and may include:
- authorized verifier;
- attendance record;
- operational note;
- optional photo/reference;
- optional virtual-platform/log reference.

V1 does not require universal photo/video evidence.

### Event self-conflict

An Event Operations/Session Chair/Moderator actor cannot normally verify their own presentation.

Own-presentation verification must be performed by another authorized event verifier or controlled exceptional authority.

### Certificate separation

Event authority records factual presentation/attendance states.

Certificate issuance remains a separate certificate-domain process that consumes those states according to certificate rules.


## Part 7 — Approved Publication Team / Proceeding Editor / OJS Handoff Matrix

### Publication Team

Scope: `EDITION`.

Publication Team is the operational authority for downstream publication processing after the required academic/publication gates have been satisfied.

It may:
- manage Publication Queue;
- validate final publication metadata;
- prepare publication package;
- record READY_FOR_TRANSFER;
- perform manual/assisted OJS/proceedings handoff;
- record TRANSFERRED / IN_PUBLICATION_PROCESS / PUBLISHED;
- record external publication references;
- perform controlled publication-record corrections.

It does not automatically hold Academic Decision Authority.

### Publication state separation

```text
PUBLICATION_APPROVED
≠
PUBLICATION_ELIGIBLE
≠
PUBLISHED
```

- Academic Decision Authority controls PUBLICATION_APPROVED.
- Eligibility rules/gate control PUBLICATION_ELIGIBLE.
- Publication Team controls downstream publication operations/status tracking after eligibility.

Publication Team cannot use operational permissions to bypass academic review or eligibility gates.

### Publication Queue

Only submissions satisfying the required publication gate may enter the actionable publication-processing queue.

Publication Team may manage:
- final metadata validation;
- publication package readiness;
- transfer destination;
- transfer timestamps/actor;
- external processing status;
- final published references.

### Data visibility

Publication Team may view publication-required data such as:
- final manuscript/version;
- title;
- abstract;
- keywords;
- language;
- contributor order;
- affiliations;
- optional ORCID;
- declarations/consent;
- presentation/eligibility result;
- derived payment requirement satisfied/not satisfied.

Publication Team does not receive raw Finance proof/reconciliation/bank details by default.

Confidential academic-review content is limited to what is operationally necessary; publication approval/eligibility result is sufficient unless another academic/editorial role grants more.

### Final publication metadata snapshot

Before/at transfer, the platform preserves an authoritative publication snapshot.

Later current-profile changes do not silently rewrite that snapshot.

Publication Team may correct non-substantive metadata through controlled workflow.

Substantive changes such as:
- adding/removing/reordering authors after protected stages;
- major title changes;
- replacing substantive manuscript content;
require the appropriate correction/academic authority rather than ordinary publication-operations edit permission.

### Manual/assisted OJS handoff

V1 Publication Team may:
- export/copy publication metadata;
- upload/transfer files manually;
- record publication destination;
- record OJS/external reference ID;
- record transferred_at / transferred_by;
- record external status.

No OJS API integration is required for V1.

### External identifiers

External identifiers may include:
- OJS submission/reference ID;
- DOI;
- publication URL;
- ISBN/ISSN reference;
- proceedings/volume/issue reference;
- published date.

These remain external references and never become internal primary keys.

### Publication status integrity

Publication status must reflect known downstream fact.

`READY_FOR_TRANSFER` cannot be marked `PUBLISHED` merely to clear a queue.

Status transitions are auditable and may require:
- actor;
- timestamp;
- external reference;
- note/evidence where applicable.

### Publication failure / withdrawal

Downstream states such as PUBLICATION_FAILED or PUBLICATION_WITHDRAWN preserve their history.

They do not erase legitimate:
- PRESENTED status;
- conference participation history;
- presenter certificate history;
- prior academic history.

### Proceeding Editor and future split

V1 may combine Publication Team / Proceeding Editor operational responsibilities for simplicity.

Permission architecture must remain compatible with future roles such as:
- Publication Metadata Staff;
- OJS Operator;
- Proceeding Editor;
- Publication Manager.

### Publication self-conflict

A Publication Team member who is also Author may perform ordinary downstream operations on an already-eligible own paper where policy allows.

But they may not:
- create/force PUBLICATION_APPROVED;
- bypass PUBLICATION_ELIGIBILITY;
- bypass required review/revision;
- use publication operations to rewrite academic history.

Any sensitive exceptional override remains governed separately.

### Publication correction / preservation

Publication records are not freely hard-deleted.

Corrections to DOI/URL/external references/status use controlled correction with audit.

Substantive post-publication metadata corrections are historical corrections and do not imply the external publication has automatically changed.
