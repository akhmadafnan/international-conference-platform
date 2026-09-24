# International Conference Platform — Canonical Context

**ID:** ICP-CANONICAL-001  
**Version:** 0.3.0  
**Status:** ACTIVE — PHASE 0  
**Updated:** 2026-09-24

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
- official submissions use formal withdrawal and later contributor changes are controlled;
- Finance is edition-scoped and solely authoritative for payment/refund execution;
- raw payment proof/reconciliation/bank data is Finance-restricted by default while other domains consume derived financial status;
- refund eligibility is policy/business-event driven, while Finance executes the eligible refund;
- financial corrections preserve audit/history and operational payment/refund records are not freely hard-deleted;
- overpayment/partial mismatch does not automatically become PAID;
- Finance cannot normally verify/process its own payment/refund case;
- V1 may use one Finance role while remaining ready for future Finance sub-role separation;
- Academic Committee manages review operations while Academic Decision Authority controls final academic decisions;
- Reviewer access is assignment/round/version scoped and subject to COI;
- anonymity is enforced per assignment, including mixed single/double-anonymous cases;
- double-anonymous review blocks identity-bearing author metadata/files and technical identity leakage;
- Reviewer cannot see other reviewers' reports by default;
- reviewer recommendations are advisory, not automatic decisions;
- Decision Authority may differ by review stage and cannot decide own/conflicted submissions;
- submitted reviews are locked and reopen only through controlled audit;
- reviewer assignment history is preserved;
- confidential editor comments must never leak to Authors;
- final academic-decision corrections use controlled supersession;
- Event Operations is edition-scoped while Session Chair/Moderator are session-scoped;
- event roles receive operationally necessary/derived data only;
- attendance/check-in is distinct from presentation status and self-check-in never means PRESENTED;
- Event Operations/Session Chair may verify PRESENTED/NO_SHOW within scope; Moderator verification is configurable;
- presentation verification is auditable and separate from exception/makeup approval;
- published schedule changes and presenter substitutions/corrections are traceable;
- event actors cannot normally verify their own presentation;
- event-domain authority does not imply Academic/Publication/Certificate issuance authority;
- Publication Team/Proceeding Editor is edition-scoped publication-operations authority;
- PUBLICATION_APPROVED, PUBLICATION_ELIGIBLE, and PUBLISHED remain distinct;
- Publication Team cannot bypass academic/publication-review/eligibility gates;
- Publication Team receives publication-required metadata plus derived Finance/eligibility status, not raw Finance evidence by default;
- final publication metadata is snapshotted before/at handoff;
- substantive authorship/manuscript changes after protected stages require controlled authority;
- V1 manual/assisted OJS handoff is handled by Publication Team;
- OJS/DOI/URL/ISBN/ISSN remain external references;
- publication transfer/status changes are auditable and must reflect known downstream fact;
- publication failure/withdrawal preserves conference/presentation/certificate history;
- Publication Team cannot bypass gates on its own paper;
- publication records are not freely hard-deleted;
- V1 may combine Publication Team/Proceeding Editor while remaining ready for future sub-role separation;
- certificate configuration/issue/manual/bulk/revoke/reissue/history are separate capabilities;
- rule-based and manual individual/bulk certificate issuance are supported, including external/manual recipients;
- activity/event date is the public certificate date while created/generated timestamps remain truthful internal audit data;
- every certificate has an individual record/identifier/token/link; public verification and QR expose only minimum credential data;
- issued certificates use revoke/supersede/reissue, not free edit or hard delete;
- manual certificate issuance does not silently mutate event/academic/publication states;
- privileged manual certificate self-issuance is denied by default;
- edition closeout is checklist-driven and can coexist with continuing publication work according to policy;
- ARCHIVED is read-only by default;
- controlled certificate/publication-reference/historical correction may continue post-archive without reopening the whole edition;
- historical.correct is a dedicated audited capability;
- unarchive is exceptional and privileged;
- archive preserves lifecycle records rather than deleting them;
- role/capability assignments are scoped auditable records and are distinct from resource assignments;
- temporary/expiring authority is supported;
- revocation removes future access without erasing historical attribution;
- protected authorities cannot be self-assigned;
- V1 does not require dual approval for every assignment, but uses authorized assigner + anti-self-escalation + audit and remains future-ready;
- COI is a generic restriction layer and direct self-conflicts are blocked across Reviewer/Academic/Finance/Event/Certificate contexts;
- overrides are domain-specific, never universal, and always preserve before/after state, reason, actor, and history;
- break-glass is separate from business override, temporary/scoped/reasoned/audited, and never permanent;
- silent impersonation/account takeover is denied; V1 does not require impersonation;
- delegation revocation and role-holder replacement preserve historical attribution;
- sensitive-role revocation stops new authorization immediately at product-policy level;
- assignment/revocation/expiry/scope-change/reassignment are first-class audit events;
- Front Office is edition-scoped support with safe operational visibility but no default Finance/Academic/Event/Publication authority or raw confidential-data access;
- administrative screening uses a distinct submission.admin_screen capability and remains separate from academic judgment;
- official-submission withdrawal separates author request from authorized edition approval;
- post-submission contributor/authorship changes require stage-aware protected approval;
- normal Publication Eligibility is policy/system evaluated from authoritative source facts;
- Publication Team may remediate its own-domain blockers but cannot rewrite Finance/Academic/Event facts;
- publication.eligibility.override is protected/domain-specific and preserves the original blocking facts;
- REQ-PERM-001 Parts 1–10 passed full consistency audit and are complete at product-requirement level;
- NFR Security Part 1 requires HTTPS/secure transport and server-side authorization on every protected request;
- MFA is mandatory for high-risk internal authorities; Participant MFA is not mandatory for V1 and Reviewer MFA is configurable;
- sensitive security/authority actions may use re-authentication/step-up verification;
- revoked authority must fail at the next protected authorization check even if a prior session remains open;
- authentication/verification/recovery endpoints require abuse protection and should avoid unnecessary account enumeration;
- magic-link/OTP tokens are unpredictable, short-lived, single-use, action/account-bound, and excluded from plaintext logs/analytics;
- uploaded files are untrusted input; secrets stay out of repo/client bundles/plaintext logs; production errors do not expose internals;
- security-sensitive events are auditable;
- silent impersonation/account takeover remains prohibited and V1 does not require impersonation;
- specific MFA/auth/session libraries/providers remain deferred to architecture/implementation;
- privacy treatment is purpose/sensitivity based; collect only lifecycle-required data;
- current-profile edits do not rewrite historical records;
- unpublished manuscripts, payment/refund evidence, reviewer data, and audit/security data are private by default;
- reviewer anonymity/confidentiality applies across UI/files/API/export/email/notifications/metadata/logs;
- Finance-restricted evidence does not leak into unrelated domains;
- notifications and integrations use minimum necessary data and prefer authenticated workspace for sensitive details;
- public URLs/logs must not expose unnecessary PII, secrets, tokens, or raw restricted content;
- export/API authorization mirrors UI authorization and sensitive bulk export may be audited;
- retention is defined by data class/purpose; exact periods remain deferred pending policy/legal basis;
- account closure does not automatically erase historical scholarly/financial/certificate/publication/audit facts;
- synthetic/redacted data is preferred for dev/test/demo/staging/AI prompts, and backups inherit source-data privacy restrictions;
- production availability objective is at least 99.5% monthly outside announced maintenance, with planned maintenance avoided during critical deadlines/event windows;
- external-service failure must not cause authoritative core-data loss;
- critical state changes are consistency-safe and sensitive operations are duplicate-safe/idempotent;
- backup/recovery covers database plus required private files/generated artifacts and recovery-critical state;
- normal RPO ≤4h, critical-window target RPO ≤1h where infrastructure reasonably supports it;
- normal RTO ≤4h, critical-window target RTO ≤2h;
- production backup is automated, monitored, failure-alerted, and restore-tested before first launch, before each major edition, and periodically (quarterly target while active);
- backups are failure-domain separated and inherit source-data security/privacy restrictions;
- database↔file/artifact integrity must be verifiable;
- integration/notification retries must be safe and delivery failure must not rewrite authoritative business truth;
- graceful degradation and controlled maintenance are required;
- conference-day continuity pack plus controlled/auditable post-outage reconciliation are required;
- data integrity/correctness takes priority over accepting unsafe transactions during degraded conditions;
- common interactive requests target ≤2s with a normal upper expectation ≤3s;
- V1 capacity baseline supports at least 50 concurrent active users on common workflows without severe degradation;
- deadline bursts must remain duplicate-safe and state-integrity safe;
- large operational lists are bounded/paginated and current operational queries are edition-scoped;
- search/filter is server-side and index-ready; dedicated search infrastructure is not mandatory for V1;
- heavy operations are asynchronous-capable and bulk certificate/export/notification flows are batch/job-ready;
- uploads expose progress/failure/retry, use configurable type-specific limits, and avoid unbounded memory usage;
- cache may optimize reads but is never the authoritative business source of truth;
- microservices are not required for V1; modular-monolith/single-application architecture is valid if NFRs are met;
- scaling is incremental/evidence-driven rather than premature distributed-system complexity;
- slow requests/jobs/database operations and integration latency/failure must be observable;
- critical workflows receive performance sanity/regression verification before production and major releases;
- slow external delivery should not unnecessarily block authoritative business transactions;
- business audit and technical application logs are separate concerns;
- business audit is append-only/immutable in normal workflow and corrections create new events rather than rewriting history;
- sensitive lifecycle/security/permission/override/archive/certificate actions and exceptional restricted-data access are auditable;
- technical logs are structured, correlation-ID capable, severity-aware, and redact secrets/PII/confidential payloads;
- application/database/storage/critical-job health plus latency/error/job/queue/database/integration signals are observable;
- backup freshness/failure is monitored;
- alerts are actionable/severity-based and may become more sensitive during critical edition windows;
- authorized audit views are searchable and sensitive audit exports are restricted/auditable;
- business-audit retention is separated from technical-log retention; exact periods remain deferred;
- technical logs use bounded retention/rotation;
- internal timestamps are consistent/unambiguous while display timezone may convert to edition/user context;
- user-facing error references correlate to internal diagnostics without exposing stack traces;
- production deployments are traceable and incident timelines are reconstructable through timestamps/correlation context;
- monitoring is not the business source of truth and must not become a core single point of failure;
- audit/observability behavior must be verified before production;
- authoritative academic/payment/publication file replacements create new versions/evidence rather than silent overwrite;
- file versions already used for review/decision/publication or other authoritative evidence are immutable in normal workflow;
- internal file identity/storage keys are opaque and separate from original filenames, with sanitized application-controlled paths;
- file validation does not trust extension alone and upload architecture remains scanner/quarantine-ready;
- protected files require authorized access and double-anonymous reviewer packets are isolated from identity-bearing originals;
- important stored files support integrity fingerprint/checksum verification;
- database↔storage partial-failure states must be detectable/reconcilable and orphan cleanup is reference-safe;
- temporary artifacts are distinguished from authoritative artifacts;
- certificates/LoA/decision letters and other important generated documents preserve provenance plus template/version/snapshot context;
- changing the current template does not silently rewrite historical generated documents;
- important file metadata preserves identity/type/size/storage/uploader/time/resource/version/integrity/validation context as applicable;
- file retention follows resource purpose/history/privacy policy and authoritative evidence files are not freely hard-deleted;
- storage failures fail safely and are observable;
- backup/restore supports post-recovery file-integrity verification;
- exceptional restricted-file access can be audited;
- export/publication packages reference authoritative approved file versions.

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
