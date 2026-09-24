# Permission Model — Part 8 Certificate & Archive

**ID:** ICP-REQ-PERM-001-P8  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Certificate capability model

Certificate authority is expressed through capabilities, not by assuming a broad administrator can do everything.

Conceptual permissions:
- certificate.configure;
- certificate.issue;
- certificate.issue_manual;
- certificate.issue_bulk;
- certificate.revoke;
- certificate.reissue;
- certificate.view_history.

V1 may grant these capabilities to Conference Admin or another designated operator.

## Issuance paths

```text
RULE_BASED
MANUAL
BULK_MANUAL
REISSUE
```

Rule-based issuance consumes authoritative lifecycle eligibility.

Manual issuance may target:
- existing account;
- existing participant;
- external/manual recipient.

## Manual Certificate Builder

Authorized fields may include:
- recipient name;
- institution/affiliation;
- optional email;
- conference/edition/activity;
- certificate type;
- role/recognition label;
- certificate title;
- custom wording;
- activity/event date;
- template;
- signer(s);
- number policy;
- internal note/reason.

## Date semantics

```text
displayed certificate date
= configured activity/event date

technical created/generated timestamps
= truthful internal audit timestamps
```

Technical timestamps do not need to be shown on normal PDF/public verification output.

## Provenance

Internally distinguish at least:
- RULE_BASED;
- MANUAL;
- BULK_MANUAL;
- REISSUE.

## Bulk issuance

Bulk does not create one shared credential record.

Each recipient receives:
- certificate record;
- certificate number/identifier;
- unique verification token;
- unique verification URL;
- independent status/history.

## Public verification

Public output should expose only minimum credential metadata:
- status/validity;
- recipient;
- certificate type/role;
- event/edition;
- activity/event date;
- certificate number.

Do not expose internal notes, issuer audit, technical timestamps, account IDs, phone/email, or unrelated PII by default.

## QR

QR points to or represents the verification identity/link.

Do not encode unrestricted recipient PII into the QR payload.

## Issued certificate correction

Once issued, certificate material data is not freely edited in place.

```text
VALID
→ REVOKED / SUPERSEDED
→ REISSUED
```

Prior verification URL remains resolvable and indicates its historical status.

## Record preservation

Issued certificates are not freely hard-deleted.

## Certificate vs underlying lifecycle

Manual certificate issuance does not automatically change:
- attendance;
- PRESENTED/NO_SHOW;
- academic decision;
- publication eligibility;
- publication status.

Certificate is its own credential/document domain.

## Presenter Certificate

Rule-based Presenter Certificate follows approved presentation eligibility.

Exceptional/manual Presenter Certificate requires the separately authorized exception/manual path and audit.

It does not silently rewrite presentation history.

## Configuration vs issuance

```text
certificate.configure
≠
certificate.issue
```

The permission model may allow template configuration without issuance rights or issuance rights without broad edition administration.

## Self-issuance

Privileged/manual recognition certificates are not normally self-issued by the certificate operator.

Another authorized issuer or controlled override is required.

Automatic rule-based issuance after objective eligibility is not treated as manual self-approval.

## Edition closeout

Closeout uses a formal checklist.

Candidate checks:
- event complete;
- presentation records reconciled;
- refund cases reviewed;
- publication queue reviewed;
- certificates reviewed;
- exceptions reviewed;
- final notes.

Items may be:
- INFO;
- WARNING;
- BLOCKER;

according to edition policy.

## Closeout and publication tail

Event closeout may occur while downstream publication work continues if edition policy permits.

The system must not require publication status to be falsified merely to close the event operation.

## Archive default

ARCHIVED is read-only by default.

Normal edits to:
- submissions;
- payments/refunds;
- reviews/decisions;
- schedules;
- attendance/presentation;
are denied.

```text
ARCHIVE
= PRESERVE + RESTRICT MUTATION
```

not delete.

## Post-archive controlled operations

Special authorized capabilities may remain available without reactivating the edition:
- certificate.issue_manual;
- certificate.issue_bulk;
- certificate.revoke;
- certificate.reissue;
- publication-reference correction;
- historical.correct.

## Historical certificate issuance

Authorized operators may create certificates for a historical archived edition using the configured activity/event date while retaining current technical generation timestamps internally.

## Historical correction

historical.correct is dedicated and audited.

Record:
- resource;
- prior value/state;
- new value/state;
- reason;
- actor/authority;
- timestamp;
- optional evidence/reference.

There is no unrestricted archive.edit_all baseline.

## Archive authority

Distinct conceptual permissions:
- edition.closeout.manage;
- edition.archive;
- historical.correct;
- edition.unarchive.

V1 may assign archive authority to Conference Admin and/or Super Admin according to policy.

## Unarchive

Unarchive is exceptional and privileged.

Requires:
- explicit reason;
- authority;
- timestamp;
- audit.

Ordinary historical certificate/correction tasks should not require unarchive.

## Archive preservation

Archive retains:
- participants;
- submissions;
- payments/refunds;
- reviews/decisions;
- sessions/schedules;
- attendance/presentation;
- publication;
- certificates;
- audit history.

## Summary matrix

| Action | Certificate Authority | Conference Admin | Super Admin | Public/Participant |
|---|---|---|---|---|
| Configure certificate template | Capability-based | If granted | Oversight/policy | Deny |
| Rule-based issue | Authorized/system | If granted | Deny default | Own receive |
| Manual individual issue | Allow | If granted | Deny default | Deny |
| Bulk issue | Allow | If granted | Deny default | Deny |
| Revoke/reissue | Controlled allow | If granted | Exceptional/oversight | Deny |
| Public verify | N/A | N/A | N/A | Allow |
| Internal certificate history | Authorized | Edition scope if granted | Authorized/global | Deny |
| Manage closeout | Deny default | Allow | Oversight | Deny |
| Archive edition | Deny default | If authorized | If authorized | Deny |
| Normal edit archived data | Deny | Deny | Deny default | Deny |
| Issue cert for archived edition | Allow | If granted | Policy-dependent | Deny |
| historical.correct | Special capability | If granted | Authorized | Deny |
| unarchive | Deny default | Privileged only | Privileged | Deny |

## Next

Part 9 defines assignment, revocation, COI, overrides, and break-glass governance.
