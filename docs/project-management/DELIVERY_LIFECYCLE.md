# Delivery Lifecycle — Audit to Final

**ID:** ICP-PM-LIFE-001  
**Status:** ACTIVE PROPOSAL

## 1. Intake — BACKLOG
Capture objective, actor, problem, evidence, expected outcome.

## 2. Audit / Discovery — ANALYSIS
Read authoritative docs, inspect current implementation/tests when present, identify dependencies, regressions, and root cause.

Deliverable: audit findings.

## 3. Plan
Define:
- scope/out-of-scope;
- affected domains/files;
- data/integration/localization/auth impact;
- tests;
- rollback/compatibility;
- docs impact.

## 4. Ready Gate — READY
Definition of Ready must pass.

## 5. Implementation — IN_PROGRESS
Scoped branch only. Smallest complete correct slice.

## 6. Technical Audit — REVIEW
Check:
- business rules;
- architecture boundaries;
- naming/data model;
- authorization;
- data isolation;
- multilingual/RTL;
- auditability;
- error states.

## 7. Targeted Regression — REGRESSION
Run focused behavior tests first.

## 8. Broader / Full Regression
Run when required by ticket/phase. Previous GREEN before later code changes is invalid evidence.

## 9. UAT — UAT
Human verifies actual business workflow and UI.

## 10. Closeout — DONE
Docs synchronized, results recorded, limitations known, next state clear.

## Permanent guardrail
Every material mistake must explain root cause, why prior controls missed it, and what permanent control now prevents recurrence.
