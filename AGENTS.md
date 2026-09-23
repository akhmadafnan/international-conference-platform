# International Conference Platform — Agent Instructions

This repository is documentation-governed. AI agents and developers must not invent product policy.

## Mandatory first read

Before proposing or changing implementation, read in order:

1. `PRD.md`
2. `docs/ai-context/INTERNATIONAL_CONFERENCE_PROJECT_CANONICAL_CONTEXT.md`
3. `docs/ai-context/CURRENT_STATE.md`
4. `docs/governance/DECISION_REGISTER.md`
5. `docs/requirements/REQUIREMENT_REGISTER.md`
6. `docs/governance/GIT_GITHUB_WORKFLOW.md`
7. `docs/project-management/DELIVERY_LIFECYCLE.md`
8. the active ticket/issue
9. every authoritative document named by that ticket

## Source-of-truth order

When sources conflict:

1. Accepted Decision Record / ADR
2. PRD baseline and canonical project docs
3. Accepted Requirement Register
4. Source code + automated tests
5. GitHub issue/ticket
6. Chat conversation
7. AI assumptions

Do not silently resolve contradictions. Surface them.

## Work status lifecycle

```text
BACKLOG
→ ANALYSIS
→ READY
→ IN_PROGRESS
→ REVIEW
→ REGRESSION
→ UAT
→ DONE
```

Additional states: `BLOCKED`, `CANCELLED`, `SUPERSEDED`.

Do not start implementation from BACKLOG or ANALYSIS.

## Definition of Ready

A work item may enter READY only when relevant items are known:

- objective;
- actor/user;
- scope;
- out-of-scope;
- business rules;
- dependencies;
- authoritative docs;
- acceptance criteria;
- test expectations;
- data/integration/security/localization impact;
- no unresolved product decision that materially changes the result.

## Core guardrails

- Do not invent conference policy.
- Do not treat exploratory discussion as accepted architecture.
- Do not work directly on `main`.
- Use a scoped branch from `develop`.
- Preserve unrelated work.
- Update tests with behavior.
- Update canonical docs when accepted behavior changes.
- OJS, ORCID, ROR, Crossref, payment, WhatsApp, email, and other systems cross explicit integration boundaries.
- Mandatory locales are `id`, `en`, and `ar`; Arabic RTL is architectural, not cosmetic.
- Do not assume framework, database, hosting, payment provider, or auth pattern until accepted.
- A previous GREEN result is not evidence for code changed after that run.

## No-repeat-mistake rule

A material mistake/regression must create a durable guardrail, such as:

- automated test;
- regression test;
- validation;
- ADR/decision;
- checklist;
- agent rule;
- UAT scenario;
- CI gate.

"Remember not to do it again" is not an acceptable closeout.

## Implementation handoff

Report:

- ticket ID;
- branch;
- files changed;
- behavior delivered;
- migrations;
- tests/checks run;
- regression result;
- UAT result if required;
- docs updated;
- known limitations;
- commit/PR.
