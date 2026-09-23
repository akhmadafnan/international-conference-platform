# International Conference Platform — Canonical Context

**ID:** ICP-CANONICAL-001  
**Version:** 0.3.0  
**Status:** ACTIVE — PHASE 0  
**Updated:** 2026-09-23

## Purpose

Durable cross-session context for this project.

A new AI session must read:
- `AGENTS.md`;
- `PRD.md`;
- this file;
- `CURRENT_STATE.md`;
- decision and requirement registers;
- active phase backlog;
- active issue/ticket.

## Product identity

International Academic Conference Lifecycle Management Platform.

Not just a website, upload form, or OJS front-end.

## Initial reality

- roughly 100 participants/submissions;
- initially primarily Indonesia;
- limited operational resources;
- intended for reuse in recurring editions.

## Locked / accepted directions

- multi-edition architecture;
- mandatory locales `id`, `en`, `ar`;
- Arabic RTL from first UI foundation;
- payment around abstract submission;
- payment does not imply academic acceptance;
- refund workflow supported and configurable;
- abstract selection mode configurable;
- presentation does not imply publication readiness;
- required post-presentation revision blocks publication;
- OJS is downstream publication infrastructure;
- one WhatsApp number as Front Office gateway;
- WhatsApp is not source of truth;
- scholarly model should be ORCID/ROR/OJS/Crossref/DOI-ready;
- ORCID is optional; lack of ORCID does not block participation/authorship/review/presentation;
- if ORCID is supplied, future verification can distinguish manual vs authenticated/verified state;
- GitHub + docs-as-code is engineering source of truth;
- Project → Phase → Epic → Ticket;
- no implementation before Definition of Ready.

## Important open decision

Authentication/registration is not locked:
- full account;
- secure token/no-login;
- progressive/hybrid.

## Benchmark lessons

### AICIS
Benchmark product/workflow, not infrastructure to copy blindly.

### NgodingPakeAI
Adopt AI-ready repository discipline:
PRD → spec → task, agent instructions, docs, and verification.

### DewaKoding Project Management
Adopt operational model:
project, epic, ticket, status, priority, assignee, comment, history, timeline.

## Delivery roles

- Product Owner: human decision authority.
- Architect/Analyst: requirements, architecture, task preparation, audit.
- Implementation Agent: Codex/developer implements READY work.
- GitHub: durable engineering record.
- UAT Authority: human acceptance.

## Repository target

`akhmadafnan/international-conference-platform`

Branch model:
- `main`: stable/release;
- `develop`: integration;
- scoped branches for work.

## Current phase

Phase 0 — Project Definition & Governance.

No application implementation yet.
