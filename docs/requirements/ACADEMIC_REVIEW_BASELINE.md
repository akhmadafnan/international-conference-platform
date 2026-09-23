# Academic Review & Decision Baseline

**ID:** ICP-REQ-ACA-001  
**Status:** PARTIALLY APPROVED — REVIEW MODE STILL OPEN  
**Updated:** 2026-09-23

## What is approved

### Abstract-stage process

```text
OFFICIAL_SUBMISSION
→ ADMINISTRATIVE_SCREENING
→ ACADEMIC_PROCESSING
→ ACCEPTED | REVISION_REQUIRED | REJECTED
```

- Administrative screening precedes academic processing.
- Academic decision authority is separate from Finance.
- Abstract revisions are versioned.
- The platform must support edition-configurable review/selection mode.
- **Double-blind review is not mandatory for the abstract stage.**

### Refund consequence

```text
ACADEMIC_REJECTED
→ REFUND_ELIGIBLE
→ Finance manual processing
→ REFUNDED
```

V1 academic rejection refund is 100% of the conference fee actually paid.

## Review-model analysis — intentionally open

The first edition may use one of these models:

1. **Academic Committee Screening**
   - committee members assess abstracts directly;
   - simplest operational model;
   - suitable when abstract is mainly a presentation-selection gate.

2. **Single-anonymous Review**
   - reviewer identity hidden from authors;
   - reviewer can see author identity;
   - lower operational burden than double-anonymous review.

3. **Double-anonymous Review**
   - both author and reviewer identities hidden;
   - stronger bias-control objective;
   - requires anonymized submissions and stricter workflow;
   - not required by the platform baseline.

The exact first-edition choice remains OPEN.

## Candidate two-gate quality model

```text
GATE 1 — PRESENTATION ELIGIBILITY
Abstract
→ administrative/academic selection
→ accepted for conference presentation

GATE 2 — PUBLICATION ELIGIBILITY
Presentation completed
→ full/final paper
→ publication-quality review
→ revision/approval
→ publication eligible
```

This separation allows the conference to keep abstract selection operationally light while applying stronger review before publication.

The publication-quality review may later be:
- committee review;
- single-anonymous;
- double-anonymous;
- another documented model required by the publication partner.

## Must be decided before review implementation

- exact first-edition abstract review mode;
- number of assessors/reviewers per abstract;
- reviewer independence/conflict-of-interest rules;
- whether reviewer honoraria exist;
- review form/scoring criteria;
- who makes the final abstract decision;
- whether full paper receives a separate post-presentation review;
- anonymity mode and reviewer count for that publication review;
- relationship to OJS/proceedings/publisher requirements.

No UI/database implementation should hardcode double-blind assumptions before these items are resolved.
