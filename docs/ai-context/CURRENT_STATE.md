# Current Project State

**State ID:** ICP-STATE-20260923-03  
**Status:** PHASE 0 IN PROGRESS  
**Implementation authorization:** NOT GRANTED

## Repository

`akhmadafnan/international-conference-platform`

Branch model:
- `main`: stable/release baseline
- `develop`: integration baseline
- scoped branches: all work

## GitHub bootstrap status

- governance bootstrap PR #1: **MERGED to develop**
- merge commit: `2b22bc68ca7271b18318b5d74a53adc43195f33e`
- PRD v0.1.0-draft: **AVAILABLE on develop**
- Phase 0 issues: created (#2–#8)

## Established

- repository is public and sanitized;
- PRD is a core Phase 0 artifact;
- AGENTS.md governs AI/developer behavior;
- canonical context and decision/requirement registers exist;
- audit → plan → ready → implement → regression → UAT → closeout workflow exists;
- no-repeat-mistake guardrail rule exists;
- mandatory languages: id, en, ar; Arabic RTL;
- payment/refund/publication-gate/OJS/FO directions documented.

## Application implementation

- framework: NOT LOCKED
- database: NOT LOCKED
- ERD: NOT STARTED
- migrations: NOT STARTED
- authentication: NOT STARTED
- application UI: NOT STARTED

## Active Phase 0 work

The next product-analysis sequence is:

1. **PRD-002 / Part 1** — Product identity, initial scale, recurring editions, V1-vs-future: **APPROVED**.
2. **PRD-002 / Part 2** — Actor/user model and multi-role relationships: **APPROVED**.
3. **REQ-ACTOR-001** — Actor catalog baseline: **APPROVED; detailed permission matrix remains pending**.
4. **PRD-002 / Part 3 / REQ-AUTH-001** — Resolve authentication/registration model: **NEXT**.
5. **REQ-LIFE-001** — Validate end-to-end lifecycle.
6. **INT-BASE-001** — Integration baseline.
7. **GOV-GATE-001** — Phase 0 consistency audit and Phase 1 readiness.

## Human local workspace

Human developer should work from `develop`, not `main`.

For a fresh local clone:

```powershell
git clone https://github.com/akhmadafnan/international-conference-platform.git
cd international-conference-platform
git fetch origin --prune
git switch --track origin/develop
git status -sb
```

Expected branch after setup: `develop`.

## Next conversational action

Do **not** ask the Product Owner to manually inspect GitHub files without guidance.

The architect should conduct the PRD/governance review interactively in chat, one decision group at a time, while recording accepted outcomes back to GitHub.
