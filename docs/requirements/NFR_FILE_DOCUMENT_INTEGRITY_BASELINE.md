# Non-Functional Requirements — Part 6 File, Document & Data Integrity

**ID:** ICP-REQ-NFR-001-P6  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Objective

Ensure scholarly manuscripts, payment/refund evidence, generated documents, certificates, exports, and other stored artifacts remain version-correct, traceable, access-controlled, integrity-verifiable, and recoverable.

## Versioned evidence

Authoritative file replacement creates a new version/evidence record.

Examples:
- Abstract v1 → Abstract v2;
- Full Paper v1 → Full Paper v2;
- Final Manuscript v1 → Final Manuscript v2;
- Payment Proof #1 → Payment Proof #2.

```text
NEW AUTHORITATIVE UPLOAD
≠
SILENT OVERWRITE
```

Draft replacement may remain more flexible before a version becomes authoritative.

## Immutable authoritative versions

Once a particular file version has been used for an authoritative workflow such as:
- official submission;
- reviewer assignment;
- academic decision;
- publication review;
- publication handoff;
- financial verification evidence;

that version is immutable in normal application workflow.

A new revision creates a new file/version record.

## Internal file identity

User-provided filename is not file identity.

Conceptually separate:
- internal file record ID;
- storage key/reference;
- original filename;
- user-facing download filename.

Internal storage identity should be opaque and application-controlled.

## Filename / path safety

User-provided filenames and path elements must be normalized/sanitized.

Application-controlled storage keys prevent:
- path traversal;
- unsafe filesystem/path assumptions;
- accidental overwrite through duplicate names.

## File-type validation

Allowed-file validation must not trust extension alone.

Validation should consider, as appropriate:
- allowed extension/policy;
- declared MIME type;
- actual content/signature/type characteristics.

Exact validation library is deferred.

## Scanner / quarantine readiness

Upload architecture must be compatible with optional malware/file scanning or quarantine workflows.

Conceptual status may support:
- uploaded;
- validating;
- accepted/safe;
- rejected/quarantined.

V1 does not lock a specific antivirus/scanner provider.

## Protected download authorization

Private files must not become permanent unauthenticated public URLs merely because they exist in storage.

Protected access requires:
- server-side authorization; or
- controlled temporary signed access/equivalent.

Applies to manuscripts, payment/refund evidence, review files, internal documents, and other private artifacts.

## Double-anonymous isolation

```text
ORIGINAL IDENTITY-BEARING FILE
≠
DOUBLE-ANONYMOUS REVIEWER PACKET
```

Reviewer access is resource-specific and must not permit URL/identifier manipulation to access identity-bearing originals.

## Integrity fingerprint

Important stored artifacts should have an integrity fingerprint/checksum or equivalent verification capability.

Purpose:
- detect corruption;
- detect unexpected replacement;
- validate restore integrity;
- support incident investigation.

Exact algorithm is deferred to implementation.

## Database ↔ storage consistency

The platform must support reconciliation of partial failures such as:
- DB record says file exists but storage write failed;
- file stored but DB transaction failed;
- referenced artifact missing;
- orphaned stored file.

The design need not require a distributed transaction but must be recoverable and diagnosable.

## Orphan / temporary cleanup

Temporary/abandoned artifacts may be cleaned up according to policy.

Deletion must verify resource references/lifecycle state first.

```text
DELETE STORED FILE
only after
REFERENCE / LIFECYCLE VALIDATION
```

## Temporary vs authoritative artifacts

Temporary artifacts such as:
- preview PDFs;
- transient exports;
- conversion output;
- incomplete uploads;

are distinguished from authoritative evidence/artifacts.

Temporary artifacts may use shorter retention.

## Generated-document provenance

Important generated documents should preserve enough provenance to understand what produced them.

Examples:
- Certificate;
- LoA;
- Decision Letter;
- publication metadata/export package.

Candidate provenance:
- source resource/snapshot;
- recipient/contributor snapshot;
- template/version;
- relevant decision/reference;
- generated_at;
- generator/version where useful.

Byte-identical reproduction is not required, but provenance must be traceable.

## Template/version integrity

Updating the current certificate/LoA/document template must not silently alter previously issued/generated historical documents.

Historical records preserve:
- generated artifact; and/or
- template/version/snapshot sufficient to represent historical output accurately.

## Certificate artifact integrity

A certificate artifact is tied to its certificate record, including as applicable:
- certificate identity;
- certificate number;
- verification identity/token;
- recipient snapshot;
- activity date;
- template/version;
- issuance/reissue state.

Reissue creates the appropriate new credential/artifact history rather than silently mutating the historical one.

## Important file metadata

Important file records should support metadata such as:
- internal identity;
- original filename;
- media/content type;
- size;
- storage reference;
- uploaded/generated by;
- uploaded/generated at;
- related resource;
- version;
- integrity reference/checksum;
- validation/scanning status.

Not all metadata is public.

## User-friendly download names

Authorized downloads may expose a meaningful filename such as:

```text
ICP2027_Submission-015_Final-Manuscript.pdf
```

while internal storage naming remains opaque.

## File retention

File retention follows:
- purpose;
- parent-resource lifecycle;
- historical integrity needs;
- privacy/retention policy.

Historical records must not silently lose required evidence simply because a separate file cleanup schedule ran.

## Authoritative-file deletion

Files that served as authoritative evidence are not freely hard-deleted in normal UI.

Examples:
- manuscript version used in review;
- payment proof used in verification;
- refund evidence;
- published final manuscript;
- issued certificate artifact.

If later policy permits/mandates deletion, the action must preserve truthful historical state and follow controlled retention/privacy procedures.

## Storage failure behavior

Storage errors must fail safely.

A user must not receive a success result before required durable storage is confirmed.

Missing/unavailable expected artifacts are observable operational incidents.

## Backup / restore verification

Disaster-recovery verification must include stored artifacts, not only database restoration.

Post-restore verification should be able to check that expected records/artifacts still correspond through identifiers and integrity metadata.

## Restricted-file access audit

Exceptional access to highly restricted files can be audited where applicable.

Example:
- break-glass download of refund evidence.

Ordinary low-risk downloads need not automatically become business audit events.

## Export / publication package integrity

Exports and publication packages must bind to explicit resource/version/query context.

Publication package metadata + file must reference the manuscript version actually approved for handoff.

Exports must not accidentally mix stale/new file versions or unrelated records because of paging/filter race.

## Deferred implementation details

Phase 0 does not lock:
- object-storage provider;
- checksum algorithm;
- antivirus/scanner provider;
- signed-URL technology;
- file metadata database shape;
- cleanup scheduler;
- exact retention periods;
- PDF generation library;
- exact file-size/type limits.

## Next

Part 7 defines Accessibility, Usability & Responsive Experience.
