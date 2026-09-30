# V1 Publication Baseline

**ID:** ICHES-V1-PUB-001  
**Status:** AUTHORITATIVE V1 BASELINE  
**Updated:** 2026-09-30

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

## OJS / Crossref

- OJS is downstream.
- Crossref is a metadata/integration target.
- V1 handoff is manual/assisted.
- No automatic OJS API submission is required.
- No automatic Crossref deposit is required.
- External identifiers never become internal primary keys.
