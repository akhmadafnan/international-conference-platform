# Publication Review & Eligibility Baseline

**ID:** ICP-REQ-PUB-001  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-23

## Two distinct gates

```text
ABSTRACT ACCEPTED
= accepted for presentation

PUBLICATION_APPROVED
= manuscript approved through Publication Review

PUBLICATION_ELIGIBLE
= publication-approved manuscript also passes all conference publication gates
```

These states must never be collapsed.

## Entry

Default entry requires verified `PRESENTED` status or an authorized qualifying presentation exception/makeup.

```text
PRESENTED
→ post-presentation assessment
→ FINAL_MANUSCRIPT_PENDING
→ revised/final manuscript
→ PUBLICATION_REVIEW
```

## Presentation feedback vs peer review

Session Chair/Moderator/presentation feedback may inform the Author's revision.

It is not automatically a formal reviewer report.

```text
PRESENTATION_FEEDBACK
≠
PUBLICATION_REVIEW
```

## Publication Review

Uses the common Review Stage architecture.

### V1 default
- anonymity mode: **double-anonymous**;
- reviewer count: edition/publication-policy configurable;
- assignments may use different tasks/forms;
- multiple rounds allowed;
- reviewer recommendations are advisory to the final decision authority.

Double-anonymous review requires:
- anonymized manuscript packet/version;
- hidden author identity/affiliation from the reviewer;
- hidden reviewer identity from the Author;
- identity-safe metadata/file isolation per assignment.

## Decisions

```text
PUBLICATION_REVIEW
├── REVISION_REQUIRED
├── PUBLICATION_APPROVED
└── PUBLICATION_REJECTED
```

Decision is recorded by the authorized Publication/Academic Decision Authority, not by automatic vote counting.

## Revisions

Each substantive revision creates a new version.

```text
Version N
→ Review Round
→ REVISION_REQUIRED
→ Version N+1
→ next Round / editorial re-check
```

## Publication Eligibility Gate

`PUBLICATION_APPROVED` is necessary but not sufficient.

Candidate gate checks:
- payment satisfied;
- abstract acceptance;
- required Full Paper/final manuscript present;
- verified presentation or qualifying exception;
- attendance where edition policy requires it;
- all required revisions approved;
- Publication Review approved;
- final author/contributor metadata complete;
- affiliation and optional ORCID metadata valid where provided;
- publication consent/declarations complete;
- required files/assets complete;
- publisher/proceedings-specific requirements complete.

Results:

```text
PASS
→ PUBLICATION_ELIGIBLE

BLOCKED
→ explicit blocking reasons
→ remediation where allowed
```

Gate decisions, overrides, and blocking reasons are auditable.

## No-show

Default:
```text
NO_SHOW → PUBLICATION_BLOCKED
```

Only an authorized qualifying presentation exception/makeup may satisfy the presentation requirement.

## Publication rejection

`PUBLICATION_REJECTED`:
- does not erase conference participation history;
- does not erase verified PRESENTED status;
- does not invalidate a legitimately earned presenter certificate;
- does not automatically trigger refund of conference fee.

The V1 100% refund rule applies to pre-conference academic rejection of the abstract, not publication rejection after conference participation has occurred.

## OJS boundary

```text
Conference Platform
→ PUBLICATION_ELIGIBLE
→ Publication Queue
→ downstream OJS / proceedings workflow
```

OJS does not decide conference payment, attendance, presentation, refund, or conference eligibility state.


## Eligibility Gate Authority — Final Permission Closure

Normal Publication Eligibility is not a discretionary Publication Team decision.

```text
AUTHORITATIVE PAYMENT / ACADEMIC / EVENT / PUBLICATION-METADATA FACTS
+ EDITION POLICY
→ GATE RESULT
```

Publication Team may remediate publication-domain blockers such as incomplete metadata, missing files, or declarations.

Publication Team cannot directly rewrite source-domain facts such as:
- payment verification;
- academic approval/rejection;
- PRESENTED/NO_SHOW.

A protected `publication.eligibility.override` capability may handle an approved exception. It must preserve the original blocking fact and record:
- blocking condition;
- authority;
- reason;
- before/after gate result;
- timestamp;
- evidence/reference where applicable.

This override must not silently convert source facts merely to make the gate pass.
