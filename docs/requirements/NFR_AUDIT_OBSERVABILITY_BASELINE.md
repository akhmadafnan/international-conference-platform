# Non-Functional Requirements — Part 5 Auditability, Logging, Monitoring & Observability

**ID:** ICP-REQ-NFR-001-P5  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Objective

Define product-level traceability and operational observability so the platform can answer both:
- who changed authoritative business state and why; and
- what technically failed, slowed, retried, or became unavailable.

## Separation of concerns

```text
BUSINESS AUDIT TRAIL
≠
TECHNICAL APPLICATION LOG
≠
METRICS / MONITORING
≠
ALERT / INCIDENT SIGNAL
```

These layers may correlate with each other but serve different purposes.

## Business audit trail

Business audit records durable governance evidence for authoritative/sensitive actions.

Where relevant, audit captures:
- actor;
- action;
- resource type/reference;
- edition/scope;
- timestamp;
- prior state/value;
- resulting state/value;
- reason/justification;
- source/context/reference.

## Audit immutability

Business audit is append-only/immutable in normal application workflow.

Ordinary administrators do not freely edit or delete audit events.

Correction pattern:

```text
ORIGINAL EVENT
+
CORRECTION / SUPERSESSION EVENT
```

not historical rewrite.

## Minimum auditable domains

At minimum, audit applies where relevant to:

### Authentication / security
- recovery;
- primary-email change;
- MFA/security changes;
- protected role/capability assignment/revocation;
- break-glass.

### Submission
- official submission;
- administrative screening;
- withdrawal;
- protected contributor correction.

### Finance
- payment verification;
- refund execution;
- financial correction/override.

### Academic
- reviewer assignment/reassignment;
- COI state;
- review submission/reopen;
- academic decision/correction.

### Event
- published schedule change;
- presenter substitution;
- PRESENTED/NO_SHOW;
- presentation exception/makeup.

### Publication
- publication decision where applicable;
- eligibility override;
- OJS/proceedings transfer;
- publication status/reference correction.

### Certificate
- manual/bulk issuance;
- revoke;
- reissue.

### Archive
- closeout;
- archive;
- historical correction;
- unarchive.

### Permission governance
- assignment;
- activation;
- scope change;
- expiry;
- revocation;
- reassignment.

Not every ordinary page view is a business audit event.

## Exceptional sensitive-read audit

The architecture must support auditing exceptional access to RESTRICTED data.

Examples:
- break-glass access to refund-bank information;
- emergency access to reviewer-confidential records;
- privileged historical/security investigation.

Ordinary low-risk reads do not need to become noisy business audit events by default.

## Technical structured logging

Technical logs should be structured and include safe operational context such as:
- timestamp;
- environment;
- severity;
- request/correlation ID;
- component;
- event/error code;
- safe resource/reference ID;
- exception/error class.

Logs should avoid vague uncorrelated messages.

## Correlation / request identity

Important request/job/integration chains should be traceable through correlation context.

Example:

```text
HTTP request
→ business transaction
→ queued job
→ email/integration attempt
```

This enables operators to distinguish:
- business operation succeeded;
- downstream notification/integration failed.

## Log severity

Conceptual technical levels may include:
- DEBUG;
- INFO;
- WARNING;
- ERROR;
- CRITICAL.

Production should not retain unlimited verbose DEBUG output indefinitely.

Exact logging framework/levels remain implementation details.

## Log privacy / redaction

Logs must not expose plaintext:
- passwords;
- OTP/magic-link tokens;
- secrets/API credentials;
- full bank/refund data;
- raw payment-proof content;
- confidential reviewer text;
- unnecessary manuscript payloads;
- unnecessary PII.

Prefer IDs/references and redacted diagnostics.

## Health monitoring

Operational monitoring must be able to determine health of critical components such as:
- application;
- database;
- storage;
- critical background jobs/queues where used;
- backup freshness;
- critical integration delivery status.

Health endpoints/output must not expose secrets or unnecessary infrastructure internals.

## Metrics / observable signals

Where applicable, observe:
- request latency;
- error rate;
- HTTP 5xx;
- failed jobs;
- queue backlog;
- slow database operations;
- integration latency/failure;
- storage/file failures;
- notification delivery failure;
- backup success/failure/freshness.

Operational business queues may also expose safe counts, but monitoring is not authoritative business state.

## Actionable alerting

Alert on conditions requiring intervention rather than every minor warning.

Candidate actionable conditions:
- application unavailable;
- database/storage unavailable;
- backup failed/stale;
- materially elevated error rate;
- critical queue/job processing stalled;
- critical integration repeatedly failing;
- repeated certificate/document generation failure.

Alert detail must remain privacy/security safe.

## Critical-window awareness

Alert severity/escalation may account for edition critical periods such as:
- submission deadline;
- payment deadline;
- conference day;
- other configured critical windows.

A normally tolerable degradation may become urgent during a critical window.

## Audit searchability

Authorized users/auditors should be able to search/filter audit events by:
- edition;
- actor;
- action;
- resource;
- domain;
- time range.

Audit visibility itself follows permission/privacy rules.

## Audit export

Sensitive audit export is permission-restricted.

Where materially sensitive, the export action itself should be auditable.

No broad default download-all-audit capability is assumed.

## Retention separation

```text
BUSINESS AUDIT RETENTION
≠
TECHNICAL LOG RETENTION
```

Business audit may require longer governance/history retention than diagnostic logs.

Exact retention periods remain deferred pending organizational/legal policy.

## Bounded technical logs

Technical logging must support rotation/retention/archival so logs cannot grow indefinitely and exhaust production storage.

## Monitoring independence

Monitoring/observability is not an authoritative business-data store.

A monitoring-provider outage must not cause core business transactions to fail solely because monitoring is unavailable.

## Time consistency

Internal system/audit timestamps must be consistent and unambiguous.

User/event display may convert to:
- edition timezone;
- user-facing timezone context.

Exact database/time type is deferred.

## User-facing error reference

Unexpected production errors should provide a safe reference/correlation identifier that users/FO can report.

Internal operators can correlate the reference with diagnostics without exposing stack traces or internal secrets to the user.

## Deployment traceability

Production deployment history should preserve:
- version/commit/release identity;
- environment;
- deployed_at;
- deployment status/result.

Exact CI/CD provider is deferred.

## Incident reconstruction

Timestamps, audit events, request/job correlation, deployment history, and monitoring signals should support reconstruction of significant incident timelines.

## Observability verification

Before production, representative scenarios should verify that expected audit/alerts/signals are generated.

Examples:
- backup failure;
- failed background job;
- integration delivery failure;
- protected override;
- break-glass;
- role revocation;
- authoritative correction.

Observability that has never been exercised is not sufficient evidence of readiness.

## Deferred implementation details

Phase 0 does not lock:
- log/metrics provider;
- APM vendor;
- tracing stack;
- incident-management provider;
- alert-delivery provider;
- exact retention durations;
- dashboard product;
- audit-storage technology.

## Next

Part 6 defines File, Document & Data Integrity.
