# Non-Functional Requirements — Part 12 Maintainability, Testability & Operational Support

**ID:** ICP-REQ-NFR-001-P12  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Objective

Ensure the platform remains understandable, safely changeable, testable, upgradeable, and operable across future editions, developer turnover, and AI-agent handoffs.

## Modular architecture

Maintain clear domain/module boundaries without requiring microservices.

Candidate domains include:
- Identity / Account;
- Conference / Edition;
- Registration;
- Submission;
- Finance;
- Academic Review;
- Event / Presentation;
- Publication;
- Certificate;
- Notification / Integration;
- Audit / Operations.

```text
MODULARITY
≠
DISTRIBUTED SYSTEM
```

A modular monolith is valid if boundaries remain explicit and testable.

## Authoritative business rules

Critical business rules must not be duplicated independently across UI, controllers, background jobs, exports, and integrations.

Concept:

```text
ONE BUSINESS RULE
→ ONE AUTHORITATIVE IMPLEMENTATION CONCEPT
→ MULTIPLE CALLERS
```

Examples:
- PAID eligibility for official submission;
- publication eligibility;
- refund eligibility;
- certificate eligibility;
- NO_SHOW publication blocking;
- state-transition rules.

Exact code pattern is deferred to architecture.

## Authorization maintainability

Authorization uses consistent centralized/policy-driven patterns.

Do not implement broad scattered shortcuts such as:
```text
if role == "admin" → allow everything
```

Permission/COI/resource-state behavior must be testable.

## Canonical vocabulary

Use consistent state/domain terminology across:
- implementation;
- tests;
- requirements/docs;
- logs/audit;
- integration/internal interfaces.

Avoid multiple names for the same authoritative concept without explicit mapping.

## Architecture decisions

Material architecture choices require durable rationale through ADR/Decision Records.

Examples:
- database;
- runtime/framework;
- identifier strategy;
- modular architecture;
- queue/background-job model;
- storage;
- authentication;
- caching.

Developers should not have to reverse-engineer why a foundational choice was made.

## Dependencies

Dependencies are added intentionally, considering:
- maintenance;
- security;
- compatibility;
- community/support lifecycle;
- replacement cost;
- operational impact.

Dependency resolution should be reproducible through the ecosystem's lock mechanism.

## Dependency/runtime lifecycle

Security/support lifecycle must be monitored for:
- framework;
- runtime;
- database;
- key libraries;
- integration clients.

Plan upgrades before reaching unsupported/EOL states.

Avoid unnecessary risky major upgrades immediately before conference critical windows.

## Automated tests and UAT

```text
AUTOMATED TESTS
≠
UAT

UAT
≠
AUTOMATED TESTS
```

Both provide different evidence.

## Unit / domain tests

Business rules suitable for isolated verification should have focused tests.

Examples:
- refund eligibility;
- publication eligibility;
- certificate eligibility;
- COI restrictions;
- state transitions;
- deadline logic;
- authorization decisions.

Do not create meaningless tests solely to increase a metric.

## Integration / feature tests

Database/resource workflows require tests covering the real relationship/transaction boundary where appropriate.

Examples:
- payment verification → resulting authoritative state;
- reviewer assignment → round/version/COI/anonymity;
- presentation verification → publication gate;
- certificate issue/revoke/reissue history.

## Critical-flow verification

Representative lifecycle paths receive end-to-end/acceptance verification.

Do not force the entire conference lifecycle into one giant brittle browser test if smaller stable critical-flow scenarios provide better evidence.

## Regression protection

High-risk areas require durable regression coverage, especially:
- permission boundaries;
- COI;
- Finance/payment/refund;
- academic decisions;
- reviewer anonymity/confidentiality;
- PRESENTED/NO_SHOW;
- publication eligibility;
- certificate issue/revoke/reissue;
- archive/historical correction;
- privacy leakage;
- protected overrides/corrections.

## Bug guardrail

For reproducible functional defects:

```text
REPRODUCE
→ ROOT CAUSE
→ GUARDRAIL/TEST
→ FIX
→ REGRESSION
→ MUST NOT RECUR
```

Automate protection where reasonably possible.

Not every purely visual or non-deterministic issue needs an artificial automated test.

## Flaky tests

Flaky tests are defects.

Do not normalize repeated reruns until green.

Investigate and repair:
- race conditions;
- shared state;
- time dependence;
- external dependency;
- ordering dependence.

## External-service tests

Normal CI must not require live production:
- email;
- WhatsApp;
- OJS;
- ORCID;
- ROR;
- Crossref;
- payment providers.

Use, as appropriate:
- fakes;
- stubs;
- mocks;
- fixtures;
- sandbox;
- contract tests.

Live-provider verification belongs in controlled staging/integration scenarios where needed.

## Time / deterministic test data

Time-dependent workflows use controllable/frozen test time.

Tests must not become wrong merely because the real calendar changed.

Automated test data is:
- repeatable;
- isolated;
- non-production;
- explicitly created for the test/scenario.

## Test isolation

Tests should not rely on another unrelated test having run first.

Explicitly orchestrated end-to-end scenarios may have ordered steps within the scenario.

## Coverage philosophy

Do not lock an arbitrary global 100% line-coverage requirement.

```text
CRITICAL RULE / BOUNDARY COVERAGE
>
VANITY LINE COVERAGE
```

Coverage metrics may be used as evidence, but critical contracts matter more.

## CI quality gate

Required CI checks should be able to block merge/release when mandatory verification fails.

Depending on the future stack, checks may include:
- syntax/static analysis;
- automated tests;
- build;
- lint/format;
- migration sanity;
- dependency/security checks;
- contract checks.

Exact tools are deferred.

## Risk-proportional verification

Continue the canonical workflow:

```text
TARGETED REGRESSION
→ BROADER / FULL REGRESSION
→ UAT WHEN REQUIRED
```

Verification depth is proportional to change risk.

## Documentation synchronization

When implementation materially changes an accepted behavior/contract/architecture decision, canonical docs/ADR/requirements must be updated in the same change or coordinated change set.

Normal implementation of an already-approved contract does not require unnecessary new decisions.

## Inter-module contracts

Modules should consume minimum necessary information from other domains.

Example:
Publication should prefer a derived payment/eligibility signal rather than raw bank evidence.

This reinforces privacy, authorization, and maintainability.

## Repair / correction scripts

High-risk scripts used for:
- historical repair;
- backfill;
- state recalculation;
- metadata migration;
- protected data correction;

should, where appropriate, be:
- version-controlled;
- reviewed;
- testable;
- dry-run capable;
- idempotent or execution-guarded;
- auditable.

Avoid unrecorded one-off production commands for significant mutations.

## Operational support boundaries

Support incidents do not erase separation of duties.

- Front Office: participant support/triage;
- Technical Admin: technical diagnosis/operations;
- Finance: Finance truth;
- Academic authority: academic truth;
- Event authority: presentation truth;
- Publication authority: publication operations.

Technical access does not automatically confer domain decision authority.

## Incident severity

Use a lightweight severity concept appropriate to impact.

Illustrative model:
- SEV-1: broad outage/data-integrity/security risk, especially during critical window;
- SEV-2: major domain degradation with workaround;
- SEV-3: limited/non-critical defect.

Exact terminology/thresholds may be finalized in operations.

## Incident record / post-incident learning

Significant incident records should capture:
- what happened;
- time/timeline;
- impact/affected domain;
- mitigation;
- recovery;
- root cause;
- follow-up corrective action.

Where applicable:

```text
INCIDENT
→ ROOT CAUSE
→ CORRECTIVE ACTION
→ TEST / RUNBOOK / MONITORING IMPROVEMENT
```

## Routine maintenance

Operational planning must include suitable cadence for:
- dependency/security review;
- framework/runtime support lifecycle;
- backup freshness/restore verification;
- secret/certificate expiry;
- failed-job backlog;
- storage integrity/capacity;
- technical-log/retention health;
- browser/device matrix refresh;
- translation completeness;
- integration health.

Exact cadence is deferred.

## Operational ownership

Critical operational areas have clear owner/capability instead of routing all problems to Super Admin.

This includes business-domain and technical ownership.

## Minimum-necessary diagnostics

Technical diagnostics expose only what Technical Admin/support needs.

Technical troubleshooting does not automatically reveal:
- full confidential manuscript;
- raw Finance evidence;
- reviewer-confidential content;
- unrelated PII.

## Repository as durable engineering memory

```text
CHAT
≠
ONLY PROJECT MEMORY
```

Durable engineering source of truth remains:
- accepted ADR/Decision Records;
- canonical requirements;
- current-state/canonical-context docs;
- issue/PR history;
- implementation/tests.

This supports developer and AI-agent continuity.

## Definition of Done direction

A feature is not DONE merely because the UI renders.

Depending on scope, DoD includes:
- implementation complete;
- authorization correct;
- automated tests green;
- targeted regression green;
- broader/full regression where required;
- docs synchronized when contract changed;
- accessibility/localization/RTL checks as relevant;
- UAT passed where required;
- no known blocker;
- temporary debug/test harness removed.

Exact implementation-phase checklist is defined later.

## Technical debt

Deliberate shortcuts/workarounds should be visible and traceable with:
- what/where;
- why accepted;
- risk/impact;
- follow-up plan.

Do not disguise temporary debt as intended permanent architecture.

## Historical compatibility

Evolution of templates/forms/states must preserve historical readability.

Examples:
- Certificate Template v1 remains interpretable after v2;
- old review forms remain readable after new form version;
- historical state snapshots remain meaningful after workflow evolution.

Do not silently reinterpret old records using only current definitions.

## Deferred implementation details

Phase 0 does not lock:
- test framework;
- static-analysis tool;
- CI provider;
- coverage threshold;
- ADR tooling;
- dependency update bot;
- incident platform;
- exact severity nomenclature;
- maintenance cadence.

## Next

Run the full NFR Parts 1–12 consistency audit. REQ-NFR-001 may close only if the audit is GREEN with no unresolved critical product-level gap or contradiction.
