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


## Approved flexible Review Stage architecture

### Default for the first edition

- Abstract review default: **single-anonymous**.
- Reviewer identity is hidden from Author.
- Author identity is visible to Reviewer.
- This default may be overridden by authorized academic/editorial staff when edition policy permits.

### Review stages

The review engine is stage-based rather than tied to one global review mode.

Candidate stages:

```text
ABSTRACT_SELECTION_REVIEW
PUBLICATION_REVIEW
CUSTOM_REVIEW_STAGE
```

Each stage may define:
- default anonymity mode;
- minimum/target reviewer count;
- allowed reviewer task types;
- review form;
- response deadline;
- review deadline;
- decision authority;
- revision/re-review rules.

### Review rounds

A review stage may contain multiple rounds:

```text
Round 1
→ decision / revision request
→ revised version
→ Round 2
→ ...
```

Each round references a specific submission/manuscript version.

### Review assignments

Each reviewer assignment is independent and may record:
- reviewer;
- assigned submission/manuscript version;
- review stage and round;
- assignment purpose/task;
- anonymity mode;
- review form;
- invitation status;
- response deadline;
- review deadline;
- conflict-of-interest declaration/status;
- recommendation;
- author-facing comments;
- confidential editor comments;
- completion timestamp;
- cancellation/reassignment reason.

Assignment task examples:
- subject/content review;
- methodology review;
- statistical review;
- language/readability review;
- publication-readiness review;
- advisory/non-voting review.

### Reviewer count

The architecture must support variable reviewer counts:
- 1 reviewer;
- 2 reviewers;
- 3 reviewers;
- more when the edition/publication policy requires it.

Reviewer count is policy-driven, not hardcoded.

### Decision authority

Review recommendations are advisory inputs.

```text
Reviewer Assignment(s)
→ Academic Decision Authority / Editor
→ final recorded decision
```

The system must not implement automatic majority-vote acceptance/rejection unless a future explicit policy adopts such a rule.

### Anonymity modes

At minimum:
- `single_anonymous`;
- `double_anonymous`;
- `committee_screening`.

For `double_anonymous`:
- reviewer receives anonymized file/snapshot;
- author identity/affiliation is hidden;
- reviewer identity remains hidden from author.

For `single_anonymous`:
- reviewer may see author identity/affiliation;
- reviewer identity remains hidden from author.

If mixed modes are deliberately used for one submission, the platform must isolate file/metadata visibility per reviewer assignment so a double-anonymous reviewer cannot inherit identity-bearing data from another assignment.

### Publication review

The same engine will later support post-presentation full-paper publication review.

Still OPEN:
- default publication-review anonymity mode;
- minimum reviewer count for publication review;
- whether all formal publication-review assignments are voting or some are advisory;
- publisher/OJS-specific requirements.

This architecture intentionally avoids hardcoding OJS itself as the conference source of truth while borrowing the proven editorial concept of flexible reviewer assignment, review rounds, configurable review mode, and assignment-specific forms/tasks.
