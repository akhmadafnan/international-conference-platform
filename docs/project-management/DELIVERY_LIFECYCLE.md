# Delivery Lifecycle — Audit to Final

**ID:** ICP-PM-LIFE-001
**Version:** 2.0
**Status:** ACTIVE / CANONICAL
**Updated:** 2026-10-06

## Purpose

This document defines the canonical lifecycle represented by the GitHub Project board **ICHES — Product & Engineering**.

The board has exactly these operational states:

```text
BACKLOG
→ READY
→ IN PROGRESS
→ IN REVIEW
→ DONE
```

`BLOCKED` is a fail-closed state that may be entered when a concrete blocker prevents safe progression.

Audit, regression, CI, and UAT are gates inside the lifecycle. They are not separate Project-board statuses unless the board is explicitly changed through governance.

## 1. BACKLOG — Identified, Not Ready

Capture:
- objective;
- actor/problem;
- expected outcome;
- known evidence;
- initial dependencies;
- unresolved product or architecture questions.

Do not start implementation from BACKLOG.

## 2. Audit / Discovery — Still BACKLOG

Read authoritative documents and inspect current implementation/tests when present.

Identify:
- dependencies;
- business/domain invariants;
- root cause when corrective work is involved;
- security/authorization impact;
- data/history impact;
- localization/RTL impact;
- regression surface.

Deliverable: findings sufficient to define a bounded task.

## 3. Plan / Definition of Ready — Still BACKLOG

Define:
- scope;
- out-of-scope;
- affected domains/files;
- data/integration/localization/auth impact;
- required tests;
- rollback/compatibility considerations;
- documentation impact;
- target branch;
- acceptance criteria.

## 4. READY — Authorized to Execute

READY requires:
- objective and scope are concrete;
- relevant product decisions are resolved;
- dependencies are satisfied;
- target branch is known;
- required quality gate is known;
- no unresolved blocker prevents execution.

## 5. IN PROGRESS — Active Implementation

Normal execution:
- create bounded child branch/worktree;
- audit exact current source;
- implement smallest complete correct slice;
- run scoped quality checks;
- run focused tests;
- run related regression when required;
- fix and retest failures inside the approved scope.

The default application-development path is workstation/VSCode + GitHub.

## 6. IN REVIEW — Implementation Complete Enough for Gate Validation

Move to IN REVIEW when:
- implementation scope is complete;
- focused tests are GREEN;
- a PR or equivalent review artifact exists;
- the work is ready for CI, technical review, regression, or UAT.

During IN REVIEW, do not silently expand the feature scope.

Review as relevant:
- business rules;
- architecture boundaries;
- naming/data model;
- authorization;
- data isolation;
- multilingual/RTL;
- auditability;
- error states;
- exact diff scope.

## 7. Regression / CI / UAT — Gates Inside IN REVIEW

### Targeted regression

Run focused behavior tests first.

### Broader / full regression

Run when required by the task, phase, or release gate.

Previous GREEN evidence before later code changes is not valid evidence for the changed revision.

### UAT

Human verifies the actual business workflow and UI when the task is user-visible.

### CI

GitHub Actions or equivalent remote gate validates the pushed revision.

A task remains IN REVIEW until all required gates for that scope are GREEN.

## 8. BLOCKED — Concrete Stop Condition

Use BLOCKED only when a concrete condition prevents safe progression.

Record:
- blocker;
- evidence;
- why continuation would be unsafe or impossible;
- what exact condition unblocks the task.

A routine technical failure that can be corrected inside the approved scope should normally be audited, fixed, and retested rather than escalated as a new Product Owner decision.

## 9. DONE — Bounded Scope Closed

DONE requires:
- accepted implementation complete;
- required tests/regression/CI GREEN;
- UAT GREEN when required;
- diff/repository hygiene acceptable;
- material documentation synchronized when needed;
- limitations/known follow-up recorded;
- merge/integration target reached as required by the task.

DONE is evidence-based.

## Phase-Level Integration

Individual bounded tasks may become DONE after merging into the active phase branch.

The phase itself remains IN PROGRESS until its aggregate exit gate is GREEN.

Example:

```text
task child PR
→ phase/02-registration-payment
→ bounded Issue DONE

Phase 02 aggregate PR #61
→ remains Draft / IN PROGRESS
→ Phase 02 exit gate GREEN
→ Product Owner approval
→ develop
```

## Permanent Guardrail

Every material repeated-risk defect must explain:
1. root cause;
2. why prior controls missed it;
3. what durable control prevents recurrence.

Possible controls:
- automated test;
- validation;
- database constraint;
- authorization Policy/Gate;
- regression case;
- UAT case;
- CI rule;
- canonical workflow rule.

## Related Canonical Workflow

Operational roles for VSCode/workstations, GitHub surfaces, VPS, and AGENT-05 are defined in:

`docs/governance/ENGINEERING_WORKFLOW.md`