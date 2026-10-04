# Git & GitHub Workflow

**ID:** ICP-GOV-GIT-001
**Version:** 2.0
**Status:** ACTIVE / CANONICAL
**Updated:** 2026-10-03

## Branch Model

```text
main
 ↑ release only
develop
 ↑ integration PRs
 ├─ phase/*
 ├─ feat/*
 ├─ fix/*
 ├─ docs/*
 ├─ refactor/*
 ├─ test/*
 └─ spike/*
```

### main

Stable/release baseline.

No routine development directly on `main`.

### develop

Integration baseline.

Completed scoped work is merged here through reviewed PRs.

### scoped branch

One coherent bounded scope. Ordinary work is created from synchronized `develop`; a bounded child issue inside an active phase is created from the current active phase branch.

Examples:
- `phase/01-foundation`
- `docs/meta-audit-001`
- `feat/payment-verification`
- `fix/rtl-dialog-overflow`

A new implementation phase branch must not be created before explicit Product Owner authorization for that phase.

## Nested Bounded Work Inside an Active Phase

When an active phase has its own branch and phase PR, a small implementation/security/quality issue may use a child branch.

```text
develop
↑
phase/NN-<scope>           ← phase PR targets develop
↑
agent/<issue>-<scope>      ← bounded Draft PR targets the active phase branch
```

Rules:
- child branch starts from the current active phase branch, not from stale `develop`;
- the child PR targets the active phase branch, never `develop` directly;
- exact issue scope and scope-appropriate quality evidence are required;
- structural wrapper success alone does not prove application behavior;
- after human acceptance, merge the child PR into the active phase branch and close the bounded issue;
- only the phase PR integrates the completed phase into `develop`;
- no automatic merge to the active phase branch, `develop`, or `main`;
- worker Git capability must be least-privilege and prepared before execution; do not use broad recursive ownership/permission changes.

## Standard Workflow

```text
SYNC / ORIENT
→ CREATE SCOPED BRANCH
→ AUDIT
→ PLAN
→ BOUNDED CHANGE
→ REQUIRED QUALITY GATES
→ EXACT-SCOPE CHECKPOINT
→ PUSH
→ CONTINUE BOUNDED WORK
→ MILESTONE CLOSEOUT
→ PR TO develop
→ PR SCOPE AUDIT
→ MERGE
→ SYNCHRONIZE LOCAL develop
```

## Safe Local Synchronization

Before synchronizing:

```bash
git status -sb
git fetch origin --prune
```

If the worktree contains unexpected changes, inspect them before continuing.

Normal synchronization:

```bash
git switch develop
git pull --ff-only origin develop
git status -sb
```

Do not use destructive reset merely to force local state to match remote.

## Creating a Scoped Branch

Start from synchronized `develop`:

```bash
git switch develop
git pull --ff-only origin develop
git switch -c <scoped-branch>
```

Verify:

```bash
git status -sb
git log -3 --oneline --decorate
```

## Exact-Scope Staging

Normal workflow uses explicit paths:

```bash
git add path/to/file-a path/to/file-b
git diff --cached --check
git diff --cached --name-only
```

Avoid `git add .` as the default because it can silently stage:
- unrelated edits;
- generated files;
- runtime artifacts;
- temporary files;
- secrets.

Before commit, the staged file list must match the intended bounded scope.

## Checkpoint Commits

A scoped branch may contain multiple bounded GREEN checkpoints.

Checkpoint rules:
- required gate for that batch is GREEN;
- commit represents one coherent intent;
- message is descriptive;
- no unrelated files;
- push after meaningful checkpoints when preserving remote recovery state is useful.

Do not create artificial micro-commits solely to update state text after every command.

## Documentation Commits

Documentation should normally be updated at:
- canonical decision changes;
- material gate changes;
- milestone/phase closeout;
- durable handoff points.

Avoid repetitive documentation-only commits for transient implementation state.

Corrective documentation commits are appropriate when a real gate defect is discovered after commit; do not hide such correction through unsafe history rewriting.

## Pull Request Gate

Before PR to `develop`:
- branch is synchronized appropriately;
- bounded/milestone gates are GREEN;
- PR diff scope is audited;
- secrets/runtime artifacts are absent;
- documentation required by the milestone is reconciled;
- application test evidence remains valid for the PR head.

A docs-only change after an already-GREEN application milestone does not invalidate application test evidence by itself.

## Merge

Default integration is a normal reviewed merge into `develop` unless a different repository policy is explicitly adopted.

Use an expected head SHA or equivalent protection when automation supports it so a PR is not merged after an unreviewed head change.

After merge:

```bash
git switch develop
git pull --ff-only origin develop
git status -sb
```

Confirm the expected merge commit/checkpoint is present.

## Release

```text
develop
→ milestone/release audit
→ required full regression
→ required production build
→ required UAT
→ release documentation
→ PR develop → main
```

`main` remains a stable/release baseline.

## Prohibited Convenience Operations

Do not use these merely to make problems disappear:
- destructive reset of unknown local changes;
- forced history rewrite without explicit reason/review;
- deleting files before identifying their origin;
- broad staging without scope review;
- committing `.env`, credentials, keys, secrets, runtime caches, or generated build artifacts that are not intentionally versioned.

## Recovery Principle

When local and remote state differ:
- inspect;
- identify which work is authoritative;
- preserve potentially valuable local changes;
- choose the least-destructive reconciliation method.

Repository cleanliness must come from understanding state, not erasing evidence.
