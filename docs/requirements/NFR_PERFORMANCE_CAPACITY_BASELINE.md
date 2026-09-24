# Non-Functional Requirements — Part 4 Performance, Capacity & Scalability

**ID:** ICP-REQ-NFR-001-P4  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Objective

Define proportionate performance/capacity expectations for an initial approximately 100-submission conference while preventing design choices that become fragile as future editions and records accumulate.

## Interactive response target

For common business interactions under normal production load:

```text
target response time ≤ 2 seconds
normal upper expectation ≤ 3 seconds
```

This applies to common dashboard/list/detail/status interactions, not inherently heavy operations such as large exports, bulk certificate generation, or large file upload/processing.

## Capacity baseline

The initial product must be designed and verified for at least:

```text
50 concurrent active users
```

performing common workflows without severe degradation, corruption, duplicate business effects, or lost state.

The design must remain reasonable when an edition grows from roughly 100 submissions into several hundred records/submissions.

## Deadline burst behavior

Submission/payment/review/full-paper deadlines may produce short bursts of saves, uploads, finalization requests, and refreshes.

Burst load must not cause:
- duplicate submissions;
- double payment/refund effects;
- duplicate certificates;
- lost updates;
- corrupted state.

Authoritative business completion should not wait on unrelated slow external delivery such as email.

## Pagination / bounded retrieval

Large operational lists must be bounded/paginated.

Examples:
- participants;
- submissions;
- payments/refunds;
- reviews;
- publication queue;
- certificates;
- audit/history records.

Unbounded full-table rendering is not acceptable.

Exact page size is a later UI/configuration decision.

## Edition-scoped queries

Operational workflows should query the selected/current edition explicitly.

Retained historical editions must not be processed unnecessarily for each current-edition request.

Historical data remains available through explicit history/archive views.

## Search and filtering

Common operational search/filtering should be server-side and index-ready.

Candidate searchable/filterable fields include:
- submission/reference code;
- participant/author;
- email where authorized;
- payment status;
- academic status;
- presenter;
- review stage;
- publication status;
- certificate number.

Dedicated external search infrastructure is not mandatory for V1 if database-native approaches meet the targets.

## Dashboard efficiency

Dashboards should use bounded/aggregate queries rather than loading entire datasets into application memory.

Growth in historical editions should not cause avoidable linear processing on every dashboard load.

## Heavy operations

Operations likely to exceed normal interactive timing must be asynchronous-capable.

Examples:
- bulk certificate generation;
- mass notification;
- large export;
- PDF/document generation;
- publication package generation;
- large import.

Conceptual flow:

```text
REQUEST
→ ACCEPTED / QUEUED
→ PROCESSING
→ SUCCEEDED / PARTIAL / FAILED
```

Exact queue/job technology is deferred.

## File upload UX and behavior

Uploads must provide:
- clear in-progress state;
- clear success confirmation only after storage is confirmed;
- clear failure state;
- size/type validation;
- safe retry behavior;
- bounded resource usage.

A failed upload must not appear as successful.

## Bounded file handling

Large files must not require unbounded application-memory loading.

Streaming/direct-storage/bounded-buffer or equivalent strategies may be selected during architecture.

## Upload limits

Upload limits are configurable by requirement/type.

Examples may differ for:
- abstract;
- Full Paper;
- final manuscript;
- payment proof;
- presentation material.

Infrastructure may enforce an overall safety ceiling.

Exact sizes are deferred to feature specification.

## Bulk processing

Bulk certificate/export/notification workflows should support, as appropriate:
- batch/job identity;
- total count;
- processed count;
- succeeded count;
- failed count;
- retryable items;
- partial completion visibility.

Each certificate remains an independent credential record even when generated in bulk.

## Notification fan-out

Large notification sends should be asynchronous/batched as needed rather than blocking one web request.

Provider rate limits should be respected.

## Cache policy

```text
CACHE
≠
AUTHORITATIVE BUSINESS STATE
```

Caching may optimize:
- public content;
- configuration;
- aggregates;
- frequently read lookups.

Authoritative states such as PAID, ACCEPTED, PRESENTED, and PUBLICATION_ELIGIBLE originate from persistent source-of-truth data.

A stale cache must not create an incorrect business decision.

## Architecture scale stance

Scalability does not require microservices.

A modular monolith or single-application architecture is acceptable if it meets:
- correctness;
- security;
- reliability;
- performance;
- maintainability;
- future scaling needs.

## Incremental scaling strategy

Preferred order:

```text
efficient schema/query/indexing
→ bounded pagination
→ background jobs
→ caching
→ storage optimization
→ infrastructure vertical/horizontal scaling when evidence justifies it
```

Do not add distributed-system complexity without demonstrated need.

## Performance observability

The production system must support visibility into:
- slow requests;
- failed requests;
- slow/failed jobs;
- queue backlog if background queues are used;
- slow database operations;
- integration latency/failure.

Detailed observability/logging requirements continue in NFR Part 5.

## Performance regression verification

Before first production launch and major releases, critical workflows should receive performance sanity/regression verification against realistic representative data/load.

Not every change requires a full load test, but major changes must not silently make core workflows materially slower.

## Deferred details

Phase 0 does not lock:
- server sizing;
- autoscaling provider;
- queue technology;
- cache product;
- database engine/index implementation;
- CDN provider;
- external search engine;
- exact upload size values;
- exact page size;
- load-test tool.

Architecture/specification must demonstrate compliance with the approved behavior.

## Next

Part 5 defines Auditability, Logging, Monitoring & Observability.
