# International Conference Platform

International academic conference lifecycle management platform — governance, architecture, and application.

## Current state

**Phase 0 — Project Definition & Governance**

Application implementation has **not started**. Technical stack, ERD, migrations, authentication model, and production architecture are not yet locked.

## Read first

1. `AGENTS.md`
2. `PRD.md`
3. `docs/ai-context/INTERNATIONAL_CONFERENCE_PROJECT_CANONICAL_CONTEXT.md`
4. `docs/ai-context/CURRENT_STATE.md`
5. `docs/governance/DECISION_REGISTER.md`
6. `docs/requirements/REQUIREMENT_REGISTER.md`
7. `docs/project-management/PHASE_0_BACKLOG.md`

## Working principle

```text
AUDIT
→ PLAN
→ READY GATE
→ IMPLEMENT
→ REVIEW
→ TARGETED REGRESSION
→ BROADER/FULL REGRESSION
→ UAT
→ CLOSEOUT
```

Routine work never goes directly to `main`.

- `main`: stable/release baseline
- `develop`: integration baseline
- scoped branches: actual work
