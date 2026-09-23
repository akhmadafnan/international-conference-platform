# AI / New-Chat Handoff Protocol

**ID:** ICP-GOV-AI-001  
**Status:** ACTIVE PROPOSAL

## Mandatory read order

1. `AGENTS.md`
2. `PRD.md`
3. canonical context
4. current state
5. decision register
6. requirement register
7. current phase backlog
8. active issue/ticket
9. ticket authoritative docs

## Bootstrap prompt for a new chat

> Open the GitHub repository `akhmadafnan/international-conference-platform`. Read `AGENTS.md`, `PRD.md`, the canonical context, current state, decision register, requirement register, current phase backlog, and the active ticket before proposing work. Treat GitHub as the engineering source of truth. Identify current phase, next READY ticket, unresolved blockers, and required audit/plan/test/UAT gates. Do not implement unless Definition of Ready is satisfied. Use a scoped branch from `develop`; never work directly on `main`. Continue our audit → plan → implementation → regression → UAT → closeout workflow.

## Session closeout

Before ending substantial work, update as applicable:
- current state;
- ticket status;
- decisions;
- requirements;
- phase backlog;
- closeout notes;
- blockers;
- next READY ticket.

Chat is disposable. Repository context is durable.
