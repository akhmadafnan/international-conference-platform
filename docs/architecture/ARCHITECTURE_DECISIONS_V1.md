# Architecture Decisions v1

**Document ID:** ICHES-ARCH-ADR-001
**Status:** FROZEN FOR V1
**Updated:** 2026-09-30

## ADR-001 — Modular monolith

Decision:
One Laravel application contains public site, authenticated workspace, admin/backoffice, jobs, documents, and integration adapters.

Reason:
Fastest path to an operational V1 with one authorization model and one transactional database.

## ADR-002 — Inertia instead of internal REST SPA

Normal first-party page flows use Laravel routes/controllers + Inertia props.

REST/API endpoints are added only when a genuine external/system integration requires them.

## ADR-003 — Logical domain boundaries without package explosion

Use logical domains, not independently deployed services:
- Identity
- Conference
- Registration
- Finance
- Submission
- Review
- Program
- Event
- Publication
- Awards
- Certificates
- Content
- Integrations
- Shared

Keep Eloquent simple. Introduce Actions when behavior crosses records or carries business invariants.

## ADR-004 — Relational core, JSON at the edges

Core business relationships are normalized relational tables.

JSON is appropriate for:
- publication snapshots
- audit before/after
- assessment rubric responses
- award candidate evidence
- adapter configuration
- localized CMS content/configuration where querying individual translated fields is not business-critical

Do not hide core participant/submission/payment relationships in JSON.

## ADR-005 — UUIDv7 + human codes

Internal identity uses UUIDv7.
Human codes remain separate, stable, edition-scoped identifiers.

## ADR-006 — Historical truth through snapshots/versioning

Use immutable/versioned records for:
- manuscript files
- official submission evidence
- publication snapshots
- issued certificates
- generated official documents

Current profile edits never rewrite historical scholarly/document truth.

## ADR-007 — Specialized business tables over generic EAV

For high-value core domains, prefer explicit foreign keys and specialized tables over generic polymorphic business records.

Generic polymorphism is acceptable at infrastructure edges such as:
- activity/audit subject
- verification target
- generic external identifier target where safe

No EAV data model.

## ADR-008 — Database queue before Redis

Expected V1 scale does not justify Redis as a mandatory infrastructure dependency.

Database queue/session/cache keep deployment simple. Redis can be added later without changing product behavior.

## ADR-009 — Private-by-default files

Uploads are private unless a business event explicitly publishes them.

Public verification pages expose minimum credential metadata only.

## ADR-010 — PDF through driver abstraction

Official documents are HTML/CSS templates rendered through a configured PDF driver.

The product must not couple certificate/LoA semantics to one renderer.

## ADR-011 — Authorization is not menu visibility

Frontend menu hiding is UX only.

Every protected request/action is authorized server-side using edition scope, role/permission, resource relationship, and restrictions.

## ADR-012 — No separate publication peer-review engine in V1

Presentation assessment + revision/no-revision + Final ACC is the V1 post-presentation academic pathway.

The old generic publication-review engine is not implemented in accelerated V1.

## ADR-013 — Institution-first ROR

Canonical scholarly affiliation uses the institution/organization.

Faculty, department, and study program are optional subdivision metadata.

## ADR-014 — Source-of-truth separation

Application/database state is authoritative.

Email, PDFs, exports, and QR displays are derived representations.

## ADR-015 — Audit, not event sourcing

Store sufficient immutable audit history for consequential actions without implementing event sourcing/CQRS.

## ADR-016 — Publication adapters consume immutable snapshot

OJS, Crossref, and future adapters consume the finalized publication snapshot.

They never read mutable current profile state as publication truth.

## ADR-017 — Published schedule is data

Schedule is stored relationally and published by state transition.

PDF schedule, email, and public page are representations of the same published schedule data.

## ADR-018 — Derived eligibility, explicit issuance

Certificate eligibility can be derived/reconciled from authoritative facts, but generated/issued/revoked certificate records are explicit persistent records.
