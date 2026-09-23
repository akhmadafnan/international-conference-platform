# Permission Model — Part 3 Participant-Side Roles

**ID:** ICP-REQ-PERM-001-P3  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-23

## Relationship-driven access

```text
PERSON
→ EDITION MEMBERSHIP
→ RESOURCE RELATIONSHIP
→ EFFECTIVE PARTICIPANT-SIDE PERMISSION
```

Being a Participant in an edition does not grant access to all edition submissions.

## Visitor

Public-only access:
- conference public pages;
- CFP/guidelines;
- published schedule;
- public speaker data;
- FAQ/announcements;
- public certificate verification;
- published proceedings/publication links.

No protected participant/submission/payment/review access.

## Prospective Participant

May:
- register/activate account;
- verify email;
- complete current profile;
- read policies;
- request/join edition.

Private edition participant capabilities require active membership.

## Participant

Scope:
- own account;
- own edition membership.

May:
- view/update current profile;
- view own membership/registration;
- view own participant-relevant announcements/agenda;
- view own check-in/attendance;
- access own eligible certificates;
- view own relevant communication/history.

Participant membership alone grants no submission-management, review, Finance, Event-authority, or Publication-authority permission.

## Author vs Corresponding Author

`Author` = scholarly contributor relationship.

`Corresponding Author` = primary submission manager/contact.

### Corresponding Author baseline

Subject to resource state, may:
- create/manage draft;
- edit submission metadata;
- manage contributor list before controlled states;
- upload abstract/files;
- submit to payment;
- upload own payment proof;
- view payer-facing status/action-required reason;
- view academic decision;
- view author-facing reviewer comments;
- submit revisions;
- upload Full Paper/final manuscript;
- designate presenter where allowed;
- complete publication metadata/declarations;
- request withdrawal;
- view LoA and correspondence.

### Explicit exclusions

Does not receive:
- anonymous reviewer identity;
- confidential-to-editor comments;
- internal COI notes;
- academic deliberation;
- unrelated submissions;
- bank mutation/reconciliation data;
- internal Finance notes.

## Co-author / Contributor

May exist without an account.

Accountless contributor:
```text
metadata relationship exists
→ no authenticated workspace access
```

Even when account-linked, contributor status does not automatically confer Corresponding Author management rights.

## Submission Collaborator / Delegation

Use a submission-scoped delegated relationship.

Candidate permissions:
- VIEW;
- EDIT_METADATA;
- UPLOAD_FILES;
- RESPOND_REVISION.

Sensitive delegated actions may require additional confirmation/authority.

Final delegation/assignment governance is handled in Part 9.

## Presenter

Submission/presentation-scoped.

May:
- confirm designation;
- view assigned session/room/date/time;
- view presentation instructions;
- access presentation-material workflow where enabled;
- view own presenter status;
- access eligible presenter certificate.

Cannot self-set authoritative PRESENTED.

## Presenter + Author

Permissions combine according to each resource relationship and lifecycle state.

## Exceptional non-author Presenter

Receives only presentation-relevant access.

No automatic:
- manuscript editing;
- payment detail;
- reviewer comments;
- publication revision;
- contributor management.

## Non-presenting Participant

Participant/member capabilities only.

May use own check-in/attendance and eligible participant-certificate workflow.

No presentation/submission authority arises merely from attendance.

## Invited Speaker / Keynote

Edition-scoped special participant role.

May manage relevant own:
- speaker profile/bio;
- affiliation;
- photo where supported;
- talk title/description;
- availability/confirmation;
- assigned session;
- presentation information;
- eligible speaker certificate.

Does not imply:
- Conference Admin;
- Finance;
- Academic Committee;
- Reviewer;
- Event authority;
- Publication authority.

## Review confidentiality

Author-facing review view must exclude:
- reviewer identity when anonymity applies;
- confidential editor comments;
- internal recommendation deliberation;
- reviewer invitation/decline history;
- internal COI notes.

## Finance confidentiality

Participant/Author may access payer-facing finance data for their own submission where applicable.

They do not receive:
- bank mutation/reconciliation records;
- internal Finance notes;
- other participants' payments.

## Current profile vs historical snapshot

Current profile changes do not silently mutate:
- submitted-version snapshots;
- accepted historical metadata;
- publication snapshot;
- certificate record;
- archived edition record.

## Withdrawal vs deletion

Before official submission:
- draft cancellation/deletion may be policy-allowed.

After official submission:
- no unrestricted hard delete;
- formal withdrawal/status workflow;
- historical audit preserved.

## Contributor changes

Before official submission:
- Corresponding Author may manage contributors subject to policy.

After official submission:
- add/remove/reorder/change contributor becomes controlled correction;
- later stages may require stricter evidence/authority.

## Participant privacy

Same-edition membership does not grant protected access to other participants' submissions or workspaces.

Publicly published schedule/proceedings data remains governed separately by public-content policy.

## Next

Part 4 defines Finance and Refund permissions.
