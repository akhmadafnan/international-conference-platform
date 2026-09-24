# Publication Handoff, Certificate Verification & Archive Baseline

**ID:** ICP-REQ-LIFE-4F-001  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-23

## Publication handoff

```text
PUBLICATION_ELIGIBLE
→ PUBLICATION_QUEUE
→ FINAL_METADATA_VALIDATION
→ READY_FOR_TRANSFER
→ manual/assisted OJS/proceedings handoff
→ TRANSFERRED
→ IN_PUBLICATION_PROCESS
→ PUBLISHED
```

### V1 rule

OJS/proceedings handoff is manual/assisted. API integration is not required for V1.

Publication Team may record:
- destination publication/proceedings;
- external OJS submission/reference ID;
- transferred_at / transferred_by;
- publication status;
- DOI when assigned;
- publication URL;
- ISBN/ISSN or equivalent publication references where applicable;
- notes/problem state.

These identifiers are external references, never internal primary keys.

## Publication status separation

```text
PUBLICATION_ELIGIBLE ≠ PUBLISHED
```

Suggested V1 transfer/publication states:
- NOT_TRANSFERRED;
- READY_FOR_TRANSFER;
- TRANSFERRED;
- IN_PUBLICATION_PROCESS;
- PUBLISHED;
- PUBLICATION_WITHDRAWN;
- PUBLICATION_FAILED.

Exact physical enum design is deferred.

## Final publication metadata snapshot

Before/at handoff, the platform preserves a publication snapshot including relevant:
- title;
- author/contributor order;
- affiliations;
- optional ORCID where provided;
- abstract/keywords;
- language;
- final manuscript/version reference;
- publication consent/declarations;
- destination-related metadata.

Later profile edits do not silently rewrite the historical publication snapshot.

## Certificate verification

Each certificate has:
- certificate record;
- certificate number/canonical identifier;
- verification code/token;
- verification URL;
- QR representation pointing to verification;
- status/history.

Public verification may expose only necessary information such as:
- validity;
- recipient;
- certificate type;
- event/edition;
- activity/event date;
- certificate number.

Internal technical timestamps/notes remain non-public unless policy requires otherwise.

## Certificate correction

Issued certificate data is not silently overwritten.

```text
VALID CERTIFICATE
→ correction needed
→ REVOKED / SUPERSEDED
→ REISSUED certificate
```

The system preserves reason, authority, timestamps, and linkage/history.

## Manual Certificate Builder

Previously approved behavior remains:
- individual issuance;
- bulk/collective issuance;
- existing or external/manual recipient;
- configurable activity/event display date;
- configurable type/wording/template/signers;
- unique verification identity per certificate;
- immutable internal creation/generation timestamps.

## Edition closeout

Edition closeout is a formal process.

Candidate checklist:
- event marked completed;
- presentation records reconciled;
- open refund cases reviewed;
- publication queue reviewed;
- certificate processing status reviewed;
- financial reconciliation acknowledged where required;
- open support/exception cases reviewed;
- archival notes completed.

Each item may be:
- informational;
- warning;
- hard blocker;

according to edition policy.

An edition may close operationally while publication items remain in process when policy allows; the system must preserve those downstream publication workflows.

## Archive

```text
ACTIVE
→ CLOSING
→ CLOSED
→ ARCHIVED
```

Exact physical states are deferred.

ARCHIVED means:
- preserved, not deleted;
- primarily read-only;
- still queryable according to authorization;
- historical participant/submission/payment/refund/review/schedule/presentation/publication/certificate data retained;
- later corrections use controlled workflow with reason, authority, and audit trail.

## Source-of-truth boundary

Conference platform remains authoritative for:
- conference eligibility;
- payment/refund;
- academic decisions;
- presentation;
- publication eligibility;
- certificate records;
- edition archive/history.

OJS/proceedings systems remain downstream publication systems.


## Approved archive permission boundary

Archive is read-only by default.

Normal mutation of archived submission/payment/refund/review/decision/schedule/presentation records is denied.

Special post-archive capabilities may remain available without reopening the entire edition:
- certificate manual/bulk issuance;
- certificate revoke/reissue;
- publication-reference correction;
- controlled historical correction.

### Historical correction

Historical correction is a dedicated capability and must record:
- target resource;
- previous value/state;
- new value/state;
- reason;
- authority;
- timestamp;
- evidence/reference where applicable.

### Unarchive

Unarchive is exceptional and privileged.

It must not be required merely to issue/reissue a historical certificate or make an authorized narrow historical correction.

### Preservation

ARCHIVED remains:
- retained;
- queryable according to authorization;
- mutation-restricted;
- auditable.

Archive is never equivalent to deletion.
