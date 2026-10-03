# Pre-Phase 02 Product Rebaseline

**Document ID:** ICHES-GOV-PRE02-001  
**Status:** ACCEPTED PRODUCT OWNER REBASELINE  
**Updated:** 2026-10-03  
**Scope:** Product/workflow corrections accepted after mature-platform benchmarking before Phase 02.

## 1. Purpose

This document records the accepted pre-Phase 02 workflow corrections and explicitly supersedes older conflicting V1 statements without rewriting historical provenance.

LeConfe is used only as a mature conference-platform benchmark. ICHES remains an independent product, codebase, architecture, and user experience.

No LeConfe fork, runtime integration, database import, or roadmap replacement is authorized.

## 2. Payment lifecycle correction

The old rule "all participants pay before the academic submission path" is superseded.

### 2.1 Presenter/author path

Canonical V1 path:

Register / establish Edition participation intent
→ Select intended Participation Package
→ Submit Abstract
→ Administrative Screening
→ Single-anonymous Review
→ Academic Decision
→ ACCEPT
→ Payment Obligation
→ Upload Payment Proof
→ Finance Verify
→ Registration Confirmed
→ Event Pass
→ Full Article
→ Confirm Presenter
→ Scheduling
→ Publish Schedule
→ Attendance / Presentation
→ Presentation Assessment
→ Revision / No Revision
→ Final ACC
→ Finalize for Production
→ Publication Handoff
→ Certificates / Awards

Payment obligation for the presenter/author path is created only after an authorized ACCEPT academic decision.

Academic acceptance and finance confirmation remain separate facts.

Presentation LoA is an academic document created from acceptance and is not itself evidence of payment.

### 2.2 Rejected abstract

Academic rejection does not create presenter payment obligation.

The user may still continue as participant-only when the Edition permits it. Participant-only registration then follows the normal participant payment path.

Normal academic rejection therefore does not require an automatic refund flow because no presenter payment should have been collected yet.

### 2.3 Participant-only path

Participant-only users who do not submit an abstract may register, select a Participation Package, pay, receive Finance verification, become confirmed participants, and receive Event Pass entitlement without entering the academic workflow.

## 3. Payment configuration

V1 retains manual bank transfer and manual Finance verification.

Admin must be able to configure per Edition:
- Participation Packages and their prices;
- package-to-activity entitlements;
- payment availability windows;
- one or more payment destinations/bank accounts;
- default destination and optional package-specific destination;
- account holder;
- account number;
- bank label/name;
- participant-facing transfer instructions.

Participant payment UI must display the authoritative payable amount and destination clearly. Account number must support a one-action copy interaction.

When a payment obligation is created, the expected amount, currency, package context, and payment-destination details are snapshotted so later configuration changes do not rewrite historical payment truth.

Money uses fixed-precision DECIMAL, never floating point.

Payment proof remains versioned and auditable.

Invoice is optional for V1. Receipt/payment confirmation is supported as an operational document where enabled.

## 4. Operational configuration learned from mature platforms

ICHES should support bounded, Edition-configurable operational windows rather than requiring developer edits for ordinary conference dates.

At minimum the architecture may represent windows/deadlines for:
- registration;
- abstract submission;
- accepted-author payment;
- full article;
- presenter confirmation;
- schedule publication where relevant;
- post-presentation revision.

Public "Important Dates" remain presentation content; enforcement windows are operational configuration.

## 5. Review/version traceability

ICHES keeps its current canonical approach:
Submission snapshot/version
→ Review assignment
→ Review report
→ Authorized academic decision.

LeConfe-style review-round behavior is a useful benchmark, but a generic multi-round review engine is not added to accelerated V1.

The single normal abstract-revision cycle must remain traceable to the exact snapshot/file reviewed. If implementation evidence later proves the current snapshot model insufficient, a bounded architecture review is required before introducing a review-cycle entity.

## 6. Public CMS refinement

V1 remains a bounded conference CMS, not a general page-builder/plugin platform.

In addition to structured conference data, news, FAQ, and edition documents, ICHES may support:
- localized custom public pages;
- configurable navigation items;
- public announcements/news;
- Edition-scoped publication state and ordering.

This exists so ordinary content such as visa information, accommodation guidance, transportation, conference policy, or other Edition-specific information does not require source-code edits.

No general plugin engine, arbitrary EAV page builder, or WordPress-like extension marketplace is introduced.

## 7. Publication-system boundary

ICHES is not the publishing system of record.

ICHES responsibilities end at publication-ready scholarly metadata and production handoff.

OJS or another authorized downstream publishing system owns:
- copyediting/production;
- final publication;
- DOI assignment/registration where applicable;
- Crossref deposit where applicable;
- final issue/volume publication state;
- scholarly landing page used for publication indexing.

Direct DOI generation and direct Crossref deposit are not V1 ICHES responsibilities.

ICHES may record downstream DOI, OJS identifiers, publication URL, publication date, volume/issue/article number, and similar results as external identifiers after publication.

## 8. OJS-ready metadata remains mandatory

The scholarly metadata contract remains central.

ICHES must preserve and validate enough canonical metadata that publication staff do not need to retype ordinary article metadata in OJS.

The production handoff must be derived from the immutable publication snapshot and may include:
- title/subtitle;
- primary scholarly locale;
- abstract;
- ordered keywords;
- ordered contributors;
- corresponding contributor;
- email where target workflow requires it;
- ORCID and verification state where present;
- affiliations and ROR snapshots where present;
- references;
- track/context;
- final approved manuscript;
- supplementary publication files where applicable;
- publication destination;
- relevant Edition/publication metadata.

OJS export/adapter output is a projection of canonical metadata. It never becomes the canonical model.

## 9. Final approved manuscript and production bundle

The file used for publication handoff is not merely the newest upload.

After presentation assessment and any required revision:
Revised/Final Article
→ academic verification
→ Final ACC
→ Finalize for Production
→ immutable publication snapshot
→ Final Approved Manuscript.

Earlier manuscript versions remain historical and are never silently overwritten.

ICHES must support:
- single-paper production bundle;
- bulk production bundle by Edition/publication destination/readiness;
- only Final-ACC, publication-ready records in production bundles;
- deterministic manifest of included papers/files;
- checksum/evidence sufficient to identify the exact approved file.

A production bundle is for copyediting/production and must exclude reviewer-confidential notes and unrelated internal audit material.

An internal editorial/audit export may be provided separately when authorized.

## 10. Publication readiness

The existing READY / WARNING / BLOCKED vocabulary is retained.

Typical BLOCKED conditions include:
- Final ACC missing;
- final approved manuscript missing;
- publication destination missing;
- required canonical contributor metadata invalid;
- exactly-one corresponding contributor invariant not satisfied.

Typical WARNING conditions include:
- ORCID absent;
- ROR unmatched but truthful manual affiliation exists;
- optional structured reference enrichment absent.

Warnings do not automatically prevent an OJS production bundle.

## 11. Public discoverability

ICHES public frontend should provide ordinary conference-web discoverability:
- sitemap;
- robots policy;
- canonical URLs;
- metadata/title/description;
- Open Graph;
- locale-aware URLs and hreflang where implemented;
- stable Past Edition/public content URLs.

This is conference/public-site SEO. ICHES does not claim to be the scholarly publication endpoint for Google Scholar when final publication lives in OJS.

## 12. Frontend boundary

This rebaseline does not choose the final public visual reference.

Frontend visual-reference work remains a later bounded workstream after the product/domain/architecture rebaseline is closed.

## 13. Explicit supersession

This document supersedes conflicting earlier statements including:
- PD-006 old "all participants pay at the beginning" behavior;
- PD-059 old paid-rejected-abstract refund scenario as the normal author path;
- Product Blueprint sections that place payment before abstract submission;
- Canonical Context lifecycle/rules that place payment before academic submission;
- direct Crossref/DOI responsibilities previously implied as V1 ICHES publication behavior.

Non-conflicting earlier decisions remain valid.
