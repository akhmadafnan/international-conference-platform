# Non-Functional Requirements — Part 9 Integration Resilience, Notifications & External Service Boundaries

**ID:** ICP-REQ-NFR-001-P9  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Objective

Define safe boundaries between the conference platform and external systems/channels so provider outages, retries, duplicate callbacks, delayed notifications, and vendor changes do not corrupt authoritative conference lifecycle state.

## Source-of-truth boundary

```text
CONFERENCE PLATFORM
= AUTHORITATIVE CONFERENCE LIFECYCLE SOURCE OF TRUTH
```

External systems may provide delivery, enrichment, downstream publication, identity verification, messaging, or future payment services.

External delivery/status does not create conference business authority by itself.

## Business commit vs external delivery

```text
AUTHORITATIVE BUSINESS COMMIT
≠
EXTERNAL DELIVERY SUCCESS
```

Examples:
- PAID remains PAID if email delivery fails;
- ACCEPTED remains ACCEPTED if notification provider times out;
- PUBLICATION_ELIGIBLE remains preserved if OJS is unavailable;
- PRESENTED remains PRESENTED if WhatsApp delivery fails.

Where appropriate, commit authoritative state first and process external delivery asynchronously/retryably.

## Integration operation state

Important integration actions preserve explicit operational state/history such as:
- pending;
- processing;
- succeeded;
- failed;
- retrying;
- cancelled/terminal failure where appropriate.

Exact enum is deferred.

Integration state is distinct from business-domain state.

## Duplicate-safe retry

Timeout/retry must not create duplicate business effects.

Particular attention:
- notification sends containing sensitive one-time actions;
- OJS/publication handoff records;
- Crossref/DOI deposits;
- future payment-provider events;
- callback/webhook processing;
- external identity verification attempts.

Use idempotency/deduplication strategies appropriate to the integration.

## Timeout / bounded retry

Outbound network interactions require:
- connection timeout;
- request timeout;
- bounded retry policy;
- terminal-failure/manual-attention behavior.

Do not retry indefinitely.

Exact values are deferred to architecture/integration specifications.

## Backoff / rate-limit awareness

Transient failures such as:
- HTTP 429;
- HTTP 503;
- network timeout;
- provider throttling;

should use backoff/rate-limit-aware retry rather than aggressive immediate loops.

## Graceful provider degradation

A prolonged provider outage should degrade the affected capability rather than cascade across unrelated domains.

Examples:
- OJS unavailable → registration/payment/review remains available;
- WhatsApp unavailable → platform/email remains available;
- ORCID lookup unavailable → core registration/submission remains available;
- certificate delivery channel failure → issued credential state remains preserved.

Architecture may use queues/circuit-breaker-like behavior or equivalent without locking a specific library.

## Email delivery state

Important email notifications should track at least:
- queued;
- sent/provider-accepted;
- failed.

When provider supports additional signals such as delivered/bounced/complained, they may be recorded as delivery metadata.

```text
PROVIDER ACCEPTED / DELIVERED
≠
HUMAN READ / BUSINESS DECISION
```

Delivery metadata never creates academic/finance/event/publication decisions.

## Authenticated workspace remains authoritative for users

Critical participant-facing status remains visible in the platform workspace even if notification channels fail.

Examples:
- ACCEPTED;
- REVISION_REQUIRED;
- PAYMENT_ACTION_REQUIRED;
- schedule/session information;
- certificate availability;
- publication-related actions/status as appropriate.

```text
EMAIL / WHATSAPP
= NOTIFICATION / SUPPORT CHANNEL

PLATFORM WORKSPACE
= AUTHORITATIVE USER-FACING RECORD
```

## WhatsApp boundary

One WhatsApp number may act as Front Office gateway.

WhatsApp is not authoritative for:
- payment verification;
- refund execution;
- academic decision;
- PRESENTED/NO_SHOW verification;
- publication approval/eligibility;
- privileged certificate governance.

Front Office may guide, explain, resend permitted communications, and escalate.

Provider/channel outage must not stop core conference workflows.

## OJS boundary

OJS remains downstream publication infrastructure.

Canonical direction:

```text
CONFERENCE PLATFORM
→ PUBLICATION_ELIGIBLE
→ PUBLICATION QUEUE
→ OJS / PROCEEDINGS
```

V1 remains manual/assisted.

OJS failure/outage may create pending/failed/manual-action handoff state but must not erase:
- publication eligibility;
- conference academic history;
- presentation history;
- certificate history.

## External identifier policy

External identifiers are references, not internal primary keys.

Examples:
- OJS submission ID;
- DOI;
- Crossref deposit/reference ID;
- ORCID;
- ROR;
- provider message ID;
- external payment transaction ID.

Core internal identity survives provider replacement or missing external IDs.

## ORCID / ROR resilience

ORCID is optional and must not block conference participation/authorship/review/presentation when unavailable.

ROR is enrichment/readiness rather than an always-required live dependency.

Where lookup/verification is unavailable, the workflow uses the approved local/manual fallback policy.

## Supplied vs verified identity

```text
SUPPLIED
≠
VERIFIED / AUTHENTICATED
```

Format-valid ORCID/ROR/external reference must not be presented as authenticated/verified solely because it was entered successfully.

Verification state is explicit when supported.

## Crossref / DOI boundary

Future Crossref/DOI deposit/registration is downstream publication metadata processing.

Deposit failure does not revoke internal academic/publication approval.

Track:
- internal publication snapshot/resource;
- attempt;
- external reference;
- status;
- error category;
- retry/history.

## Future payment-provider callback boundary

Although V1 uses manual bank transfer, future provider callbacks must be treated as untrusted external events.

Before internal Finance state changes, validate as applicable:
- provider authenticity/signature/secret;
- expected transaction/reference;
- internal resource mapping;
- expected state compatibility;
- amount;
- currency;
- duplicate/event identity;
- audit context.

Callback receipt alone never blindly means PAID.

## Webhook security

Webhook endpoints verify authenticity according to provider capability.

A request arriving at a webhook URL is not trusted merely because it reached the endpoint.

Webhook payload processing follows security/privacy/validation requirements.

## Duplicate webhook delivery

Provider delivery may be at-least-once.

Repeated identical events must be safe and must not duplicate side effects.

## Out-of-order provider events

Provider events may arrive out of order.

A late older event must not incorrectly regress internal integration/business state.

Processing must account for provider semantics, event identity/version/time where available, and valid state progression.

## External payload validation

External payload is untrusted input.

Validate as applicable:
- schema/type;
- required fields;
- expected identifier/resource;
- state compatibility;
- amount/currency;
- authenticity/signature;
- allowed value/domain.

Invalid payload must fail safely.

## Provider secrets

Credentials such as:
- API keys;
- webhook secrets;
- SMTP credentials;
- WhatsApp credentials;
- OJS API credentials;
- payment-provider secrets;

must not appear in:
- source control;
- public/client bundles;
- plaintext application logs.

## Minimum-data outbound integration

Only send data required for the integration's defined purpose.

Examples:
- notification provider receives delivery-required recipient/content data, not unrelated Finance evidence;
- OJS receives publication handoff metadata/files, not support/refund/reviewer-confidential data;
- WhatsApp does not receive confidential review data by default.

## Integration traceability

Important integration attempts should preserve:
- integration/provider category;
- operation;
- internal resource/reference;
- external reference where available;
- attempt count/identity;
- status;
- timestamps;
- safe error category/reference.

Avoid retaining unnecessary raw sensitive request/response payloads indefinitely.

## Manual / operational fallback

Critical integrations should have proportional fallback where practical.

Examples:
- OJS V1 manual/assisted handoff;
- failed email remains visible in workspace and may be resent by authorized operator;
- WhatsApp outage falls back to platform/email/support alternatives;
- optional scholarly lookup can use controlled manual entry.

Fallback does not bypass business permission/governance.

## Provider-neutral domain concepts

The domain model should not require one vendor forever.

Examples:
- notification;
- publication handoff;
- external identity/reference;
- external payment reference;
- integration attempt.

Exact adapter/provider architecture is deferred, but vendor replacement should not require rewriting core lifecycle semantics.

## Integration observability

Observe, where applicable:
- provider availability/degradation;
- integration latency;
- repeated failure rate;
- queue/backlog;
- last successful operation;
- webhook/callback failure;
- retry exhaustion.

```text
INTEGRATION HEALTH
≠
CORE APP HEALTH
```

Examples:
- OJS DEGRADED;
- Email DEGRADED;
- Core Platform HEALTHY.

## User-facing failure behavior

Do not expose low-level provider/framework errors to normal users.

Prefer truthful, actionable messaging such as:
- authoritative action succeeded but confirmation delivery is delayed;
- external publication handoff is temporarily unavailable;
- optional identifier lookup is unavailable but the workflow may continue.

Use safe error references where useful.

## Deferred implementation details

Phase 0 does not lock:
- email provider;
- WhatsApp provider/API;
- queue library;
- circuit-breaker library;
- webhook signing scheme beyond provider-supported verification;
- ORCID/ROR API client;
- OJS API implementation;
- Crossref client;
- payment gateway/provider;
- exact timeout/retry/backoff values.

## Next

Part 10 defines Deployment, Configuration, Environment & Production Readiness.
