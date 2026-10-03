# ARCH-001 Freeze Audit — Stack + ERD v1

**Document ID:** ICHES-ARCH-AUDIT-001
**Status:** GREEN
**Updated:** 2026-10-02

## Scope audited

- Product Blueprint v1
- V1 lifecycle/payment/academic/publication/authority baselines
- Submission & Scholarly Metadata Contract
- Frontend Product Specification
- Technical Stack Freeze v1
- Architecture Decisions v1
- ERD v1

## Result

| Area | Status | Note |
|---|---|---|
| Product lifecycle coverage | GREEN | Registration through publication/certificates represented |
| Multi-edition | GREEN | Series/Edition scope explicit |
| Payment before submission | GREEN | Registration/Payment separated from Submission |
| Rejected abstract remains participant | GREEN | Academic state does not cancel Registration |
| One Submission identity | GREEN | Abstract/full/revised/publication stay on same submission |
| Contributor without User | GREEN | submission_contributors independent from users |
| Institution-first ROR | GREEN | institutions + affiliation snapshot + subdivision text |
| ORCID optional | GREEN | nullable with verification semantics |
| File versioning | GREEN | stored_files + submission_files + supersession |
| Review simplification | GREEN | no generic multi-round publication-review engine |
| Scheduling | GREEN | sessions/rooms/slots/reviewer assignments |
| Attendance vs Presented | GREEN | separate tables/facts |
| Publication snapshot | GREEN | immutable versioned publication_snapshots |
| OJS/Crossref adapters | GREEN | external to canonical relational model |
| Awards human authority | GREEN | candidate evidence separated from finalization |
| Certificate lifecycle | GREEN | eligibility/generation/issuance/revoke/supersede supported |
| Public verification | GREEN | central token edge model |
| Audit/history | GREEN | append-only activity approach |
| Frontend/backend fit | GREEN | Laravel/Inertia/Vue single codebase |
| Two-week operational simplicity | GREEN | no Redis/microservices/mandatory external APIs |
| V1 infrastructure | GREEN | MySQL/database queue/private storage/PDF abstraction |

## Authorization implementation decision

For accelerated V1:

- use Spatie Laravel Permission with Teams enabled;
- custom team foreign key = conference_edition_id;
- role/permission migrations are UUID-compatible;
- active Edition middleware sets the current permission team;
- Laravel Policies/Gates still apply resource relationship and COI rules;
- global superadmin authority bypasses ordinary role/permission/policy authorization across editions, but never bypasses explicit business-domain integrity rules, validation, state transitions, database constraints, immutable history, or mandatory audit.

This provides edition-scoped authorities without creating a second custom RBAC engine.

## Low-level implementation details that do NOT reopen ARCH-001

The following may be chosen while writing migrations/code:
- exact VARCHAR lengths;
- index names;
- exact enum implementation: backed PHP enum + string column is preferred;
- migration file order;
- exact package patch/minor versions compatible with the frozen stack;
- exact CSS tokens;
- exact queue retry/backoff values;
- exact PDF margin/font setup;
- exact notification email templates.

## Explicitly deferred beyond accelerated V1

- Redis
- object-storage migration if local private storage is sufficient
- mandatory ROR API
- ORCID OAuth
- OJS API push
- Crossref remote deposit
- generalized review-stage/round engine
- advanced search service
- AI scheduling
- AI award selection
- hotel/travel/contract engines

## Gate decision

ARCH-001 — **GREEN / CLOSED**

ARCH-001 remains GREEN / CLOSED as a historical architecture gate.

PLAN-001 was completed and ICHES-DEV-START-001 subsequently passed. Phase 01 Foundation later reached `CLOSED_GREEN` and was merged into `develop` through PR #57.

This file is historical evidence for the ARCH-001 freeze. It must not be interpreted as the authority for the current implementation phase or gate; use `docs/ai-context/CURRENT_STATE.md`.
