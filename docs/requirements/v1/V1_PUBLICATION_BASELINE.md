# V1 Publication Baseline

**ID:** ICHES-V1-PUB-001  
**Status:** AUTHORITATIVE V1 BASELINE  
**Updated:** 2026-10-23

> **ICHES 2027 authority:** Apply the [operational rebaseline](../../governance/ICHES_2027_OPERATIONAL_REBASELINE_2026_10_23.md), including its authoritative candidate journal catalog in section 12. This is approved operational design, not an implementation/handoff completion claim. Publication/DOI/Crossref responsibility remains downstream under the [Pre-Phase 02 rebaseline](../../governance/PRE_PHASE02_PRODUCT_REBASELINE.md).

## Distinct academic and publication facts

Deep Full Paper review occurs after the 5 January 2027 presentation. Reviewer recommendations are `NO_REVISION_REQUIRED`, `MINOR_REVISION_REQUIRED`, `MAJOR_REVISION_REQUIRED`, or `NOT_RECOMMENDED`; they do not automatically finalize academic decisions or select a publication destination.

No revision requires no unnecessary manuscript upload. Minor/major revision creates immutable manuscript versions under the same Submission ID. Academic/Publication Authority completes Final ACC and validates the exact final approved manuscript.

Reviewer may recommend a destination and candidate journal. Revision outcome, final academic approval, publication destination, readiness, handoff and downstream acceptance/publication are separate facts. SC / authorized Academic or Publication Authority controls final routing; Author does not freely choose it. Authorized operators record SC policy without an invented SC application role.

## Entry gate

Publication path begins only after:
- PRESENTED;
- required assessment completed;
- required revision completed if applicable;
- FINAL_ACC;
- final manuscript available.

Then:
→ READY_FOR_PRODUCTION.

## Readiness

Evaluate required scholarly metadata, final contributor order, affiliations, references, final manuscript, destination, and blocking integrity conditions.

Readiness output:
- READY
- WARNING
- BLOCKED

ORCID absence is not a blocker.

## Destination

Default:
- Proceedings.

Authorized override:
- Selected Journal.

Author does not freely choose destination.

ICHES 2027 destination values are `PROCEEDINGS` (default) and `SELECTED_JOURNAL` (authorized override). Final ACC and publication readiness precede downstream handoff. Bundles derive from immutable canonical metadata and the explicitly approved manuscript, exclude confidential reviewer/internal notes, and must prevent duplicate publication or misleading status claims.

## Proceedings

```text
READY_FOR_PRODUCTION
→ metadata validation
→ publication snapshot
→ IN_PRODUCTION
→ PUBLISHED
```

May record final PDF, DOI, pages/article number, publication date, and landing URL.

Proceedings Publication Acceptance may be generated after Final ACC + destination confirmation when operationally needed.

ICHES 2027 proceedings publication target: **28 January 2027**. It is a target, not a guarantee or a selected-journal deadline. Presentation LoA is acceptance for presentation only, not proceedings or journal acceptance.

## Selected Journal

```text
READY_FOR_PRODUCTION
→ SELECTED_FOR_JOURNAL
→ metadata validation
→ publication snapshot
→ HANDOFF
→ external journal process
```

May record target journal, external submission/reference ID, handoff date, and known downstream status.

The conference may issue Selection/Handoff Notice.

The conference must not state Accepted by Journal unless that fact is actually established by the journal or formally delegated authority.

Selected for Journal does not mean Accepted by Journal or Published. Journal editorial review, acceptance, DOI and publication timelines are controlled by the downstream journal/OJS system. No shared 28 January journal-publication requirement is imposed.

The [ten-candidate catalog](../../governance/ICHES_2027_OPERATIONAL_REBASELINE_2026_10_23.md) records Product Owner-supplied titles and SINTA tiers, not independently verified facts. For every candidate, identity, official title, ISSN, accreditation validity, OJS URL, partnership confirmation and slots remain pending verification; do not fabricate identifiers or assert a confirmed acceptance agreement.

## OJS / Crossref

- OJS is downstream.
- OJS/journal owns copyediting/editorial acceptance, final publication, DOI registration and Crossref operations; ICHES records established downstream facts and prepares publication-ready handoff.
- V1 handoff is manual/assisted.
- No automatic OJS API submission is required.
- Direct Crossref deposit or DOI registration is not an accelerated-V1 ICHES responsibility.
- External identifiers never become internal primary keys.
