# Working Protocol

**ID:** ICP-GOV-WORK-001  
**Status:** DRAFT

## Standard loop

```text
DISCUSS
→ AUDIT / ANALYZE
→ DOCUMENT
→ PLAN
→ READY GATE
→ IMPLEMENT
→ REVIEW
→ TARGETED REGRESSION
→ BROADER/FULL REGRESSION
→ UAT
→ CLOSEOUT
```

## No-repeat-mistake protocol

Every material repeated-risk mistake must be converted into a durable guardrail.

Required questions:
1. What was the root cause?
2. Why did existing controls fail to catch it?
3. What permanent control now prevents recurrence?

Possible control:
test, regression case, validation, ADR, checklist, agent rule, UAT case, CI gate.

## Change control

Accepted decisions are never silently rewritten.

A change:
- creates a new decision/ADR;
- marks the old one SUPERSEDED;
- identifies impacted requirements;
- updates canonical docs;
- assesses migration/regression impact.

## AI rules

AI:
- reads context before work;
- distinguishes proposal from accepted decision;
- does not invent policy;
- stays in ticket scope;
- reports evidence;
- updates docs when canonical behavior changes.
