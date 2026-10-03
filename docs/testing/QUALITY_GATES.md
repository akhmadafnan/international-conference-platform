# Quality Gates

**ID:** ICP-TEST-GATES-001
**Version:** 2.0
**Status:** ACTIVE / CANONICAL
**Updated:** 2026-10-03

## Principle

Testing depth must match change scope and risk.

The project does not run the full suite after every bounded edit.

No gate may be reported GREEN without evidence.

## Gate 0 — Orientation

Required before implementation:
- current branch/HEAD known;
- worktree state known;
- current project gate known;
- authoritative documents identified;
- already-GREEN work identified;
- bounded scope defined.

## Gate A — Audit / Ready

Required:
- no unresolved material product-policy assumption hidden inside implementation;
- affected invariants identified;
- security/data/history implications considered;
- test strategy known;
- bounded scope is READY.

If a genuine product decision is missing, stop and obtain Product Owner direction.

## Gate B — Bounded Implementation

Implementation must:
- remain in scope;
- preserve frozen architecture;
- preserve domain invariants;
- avoid unrelated refactors;
- avoid temporary/runtime/secret artifacts.

## Gate C — Scoped Quality Check

Run relevant changed-scope tooling.

Examples:
- Pint for changed PHP scope;
- frontend formatter/lint for changed frontend scope;
- TypeScript for changed typed frontend scope;
- PHPStan/static analysis for changed backend scope;
- `git diff --check`.

Do not run irrelevant tooling merely for ceremony.

## Gate D — Focused Test

Run the smallest meaningful tests proving the changed behavior.

A successful command is evidence only for the behavior it actually covers.

## Gate E — Related Regression

Run tests covering nearby behavior that could reasonably regress because of the change.

The related-regression boundary should be risk-based, not repository-wide by default.

## Gate F — Browser / UAT

Required when the change materially affects user-visible behavior or an operational workflow.

As relevant, verify:
- happy path;
- failure/error state;
- responsive behavior;
- Light/Dark appearance;
- localization;
- Arabic RTL;
- accessibility-critical interaction;
- actual workflow state transition.

Pure backend/internal/docs changes do not require browser UAT unless they alter a user-visible contract.

## Gate G — Milestone / Full Regression

Full-project regression is required when appropriate for:
- phase or milestone exit;
- release candidate;
- deployment/release gate;
- genuinely high-risk global architecture/security change;
- integration gate when the exact application revision has not already passed the required milestone suite.

A previously completed full regression may serve a later merge/closeout gate when:
- application code has not changed since that result;
- only documentation or other non-behavioral changes followed;
- no new evidence invalidates the result.

Do not rerun the full suite merely because documentation was updated after an already-tested application checkpoint.

## Gate H — Production Build

Run the production frontend build at:
- relevant frontend milestone exits;
- release/deployment gates;
- when build configuration or dependency behavior changes.

A successful development server does not replace a production build gate where one is required.

## Gate I — Data / Migration Integrity

For milestone changes affecting persistence, run the appropriate integrity gate, which may include:
- clean migration from zero;
- seed from zero;
- migration status;
- database invariants;
- temporary test-database cleanup.

Never run destructive migration commands against an unverified database target.

## Gate J — Repository Hygiene

Before checkpoint/PR:
- `git diff --check` GREEN;
- staged scope exact;
- no secrets;
- no temporary/runtime artifacts;
- no unrelated files;
- worktree state understood.

## Gate K — Closeout

At milestone/phase closeout:
- required technical gates GREEN;
- limitations/blockers recorded;
- canonical docs synchronized;
- current state synchronized;
- PR scope reviewed;
- next gate explicitly stated.

## Docs-Only Gate

For documentation/governance-only work with no application behavior change, normally require:
- source-of-truth consistency review;
- path/link/name sanity;
- `git diff --check`;
- exact staged-file audit.

Do not rerun application test/build suites unless the documentation audit exposes a concrete implementation inconsistency.

## RED Gate Rule

A failed required gate is RED.

When RED:
- do not advance;
- fix or reconcile the failure;
- rerun the failed check;
- rerun related regression when the fix could affect neighboring behavior.

Schedule pressure is not permission to cross a RED gate.

## Evidence Rule

Do not state:
- tests passed;
- build passed;
- migration passed;
- UAT passed;
- branch synchronized;

unless current evidence supports the statement.

Expected output is not verified output.

## No-Repeat-Mistake Rule

A recurring-risk defect should add a durable guardrail such as:
- test;
- validation;
- database constraint;
- policy/guard;
- regression case;
- checklist;
- UAT case;
- architecture decision;
- workflow rule;
- CI gate.
