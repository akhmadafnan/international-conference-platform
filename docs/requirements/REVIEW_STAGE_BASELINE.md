# Review Stage Architecture Baseline

**ID:** ICP-REQ-ACA-REVIEW-STAGE-001  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-23

## Design intent

Adopt the proven editorial concept of a flexible Review Stage, similar in spirit to OJS:
- editors/academic authorities select reviewers individually;
- multiple reviewers may be assigned;
- review rounds are supported;
- review mode is configurable;
- review forms/tasks may differ by assignment.

The conference platform remains its own source of truth and does not depend on OJS for conference review workflow.

## First-edition default

Abstract review default:

```text
single_anonymous
```

Meaning:
- Reviewer sees Author identity.
- Author does not see Reviewer identity.

This is the default, not a permanent global restriction.

## Core hierarchy

```text
REVIEW STAGE
└── REVIEW ROUND
    └── REVIEW ASSIGNMENT
        ├── Reviewer
        ├── Assigned Version
        ├── Task/Purpose
        ├── Anonymity Mode
        ├── Review Form
        ├── Deadlines
        ├── COI Status
        ├── Recommendation
        ├── Author Comments
        └── Confidential Editor Comments
```

## Supported review modes

Minimum:
- `single_anonymous`;
- `double_anonymous`;
- `committee_screening`.

Additional modes may be introduced later by documented policy.

## Reviewer count

No fixed reviewer count is hardcoded.

Examples:
- one specialist reviewer;
- two independent reviewers;
- three reviewers with different responsibilities;
- additional advisory reviewer.

Edition/review-stage policy can define minimum/target counts.

## Assignment purposes

Examples:
- subject/content;
- methodology;
- statistics;
- language/readability;
- publication readiness;
- ethics/compliance;
- advisory review.

A review assignment may be formal/voting or advisory according to future policy, but the final decision still belongs to the designated Academic Decision Authority/Editor.

## Multiple rounds

```text
Round 1 → revision request
Revised Version → Round 2
Round 2 → final decision / further revision
```

Each round is linked to a stable manuscript/submission version.

## Identity-safety rule

For double-anonymous assignments:
- provide an anonymized file/version;
- suppress author name/affiliation and other identity-bearing metadata;
- preserve reviewer anonymity from Author.

If another assignment for the same submission is single-anonymous, the double-anonymous reviewer must still receive an isolated identity-safe review packet.

## Conflict of interest

Reviewer assignment must support:
- reviewer COI declaration;
- editor/committee COI screening;
- assignment cancellation/reassignment;
- auditable reason/history.

## Decision rule

```text
Review recommendations
→ synthesized by Academic Decision Authority / Editor
→ final decision
```

No automatic majority-vote rule is assumed.

## Publication Review baseline

The post-presentation full-paper Publication Review uses this same architecture.

### V1 default
- default anonymity mode: **double-anonymous**;
- exact reviewer minimum/target: edition/publication-policy configurable;
- formal vs advisory assignment mix: configurable;
- publisher/proceedings/OJS requirements may add stricter constraints.

Double-anonymous remains a default, not a global hardcoded rule. Authorized stage/assignment overrides must follow documented edition policy and preserve identity isolation.

Publication Review outcomes feed an authorized Publication/Academic Decision Authority rather than an automatic majority-vote rule.
