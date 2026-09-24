# Non-Functional Requirements — Part 3 Reliability, Availability, Backup & Disaster Recovery

**ID:** ICP-REQ-NFR-001-P3  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Objective

Define proportionate reliability and recovery behavior for an initially small conference platform without requiring enterprise-scale infrastructure prematurely.

The target is:
- recoverable;
- safe during deadline peaks;
- usable on conference day;
- resilient to external integration failure;
- protective of authoritative conference data.

## Availability objective

Production monthly availability objective:

```text
≥ 99.5%
```

excluding announced scheduled maintenance.

This is a service objective for V1, not a guarantee that forces multi-region active-active architecture.

## Critical windows

Planned maintenance should be avoided during critical periods such as:
- submission deadline;
- payment deadline;
- review deadline;
- Full Paper deadline;
- conference/event day;
- other edition-defined high-risk windows.

Scheduled maintenance outside critical windows should be communicated operationally.

## External dependency isolation

```text
EXTERNAL SERVICE FAILURE
≠
CORE BUSINESS DATA LOSS
```

Examples:
- email outage must not delete/rollback a submission or academic decision;
- WhatsApp outage must not invalidate payment records;
- OJS outage must not erase PUBLICATION_ELIGIBLE or Publication Queue data;
- PDF/certificate rendering outage should not break submission/review domains.

External delivery state is separate from authoritative business state.

## Consistency-safe critical operations

Critical state-changing operations must be atomic or otherwise consistency-safe.

Examples include:
- finalize official submission;
- verify payment;
- execute refund;
- record final academic decision;
- verify PRESENTED/NO_SHOW;
- issue/revoke certificate;
- archive/unarchive edition;
- perform protected override/correction.

A partial technical failure must not leave business state silently contradictory.

Implementation technology is deferred.

## Duplicate-safe / idempotent behavior

Repeated requests must not duplicate irreversible or sensitive business effects.

Particular attention:
- submission finalization;
- payment verification;
- refund execution;
- certificate issuance;
- publication transfer/status records;
- notification/integration jobs;
- other retried state-changing operations.

A slow connection or retry must not create duplicate records/effects merely because a button/request is repeated.

## Recovery scope

Production recovery planning covers more than source code.

At minimum consider:
- primary database;
- private uploaded manuscripts/files;
- payment/refund evidence where retained;
- generated certificates/documents where required;
- recovery-critical configuration/state;
- integration/queue state where necessary to restore consistent processing.

Source control is not a substitute for data/file backup.

## Recovery Point Objective (RPO)

Normal production:

```text
RPO ≤ 4 hours
```

Critical edition windows target:

```text
RPO ≤ 1 hour
```

where the selected infrastructure can support that reasonably.

This requirement does not mandate a particular backup technology. Snapshot, continuous/PITR, incremental, or other mechanisms remain architecture decisions.

## Recovery Time Objective (RTO)

Normal production:

```text
RTO ≤ 4 hours
```

Critical edition/event windows target:

```text
RTO ≤ 2 hours
```

The recovery target prioritizes critical platform functions before non-essential ancillary functionality.

## Automated production backup

Production backup must be automated.

Operationally visible state should include at least:
- last successful backup;
- backup failure;
- relevant health/freshness indicator.

Backup failure must generate an actionable operational signal/alert.

Manual backup may exist as an additional safeguard but is not the primary production strategy.

## Restore verification

A backup is not considered operationally trustworthy solely because a file/job exists.

Controlled restore verification is required:
- before first production launch;
- before each major conference edition;
- periodically while production is active.

Target cadence while actively used:

```text
quarterly restore test
```

Restore testing may use a controlled non-production recovery environment.

## Failure-domain separation

At least one recoverable backup path must be separated from the primary production failure domain.

Do not rely solely on a backup located on the same single disk/server/failure point as production.

Exact provider/location is deferred.

## Backup security/privacy

Backups inherit the classification and access restrictions of their source data.

Restricted production data remains restricted in backups.

Backup credentials and locations must not create an informal bypass around the application privacy/security model.

## Database ↔ file integrity

The platform must support verification that stored-file references and actual required artifacts remain consistent.

Examples:
- database references a manuscript but file is missing;
- certificate record references a generated artifact that is unavailable;
- orphaned/unreferenced files accumulate unexpectedly.

The implementation need not continuously scan every file, but integrity must be checkable and operationally recoverable.

## Retry-safe integrations

External integrations and notifications should support explicit delivery/attempt status such as pending/retry/failed/succeeded or equivalent.

Transient failure must be retryable without duplicating authoritative business effects.

## Notification independence

```text
BUSINESS STATE
≠
NOTIFICATION DELIVERY STATE
```

Examples:
- ACCEPTED remains ACCEPTED if email delivery fails;
- PAID remains PAID if a notification fails;
- PRESENTED remains PRESENTED if WhatsApp/email delivery fails;
- issued certificate remains issued if notification delivery fails.

Notification failure is separately tracked/retried/escalated.

## Graceful degradation

Failure of a non-core subsystem should not unnecessarily make unrelated workflows unavailable.

Examples:
- WhatsApp unavailable → core conference workspace remains usable;
- OJS unavailable → registration/review/payment workflows remain usable;
- certificate-rendering failure → academic workflows remain usable.

Architecture should avoid avoidable cascading failures.

## Controlled maintenance

When maintenance requires temporarily blocking transactions, the product should fail safely and communicate controlled unavailability rather than expose technical errors or accept inconsistent writes.

## Conference-day continuity pack

Before event operations, authorized staff should be able to obtain a minimum-necessary operational fallback pack.

Candidate content:
- published schedule;
- sessions/rooms;
- presenter roster;
- participant/check-in reference needed for operations;
- presentation verification checklist.

The pack is an emergency continuity aid, not a replacement source of truth.

Its contents remain subject to privacy/minimum-data rules.

## Post-outage reconciliation

If manual/offline fallback is used during an outage, recovery into the authoritative platform must be controlled and auditable.

Reconciliation should preserve:
- actor;
- source/reference;
- recorded operational time where known;
- reconciliation time;
- reason/outage context;
- authoritative resulting state.

Manual fallback must not become unaudited history rewriting.

## Integrity before availability

During degraded operation, correctness takes priority over accepting unsafe writes.

```text
1. Preserve data integrity/correctness
2. Restore critical functionality
3. Restore ancillary functionality
```

It is preferable to reject/pause an unsafe state-changing request than to accept it into a corrupted/ambiguous state.

## Deferred implementation details

Phase 0 does not lock:
- cloud/hosting vendor;
- database product;
- replication topology;
- backup provider;
- snapshot/PITR technology;
- queue technology;
- exact monitoring provider;
- object-storage provider;
- multi-region design.

Architecture must demonstrate that the approved objectives can be met.

## Next

Part 4 defines Performance, Capacity & Scalability.
