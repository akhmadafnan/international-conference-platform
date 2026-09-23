# Git & GitHub Workflow

**ID:** ICP-GOV-GIT-001  
**Status:** ACTIVE PROPOSAL FOR PHASE 0

## Branch model

```text
main
 ↑ release only
develop
 ↑ integration PRs
 ├─ docs/*
 ├─ feat/*
 ├─ fix/*
 ├─ refactor/*
 ├─ test/*
 └─ spike/*
```

### main
Stable/release baseline. No routine work.

### develop
Integration baseline. Human local workspace normally syncs from this branch.

### scoped branch
One coherent scope. Create from `develop`.

## Assistant → GitHub → VS Code

```text
Read context
→ select READY ticket
→ branch from develop
→ audit
→ plan
→ change
→ test/review
→ PR to develop
→ merge after gate
→ human pulls develop
```

## Local safe sync

```powershell
git status --short
git fetch origin --prune
git switch develop
git pull --ff-only origin develop
git status -sb
```

If working tree is dirty, do not pull blindly.

## Release

```text
develop
→ milestone audit
→ required full regression
→ UAT accepted
→ PR develop → main
```

Never use destructive reset merely to make local code match remote without reviewing local changes.
