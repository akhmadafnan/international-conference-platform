# Non-Functional Requirements — Final Baseline

**ID:** ICP-REQ-NFR-001  
**Status:** DONE / GREEN — PRODUCT REQUIREMENT COMPLETE  
**Baseline date:** 2026-09-24  
**Implementation authorization:** NOT GRANTED by this document

## Purpose

This document is the canonical index for the approved product-level non-functional requirements of the International Academic Conference Lifecycle Management Platform.

The baseline is intentionally implementation-neutral. It defines required product behavior, quality, boundaries, and readiness expectations while deferring framework/provider/schema/tool choices to architecture and implementation phases.

## Approved parts

| Part | Domain | Canonical file | Status |
|---|---|---|---|
| 1 | Security & Access Protection | `NFR_SECURITY_ACCESS_BASELINE.md` | APPROVED |
| 2 | Privacy, PII & Sensitive Data Protection | `NFR_PRIVACY_DATA_PROTECTION_BASELINE.md` | APPROVED |
| 3 | Reliability, Availability, Backup & Disaster Recovery | `NFR_RELIABILITY_DR_BASELINE.md` | APPROVED |
| 4 | Performance, Capacity & Scalability | `NFR_PERFORMANCE_CAPACITY_BASELINE.md` | APPROVED |
| 5 | Auditability, Logging, Monitoring & Observability | `NFR_AUDIT_OBSERVABILITY_BASELINE.md` | APPROVED |
| 6 | File, Document & Data Integrity | `NFR_FILE_DOCUMENT_INTEGRITY_BASELINE.md` | APPROVED |
| 7 | Accessibility, Usability & Responsive Experience | `NFR_ACCESSIBILITY_USABILITY_BASELINE.md` | APPROVED |
| 8 | Localization, Internationalization & Arabic RTL Quality | `NFR_LOCALIZATION_RTL_BASELINE.md` | APPROVED |
| 9 | Integration Resilience, Notifications & External Service Boundaries | `NFR_INTEGRATION_RESILIENCE_BASELINE.md` | APPROVED |
| 10 | Deployment, Configuration, Environment & Production Readiness | `NFR_PRODUCTION_READINESS_BASELINE.md` | APPROVED |
| 11 | Compatibility, Browser & Device Support | `NFR_COMPATIBILITY_BROWSER_DEVICE_BASELINE.md` | APPROVED |
| 12 | Maintainability, Testability & Operational Support | `NFR_MAINTAINABILITY_TESTABILITY_BASELINE.md` | APPROVED |

## Requirement count

The Requirement Register contains **271 NFR requirements** across Parts 1–12:

- Security: 16
- Privacy: 19
- Reliability: 21
- Performance: 17
- Observability: 22
- File/Document Integrity: 22
- Accessibility/Usability: 19
- Localization/RTL: 27
- Integration Resilience: 26
- Production Readiness: 26
- Compatibility: 24
- Maintainability/Testability: 32

## Key measurable baselines

- Production monthly availability objective: **≥99.5%**, excluding announced scheduled maintenance.
- Normal **RPO ≤4 hours**.
- Critical-window target **RPO ≤1 hour** where reasonably supported.
- Normal **RTO ≤4 hours**.
- Critical-window target **RTO ≤2 hours**.
- Common interactive request target: **≤2 seconds**, with normal upper expectation **≤3 seconds**.
- Initial concurrency baseline: **at least 50 active concurrent users** on common workflows.
- Accessibility target: **WCAG 2.2 Level AA**.
- Required V1 locales: **id / en / ar**, with Arabic first-class RTL.
- Official desktop browser support: latest **2 stable major versions** of Chrome, Edge, Firefox, Safari at release time.
- Participant/Author experience: **mobile-first**.
- V1 does **not** require microservices, full-offline PWA, native mobile app, enterprise SSO, or provider-specific infrastructure.

## Cross-cutting invariants

The following are non-negotiable across all implementation phases:

```text
SERVER-SIDE AUTHORIZATION
DEFAULT DENY
LEAST PRIVILEGE
SOURCE-OF-TRUTH PRESERVATION
NO SILENT HISTORY REWRITE
BUSINESS STATE ≠ NOTIFICATION DELIVERY
PUBLICATION_APPROVED ≠ PUBLICATION_ELIGIBLE
CHECKED_IN ≠ PRESENTED
ARCHIVED ≠ DELETED
CACHE ≠ AUTHORITATIVE BUSINESS STATE
EXTERNAL ID ≠ INTERNAL PRIMARY KEY
UI LOCALE ≠ SCHOLARLY CONTENT LANGUAGE
ACCOUNT CLOSURE ≠ ERASE ALL HISTORICAL FACTS
```

## Implementation deferrals

The following remain intentionally deferred and are not gaps in this product baseline:

- framework/runtime selection;
- database engine/schema/index design;
- authentication/MFA provider and session implementation;
- authorization library/policy implementation;
- hosting/cloud/deployment provider;
- queue/cache/search products;
- object storage/file-scanning provider;
- logging/APM/monitoring provider;
- exact backup technology;
- exact timeout/retry/backoff values;
- exact retention periods pending policy/legal basis;
- exact upload limits/page sizes;
- exact browser physical-device matrix;
- exact testing/static-analysis/CI tooling;
- exact maintenance cadence;
- physical RBAC/ABAC schema and database constraints.

These must be resolved in architecture/specification/implementation without weakening the accepted product behavior.

## Closure

The full consistency audit is recorded in `NFR_CONSISTENCY_AUDIT.md`.

Result:

```text
GREEN
NO UNRESOLVED CRITICAL PRODUCT-LEVEL NFR GAP
NO UNRESOLVED CROSS-PART CONTRADICTION
REQ-NFR-001 = DONE
```

This closure does not authorize application implementation. Phase 0 remains in progress until the broader governance/readiness gate is completed.
