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
- V1 payment uses manual bank transfer + Author payment-proof upload + Finance verification;
- payment-proof upload is not PAID; only Finance verification after actual receipt cross-check may set PAID;
- only PAID transitions a paper to OFFICIAL_SUBMISSION;
- payment gateway is not required for V1, while the payment domain remains future-provider-ready;
- payment does not imply academic acceptance;
- refund workflow supported and configurable;
- academic rejection in V1 receives 100% refund of conference fee actually paid;
- refund execution is manual by Finance; withdrawal/admin-ineligible refund rules are separate edition policies;
- first-edition abstract review defaults to single-anonymous;
- review architecture is flexible by stage/round/assignment, supporting single-anonymous, double-anonymous, committee screening, variable reviewer counts, assignment-specific tasks/forms, and controlled overrides;
- final academic decision belongs to the authorized editor/Academic Decision Authority, not automatic reviewer majority voting;
- post-presentation full-paper Publication Review is part of the publication path;
- V1 Publication Review defaults to double-anonymous while remaining edition/stage configurable;
- reviewer count/tasks/forms remain policy-driven through the common Review Stage engine;
- session feedback is distinct from formal Publication Review;
- publication decision outcomes are revision required / approved / rejected, followed by an auditable Publication Eligibility Gate;
- publication rejection does not erase presenter history and does not automatically trigger conference-fee refund;
- conference platform controls PUBLICATION_ELIGIBLE before downstream OJS/proceedings handoff;
- abstract selection mode configurable;
- abstract acceptance/LoA means accepted for presentation, not publication;
- Full Paper is required after acceptance and validated before presentation;
- presenter is explicitly designated/confirmed; attendance and presentation statuses are separate;
- NO_SHOW blocks publication by default unless an authorized makeup/waiver exception applies;
- manual certificate issuance is supported through an individual/bulk Manual Certificate Builder with auditable authority/reason;
- certificate display date uses the configured activity/event date, while actual record creation/generation timestamps remain immutable internal audit data and need not appear publicly;
- each generated certificate has its own record and verification identity/link;
- Presenter Certificate requires actual presentation or qualifying authorized exception;
- presentation does not imply publication readiness;
- required post-presentation revision blocks publication;
- OJS is downstream publication infrastructure;
- V1 OJS/proceedings handoff is manual/assisted through a Publication Queue; API integration is deferred;
- PUBLICATION_ELIGIBLE and PUBLISHED are distinct states;
- final publication metadata is snapshotted and external OJS/DOI/URL/ISBN/ISSN identifiers remain external references;
- certificate capability includes individual/bulk manual builder, unique verification link/identity, public verification + QR, and controlled revoke/reissue;
- edition closeout uses a policy-driven checklist and ARCHIVED editions are preserved/primarily read-only with controlled auditable correction;
- end-to-end lifecycle stages 4A–4F are Product Owner approved; REQ-LIFE-001 is complete at PRD level;
- one WhatsApp number as Front Office gateway;
- WhatsApp is not source of truth;
- scholarly model should be ORCID/ROR/OJS/Crossref/DOI-ready;
- ORCID is optional; lack of ORCID does not block participation/authorship/review/presentation;
- if ORCID is supplied, future verification can distinguish manual vs authenticated/verified state;
- GitHub + docs-as-code is engineering source of truth;
- Project → Phase → Epic → Ticket;
- no implementation before Definition of Ready;
- authorization baseline: default deny + least privilege;
- effective permission is scope/resource/domain/restriction aware rather than role-name only;
- visibility does not imply authority;
- Super Admin/Technical Admin do not automatically inherit business-domain decision authority;
- Conference Admin coordinates edition operations without automatically owning Finance/Academic/Event/Publication decisions;
- multi-role is allowed but COI/restrictions override normal allows;
- sensitive-data access follows need-to-know;
- overrides/exceptions require distinct authority and audit;
- server-side authorization is mandatory;
- Super Admin governs the platform globally but does not automatically verify payments, decide papers, verify presentations, or approve publication;
- Technical Admin operates the system/diagnostics with minimum-necessary business-data access;
- Conference Admin is edition-scoped and may configure workflows/policies without automatically executing Finance/Academic/Event/Publication decisions;
- global/protected roles cannot be assigned through ordinary edition administration;
- protected-role self-escalation is denied;
- protected-role assignment/revocation is auditable;
- controlled break-glass access may exist for serious incidents but must be reasoned, temporary, scoped, audited, and must not rewrite business authority/history;
- Participant access is own-account/own-membership scoped unless another resource relationship grants more;
- Corresponding Author is the primary submission manager;
- Co-author/contributor may exist without an account and does not automatically receive submission-edit authority;
- submission collaboration/delegation is submission-scoped;
- Author sees only author-facing review information, not anonymous/confidential/internal review data;
- Presenter cannot self-verify PRESENTED;
- exceptional non-author Presenter receives limited presentation access, not Author permissions;
- Invited Speaker/Keynote is edition-scoped and does not imply administrative/reviewer authority;
- current profile updates do not rewrite historical snapshots;
- official submissions use formal withdrawal and later contributor changes are controlled.

## Authentication / registration

Progressive/Hybrid Account Model is accepted:
- participant workspace uses verified email;
- password is not mandatory at initial participant registration;
- secure email link/code may support normal participant access;
- account is reusable across editions;
- co-author account is optional;
- reviewer requires authenticated account;
- privileged internal roles require stronger-auth/MFA readiness;
- FO cannot perform informal recovery/account takeover;
- ORCID remains separate from login identity.

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
