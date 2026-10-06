# Engineering Workflow — Product, GitHub, Workstation, VPS, and AGENT-05

**ID:** ICP-GOV-ENG-001
**Version:** 1.0
**Status:** ACTIVE / CANONICAL
**Updated:** 2026-10-06

## Purpose

This document defines the operational engineering workflow for the ICHES International Conference Platform.

It exists to remove ambiguity about:
- where application development happens;
- what GitHub Code, Issues, Pull Requests, and Projects are each for;
- how the Delivery Board represents execution state;
- how the active workstation and VSCode are used;
- what the VPS is allowed to do;
- what AGENT-05 is allowed to do;
- which actions require Product Owner approval;
- how work continues safely across laptops and new ChatGPT conversations.

The core rule is:

> Application development is workstation-first and GitHub-governed. The VPS is runtime/infrastructure. AGENT-05 is a bounded autonomous worker, not the default development path and not a product decision-maker.

Chat history is temporary context. Repository state, GitHub evidence, tests, accepted decisions, and canonical documentation are durable project truth.

## Source-of-Truth Hierarchy

When sources disagree, use this order:

1. accepted Product Owner decision;
2. current canonical repository documentation;
3. current Git branch/commit and automated test evidence;
4. GitHub Issue / PR / Project state;
5. runtime evidence from controlled VPS validation;
6. chat memory or narrative history.

Never let an old chat transcript override newer canonical repository evidence.

## Operational Components and Their Roles

### 1. VSCode / Active Workstation

The active workstation is the primary place for application development.

Current Laptop 2 application workspace:

```text
D:\PINJAM-AFNAN\Herd\international-conference-platform
```

Current Laptop 2 infrastructure/tooling workspace:

```text
D:\PINJAM-AFNAN\AfnanForge
```

The application workspace is used for:
- Laravel/Vue source editing;
- migration/model/action/service implementation;
- frontend work;
- focused tests and related regression;
- scoped formatter/static-analysis commands;
- Git review, commit, and branch work;
- VSCode inspection and debugging.

The AfnanForge workspace is used for:
- AGENT-05 control-plane source;
- VPS handoff scripts;
- runtime/preflight evidence;
- SSH configuration for infrastructure operations.

Do not mix the two workspaces.

### 2. GitHub Code

The GitHub repository is the durable source of truth for versioned project source.

Use **Code** to inspect:
- branches;
- files;
- commits;
- tags/releases when introduced;
- current repository history.

Code is not the primary planning surface.

### 3. GitHub Issues

An Issue defines a unit of work or a durable product/engineering concern.

An executable Issue should state, as appropriate:
- objective;
- scope;
- out-of-scope;
- acceptance criteria;
- dependencies;
- affected domain;
- quality/UAT expectation;
- product decisions that are already resolved.

An open Issue does not automatically mean the work is being executed.

### 4. GitHub Pull Requests

A Pull Request is the integration and review gate for code or material documentation changes.

A PR answers:
- what changed;
- why it changed;
- which Issue/scope it implements;
- whether the diff is bounded;
- whether required automated checks are GREEN;
- whether it is safe to merge into its target branch.

There are two PR levels during an active implementation phase:

```text
task/feat/fix/docs branch
        ↓
active phase branch
        ↓
phase aggregate PR
        ↓
develop
```

The completed Phase 02 aggregate PR was:

```text
#61
phase/02-registration-payment → develop
```

It was merged after the Phase 02 exit gate was GREEN and the Product Owner explicitly approved integration.

The active Phase 03 aggregate PR targets:

```text
phase/03-submission-metadata → develop
```

It remains Draft/human-gated until the Phase 03 exit gate is GREEN.

### 5. GitHub Project — ICHES — Product & Engineering

The Project is the operational delivery board.

It answers:

> Where is each unit of work in the delivery lifecycle right now?

The Project does not replace Issues or PRs. It coordinates them.

## Delivery Board Status Contract

### BACKLOG

Work is known but is not ready to execute.

Typical reasons:
- scope is incomplete;
- product policy is unresolved;
- dependencies are unfinished;
- acceptance criteria are not yet concrete.

Do not start implementation from BACKLOG.

### READY

The work is sufficiently specified and authorized to start.

READY means:
- objective is clear;
- relevant product decisions are resolved;
- target branch is known;
- scope is bounded;
- required quality gate is known;
- no unresolved blocker prevents execution.

### IN PROGRESS

Implementation is actively being executed.

Normally this means:
- a scoped branch/worktree exists;
- source changes are underway;
- the work is not yet ready for final review.

### IN REVIEW

Implementation is complete enough for review/gate validation.

Normally this means:
- a PR exists or equivalent review evidence exists;
- required focused tests are GREEN;
- review/CI/UAT is underway or pending;
- no further feature expansion is allowed inside the same bounded task.

### BLOCKED

The work cannot safely continue because of a concrete blocker.

A BLOCKED item must record:
- blocker;
- evidence;
- what must become true to unblock it.

A technical failure is not automatically a Product Owner decision. Technical failures should normally be audited, corrected, and retested inside the approved scope.

### DONE

The bounded work and all required gates are complete.

DONE is evidence-based, not narrative.

## Branch Model

Canonical branch hierarchy:

```text
main
  ↑
develop
  ↑
phase/NN-<scope>
  ↑
task / feat / fix / docs / test / agent child branch
```

### main

Stable/release baseline.

No routine development and no autonomous merge.

### develop

Cross-phase integration baseline.

A phase branch may merge into `develop` only after:
- phase exit gate GREEN;
- phase PR review;
- Product Owner approval.

### phase branch

Holds the currently authorized phase.

Current phase:

```text
phase/03-submission-metadata
```

### ordinary bounded child branch

For normal human/assistant development inside an active phase, use a descriptive bounded branch, for example:

```text
task/80-event-pass
feat/80-event-pass
fix/76-cross-payment-proof-isolation
docs/engineering-workflow-v1
```

The child branch starts from the current active phase branch and targets the same phase branch.

### AGENT-05 child branch

AGENT-05 uses:

```text
agent/<issue>-<scope>
```

An AGENT-05 child branch may target only the explicitly authorized active phase branch.

It must never autonomously target or merge into:
- `develop`;
- `main`;
- `master`.

## Standard Product Development Workflow

The default workflow is:

```text
ISSUE / PRODUCT SCOPE
→ READY
→ CREATE CHILD BRANCH FROM ACTIVE PHASE
→ OPEN IN VSCODE
→ IN PROGRESS
→ AUDIT CURRENT IMPLEMENTATION
→ IMPLEMENT BOUNDED CHANGE
→ SCOPED QUALITY CHECK
→ FOCUSED TEST
→ RELATED REGRESSION
→ UAT WHEN USER-VISIBLE
→ EXACT-SCOPE COMMIT
→ PUSH
→ CHILD PR TO ACTIVE PHASE
→ IN REVIEW
→ CI / REVIEW / GATE
→ MERGE CHILD PR
→ DONE
```

Application implementation should not begin from the VPS.

## VSCode Working Protocol

For application development on Laptop 2:

1. Open:
   ```text
   D:\PINJAM-AFNAN\Herd\international-conference-platform
   ```
2. Verify branch, HEAD, and worktree before editing.
3. Fetch/prune and synchronize the active phase branch using non-destructive Git operations.
4. Create a bounded child branch.
5. Work in VSCode against that branch.
6. Run only the scope-appropriate local gates during implementation.
7. Stage exact paths; do not use `git add .` as the default.
8. Commit only coherent, GREEN checkpoints.
9. Push meaningful checkpoints.
10. Open a PR to the active phase branch.
11. Let GitHub CI provide remote evidence.
12. Merge only when the required gate is GREEN.

The Product Owner should not be reduced to repeatedly copying terminal commands. The AI should perform routine engineering actions directly when available tooling supports them.

User action should normally be required only for:
- SSH private-key passphrase;
- sudo/password boundary;
- secret/credential provisioning;
- Product Owner approval gates;
- browser/UAT judgment that requires human observation.

## GitHub Pull Request Rules

### Child PR to active phase

Allowed after:
- exact bounded scope is implemented;
- focused test is GREEN;
- related regression is GREEN when required;
- diff/scope audit is clean;
- no secrets/runtime artifacts are included.

### Phase PR to develop

Human-only.

Current example:

```text
PR #61
phase/02-registration-payment → develop
```

It must remain Draft until all remaining Phase 02 scope and exit evidence are GREEN.

### develop to main

Human-only release gate.

Requires release-level regression/UAT/deployment readiness.

## VPS Role

The VPS is engineering infrastructure, not the primary application editor.

Allowed VPS responsibilities:
- LiteLLM/model gateway runtime;
- OpenCode executor runtime;
- AGENT-05 controller/attempt services;
- Linux-specific systemd/permission/worktree validation;
- controlled preflight;
- future staging runtime;
- future deployment/runtime services;
- queue/scheduler/web runtime when the application reaches those gates.

Do not use the VPS as the normal place to:
- manually edit Laravel/Vue application source;
- bypass Git;
- create untracked production fixes;
- make ad-hoc source changes that are not represented by a reviewed Git revision.

Runtime deployment must come from a known, tested Git revision.

## AGENT-05 Role

AGENT-05 is a bounded autonomous engineering worker.

It is appropriate for work such as:
- one exact regression test;
- a small isolated bug fix;
- a small action/service whose contract is already frozen;
- exact documentation work;
- bounded repetitive implementation;
- tightly scoped refactor with known acceptance criteria.

It is not allowed to invent:
- product policy;
- architecture policy;
- institutional rules;
- new phase scope;
- budget increases;
- credential changes;
- destructive migration/data deletion;
- production deployment approval.

### AGENT-05 execution model

```text
READY GitHub Issue with machine-readable contract
→ AGENT-05 controller on VPS
→ isolated worktree
→ factory_rw / OpenCode bounded implementation
→ scope validator
→ tests / verification
→ factory_ops commit gate
→ bounded branch push
→ child PR
→ required CI
→ guarded child merge to active phase only
→ Project status update
```

### AGENT-05 trust boundary

`factory_ops` owns trusted Git/controller operations.

`factory_rw` receives only bounded filesystem/edit execution.

The AI executor must not receive:
- broad GitHub repository/project credentials;
- autonomous authority over `develop` or `main`;
- production-deployment authority;
- unrestricted repository scope.

Continuous autonomous worker mode remains OFF until explicitly authorized after controlled pilots are GREEN.

Paid inference remains OFF unless explicitly authorized through the budget gate.

## When AGENT-05 Must Not Block Product Development

AGENT-05 is a productivity tool, not a prerequisite for ordinary product development.

If AGENT-05 has a non-critical infrastructure defect:
- record the blocker;
- determine whether the defect affects application correctness;
- if it does not, continue product work through the normal workstation/GitHub workflow;
- fix AGENT-05 in a bounded infrastructure track rather than pausing the entire product indefinitely.

Product progress must remain visible independently from infrastructure progress.

## Reporting Contract

Every substantial checkpoint should report two separate sections.

### PRODUCT PROGRESS

Report:
- active product Issue/scope;
- branch/PR;
- implementation completed;
- focused/regression/UAT evidence;
- remaining product gap;
- exact next product action.

### INFRA / AGENT PROGRESS

Report only when relevant:
- AGENT-05 state;
- VPS/runtime state;
- worker state;
- deployment/preflight state;
- infrastructure blocker;
- whether infrastructure affects current product execution.

Do not present infrastructure activity as product feature progress.

## Human Approval Gates

Explicit Product Owner approval is required for:
- phase branch → `develop`;
- `develop` → `main`;
- production deployment;
- destructive migration or data deletion;
- secret/credential creation or scope change;
- new product policy;
- material architecture-contract change;
- scope escalation;
- budget increase;
- enabling paid autonomous inference;
- enabling continuous autonomous worker mode.

Normal implementation correction, test repair, lint repair, or bounded technical recovery inside already-approved scope does not require repeated approval.

## New-Chat and Multi-Laptop Continuity

A new conversation must not reconstruct the project from chat memory.

It must orient from repository truth.

Mandatory bootstrap:
1. inspect current branch/HEAD/worktree;
2. read `docs/ai-context/CURRENT_STATE.md`;
3. read `docs/governance/WORKING_PROTOCOL.md`;
4. read `docs/governance/ENGINEERING_WORKFLOW.md`;
5. read `docs/ai-context/INTERNATIONAL_CONFERENCE_PROJECT_CANONICAL_CONTEXT.md`;
6. load only the additional authoritative documents required by the current task;
7. inspect GitHub Issue/PR/Project state for the active work;
8. continue only from verified current evidence.

Laptop-specific paths are convenience, not project truth.

Current convention:
- Laptop 1 infrastructure root: `D:\PROJEKKU\AfnanForge`
- Laptop 2 infrastructure root: `D:\PINJAM-AFNAN\AfnanForge`
- Laptop 2 application repo: `D:\PINJAM-AFNAN\Herd\international-conference-platform`

Never copy an uncommitted working tree between machines and assume it is authoritative. Commit/push meaningful checkpoints or explicitly preserve/reconcile local uncommitted work.

## Current Phase 03 Operational Rule

Current active branch:

```text
phase/03-submission-metadata
```

Current umbrella Issue:

```text
#63 — [P03] Submission + Scholarly Metadata
```

The Phase 03 aggregate PR targets `develop` and remains human-only.

Phase 03 child work must remain bounded to canonical submission and scholarly metadata. Administrative screening, review, academic decision, LoA, Full Article, scheduling, event-day operations, and publication-finalization implementation remain later phases.

OpenCode is the implementation executor for bounded Phase 03 work units. The normal contract remains Issue → child branch → OpenCode implementation → gates → child PR to `phase/03-submission-metadata` → review/merge.

## Related Canonical Documents

This document complements, and does not replace:
- `docs/governance/WORKING_PROTOCOL.md`
- `docs/governance/GIT_GITHUB_WORKFLOW.md`
- `docs/governance/AI_HANDOFF_PROTOCOL.md`
- `docs/project-management/DELIVERY_LIFECYCLE.md`
- `docs/testing/QUALITY_GATES.md`
- `docs/ai-context/CURRENT_STATE.md`

If a contradiction is found, stop and reconcile the canonical documents before implementation.

## Core Principle

```text
Product decision
→ GitHub Issue / READY
→ bounded branch
→ VSCode implementation
→ tests
→ commit
→ PR
→ CI/review
→ merge to active phase
→ DONE
```

VPS supports runtime and automation.

AGENT-05 accelerates bounded engineering.

Neither should obscure or replace visible product delivery.