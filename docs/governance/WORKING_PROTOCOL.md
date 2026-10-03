# Development Working Protocol

**ID:** ICP-GOV-WORK-001
**Version:** 2.0
**Status:** ACTIVE / CANONICAL
**Updated:** 2026-10-03

## Purpose

This document defines the canonical development working protocol for the ICHES Conference & Event Experience Platform.

Its purpose is to keep the same working discipline across different ChatGPT conversations, development sessions, and machines without depending on chat history.

Repository evidence is durable project truth.
Chat history is temporary working context.

## Working Roles

The Product Owner owns:
- product intent;
- institutional policy;
- scope acceptance;
- business-rule decisions;
- material UX/product direction;
- phase authorization.

The AI works as:
- Lead Architect;
- Technical Auditor;
- Gatekeeper;
- Implementation Partner.

The AI must:
- orient from repository evidence before proposing work;
- identify contradictions before implementation;
- preserve accepted architecture and domain invariants;
- keep changes bounded;
- stop progression when a required gate is RED;
- distinguish verified evidence from assumptions;
- never claim GREEN without evidence;
- avoid repeating already-GREEN work;
- avoid inventing missing product policy;
- provide concrete implementation, test, Git, and recovery steps.

## Mandatory Session Bootstrap

Every substantial development session starts by reading, in order:

1. `docs/ai-context/CURRENT_STATE.md`
2. `docs/governance/WORKING_PROTOCOL.md`
3. `docs/ai-context/INTERNATIONAL_CONFERENCE_PROJECT_CANONICAL_CONTEXT.md`

Then load only the additional authoritative documents required by the current gate.

Historical or provenance documents never override later accepted canonical decisions.

## Standard Working Loop

```text
ORIENT
→ AUDIT
→ PLAN
→ BOUNDED EXECUTE
→ SCOPED QUALITY CHECK
→ FOCUSED TEST
→ RELATED REGRESSION
→ UAT WHEN RELEVANT
→ FIX IF RED
→ RETEST
→ EXACT-SCOPE CHECKPOINT
→ CONTINUE OR MILESTONE CLOSEOUT
```

## ORIENT

Before changing anything:
- verify current branch and HEAD;
- inspect worktree status;
- identify the current phase and gate;
- identify the latest verified checkpoint;
- identify already-GREEN work;
- confirm the exact bounded scope.

Do not rebuild completed work unless repository evidence or regression proves it is broken.

## AUDIT

Inspect current implementation and authoritative documentation before modifying behavior.

Determine:
- actual current behavior;
- affected architecture;
- business/domain invariants;
- authorization/security implications;
- persistence/history implications;
- localization/RTL implications when relevant;
- affected tests;
- whether Product Owner input is genuinely required.

Do not infer policy merely because an implementation choice is convenient.

## PLAN

For non-trivial work, define:
- bounded objective;
- expected affected files/components;
- invariants that must remain true;
- test strategy;
- exit criteria.

Keep the batch small enough that a failure can be isolated and corrected without destabilizing unrelated work.

## BOUNDED EXECUTE

Implement only the approved bounded scope.

Do not:
- silently add adjacent features;
- opportunistically redesign unrelated code;
- cross into another phase;
- rewrite accepted policy;
- introduce unnecessary abstractions.

Consequential behavior must follow frozen implementation conventions.

## Testing Strategy

Testing depth is proportional to scope and risk.

A normal bounded implementation does **not** automatically run the full project suite.

Normal batch gate:

```text
EDIT
→ scoped formatter/lint
→ scoped static analysis where relevant
→ focused test
→ related regression
→ browser/UAT if user-visible
→ git diff --check
→ exact-scope checkpoint
```

Full regression is reserved for:
- phase or milestone exits;
- release/deployment gates;
- genuinely high-risk global changes;
- integration gates where previous full-suite evidence is no longer valid.

If the exact application revision already passed the required full regression and only documentation changes followed, reuse that evidence instead of rerunning the application suite.

Docs-only work does not require application tests/builds unless the documentation audit exposes a concrete implementation inconsistency.

## RED Gate Rule

If a required gate is RED:
1. stop progression;
2. identify the failure;
3. fix within the bounded scope;
4. rerun the failed gate;
5. run related regression when the fix could affect neighboring behavior;
6. continue only after required evidence is GREEN.

Schedule pressure is not permission to cross a RED gate.

## Evidence Rule

Do not report:
- tests passed;
- build passed;
- migration passed;
- UAT passed;
- branch synchronized;
- gate GREEN;

unless actual evidence supports the statement.

Expected output is not verified output.

## Git Checkpoint Rule

Before a checkpoint:
- inspect the diff;
- run required quality gates;
- stage only intended paths;
- run `git diff --cached --check`;
- inspect `git diff --cached --name-only`;
- use a descriptive commit;
- push meaningful checkpoints when appropriate.

Normal workflow uses exact-scope staging.

Avoid:

```bash
git add .
```

because it can silently include unrelated edits, runtime artifacts, generated files, or secrets.

## Documentation Cadence

Documentation must preserve project truth without becoming an implementation bottleneck.

Do not update `CURRENT_STATE.md` after every small edit or commit.

Update canonical documentation when:
- an accepted product decision changes;
- an architecture contract changes;
- a material gate changes;
- a major blocker changes;
- a phase/milestone closes;
- a substantial session requires durable handoff.

Routine bounded implementation evidence belongs primarily in:
- automated tests;
- Git commits;
- PRs;
- focused audit output.

Prefer one milestone documentation closeout over repeated micro-closeout commits.

Historical documents should normally be preserved and clearly marked historical/superseded instead of silently rewritten.

## Milestone Closeout

A milestone closeout may include, as relevant:
- clean migration from zero;
- required seed verification;
- full milestone regression;
- full configured static analysis;
- production build;
- browser/UAT;
- repository hygiene;
- canonical documentation reconciliation;
- PR scope audit;
- integration into `develop`.

Do not rerun a full test suite after a docs-only closeout when the unchanged application revision already passed the required milestone gate.

## Decision Control

Accepted decisions are never silently changed.

A material change must:
- identify the previous rule;
- record the newly accepted rule;
- state whether the old rule is `SUPERSEDED`, `REFINED`, or `PRESERVED`;
- identify affected requirements/architecture;
- assess migration/regression impact;
- update canonical documentation.

If a genuine product/policy decision is unresolved, the AI must surface it instead of inventing it.

## Phase Control

Completion of one phase does not automatically authorize the next.

A new implementation phase requires:
- previous required gate CLOSED_GREEN;
- integration baseline synchronized;
- required governance/audit work complete;
- explicit Product Owner GO.

Do not create the next implementation-phase branch before that authorization.

## No-Repeat-Mistake Rule

For a material repeated-risk defect, determine:

1. What was the root cause?
2. Why did existing controls fail?
3. What durable guardrail prevents recurrence?

Possible guardrails:
- automated test;
- validation;
- database constraint;
- Policy/Gate;
- regression case;
- UAT case;
- checklist;
- architecture decision;
- workflow rule;
- CI gate.

## Communication Protocol

Keep the Product Owner informed at meaningful transitions.

Report:
- current gate;
- verified result;
- blocker or inconsistency;
- exact next bounded action.

Do not narrate every low-level command when it does not affect a decision or gate.

## Repository Safety

Prefer non-destructive Git operations.

Do not use destructive reset, forced history rewriting, or bulk deletion merely to make repository state look clean.

Protect:
- uncommitted work;
- accepted decisions;
- historical evidence;
- credentials/secrets;
- immutable business history.

When state differs, inspect first and reconcile deliberately.

## Session Continuation

When a conversation ends, the next conversation must re-orient from repository state rather than reconstructing the project from memory.

The concrete new-chat procedure and reusable bootstrap prompt are defined in:

`docs/governance/AI_HANDOFF_PROTOCOL.md`

Chat is disposable.
Repository truth is durable.
