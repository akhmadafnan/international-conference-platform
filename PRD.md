# Product Requirements Document — International Conference Platform

**Document ID:** ICP-PRD-001  
**Version:** 0.1.0-draft  
**Status:** PHASE 0 — DRAFT, NOT IMPLEMENTATION BASELINE  
**Updated:** 2026-09-23  
**Owner:** Product Owner / Conference Organizer  
**Purpose:** Define the product to be built before domain/data/technical implementation begins.

**Product Owner Review Progress:** Part 1 — product identity/scale/recurrence/V1-vs-future **APPROVED**; Part 2 — actor model/multi-role/edition scope **APPROVED**, with ORCID policy amendment **APPROVED on 2026-09-23**.

---

## 1. Executive Summary

The International Conference Platform is a reusable **academic conference lifecycle management platform**.

It is not merely:
- a conference landing page;
- a paper-upload form;
- an OJS replacement;
- a one-year event application.

It should eventually coordinate the lifecycle from conference information and participant registration through abstract submission, payment, academic processing, presentation, revision, publication eligibility, OJS handoff, certificates, support, and historical reporting.

The first realistic scale is approximately **100 participants/submissions**, initially dominated by Indonesian participants. This initial scale is an **approved planning baseline**. The architecture must remain operationally simple at this scale while avoiding a disposable one-event schema.

---

## 2. Product Vision

Create a participant-friendly, committee-manageable, multilingual platform that can be reused for recurring conference editions without rebuilding the system from zero.

Product principle:

> **Simple now, scalable later.**

Scale readiness means sound domain boundaries and reusable data—not premature microservices or enterprise infrastructure.

---

## 3. Problem Statement

Academic conference operations often become fragmented across:
- public website/CMS;
- forms;
- spreadsheets;
- bank/payment confirmation;
- WhatsApp;
- email;
- reviewer coordination;
- scheduling documents;
- certificate generation;
- proceedings/OJS.

This creates risks:
- duplicate or inconsistent records;
- status ambiguity;
- unclear responsibility;
- manual reconciliation;
- weak audit trail;
- delayed publication;
- participants repeatedly asking for status through chat;
- conference history disappearing after the event.

The product should establish a **single authoritative lifecycle** while integrating with specialist systems where appropriate.

---

## 4. Product Principles

1. **Conference-driven, not page-driven.**
2. **Multi-edition from the beginning.**
3. **Payment does not determine academic acceptance.**
4. **Presentation does not automatically mean publication-ready.**
5. **OJS is downstream publication infrastructure.**
6. **WhatsApp is a communication gateway, not a source of truth.**
7. **Scholarly identity and metadata should be standards-ready.**
8. **Indonesian, English, and Arabic are native product languages.**
9. **Arabic RTL is a first-class design requirement.**
10. **Every consequential state change should be traceable.**
11. **Initial simplicity must not create future structural debt.**
12. **AI/developer work is governed by documented requirements and quality gates.**

---

## 5. Initial Context

### 5.1 Initial expected scale
- around 100 participants/submissions;
- initially primarily Indonesia;
- limited committee/technical resources;
- likely one active edition at a time initially.

### 5.2 Recurrence
The conference may run:
- annually;
- every two years;
- or another configurable cycle.

Historical editions must remain accessible. One application installation is intended to serve successive editions rather than rebuilding a separate application/database for each event.

### 5.3 Initial operational communication
One WhatsApp number is planned as Front Office (FO).

---

## 6. Goals

The product should:

- centralize conference lifecycle status;
- reduce committee dependence on scattered spreadsheets/chat;
- provide clear participant-facing status;
- support reusable conference editions;
- manage payment/refund traceably;
- support configurable abstract-selection workflow;
- support presentation and post-presentation revision;
- block incomplete manuscripts from publication;
- hand publication-ready records to OJS;
- support certificates/documents;
- provide multilingual participant experience;
- preserve auditable history;
- prepare scholarly metadata for ORCID/ROR/OJS/Crossref/DOI ecosystems.

---

## 7. Non-Goals for Initial Release

The first release is not required to implement all future possibilities.

Unless later accepted, initial release should not automatically imply:
- multi-region infrastructure;
- Kubernetes;
- microservices;
- thousands of concurrent participants;
- multi-currency;
- full Crossref DOI deposit automation;
- automatic ORCID OAuth;
- advanced ROR autocomplete;
- full bidirectional OJS synchronization;
- complex AI review/scoring;
- global payment coverage;
- multiple simultaneous conference series.

These may be architecture-ready without being implemented in V1. This separation between **V1 operational completeness** and **future capability readiness** is approved product direction.

---

## 8. Product Actors — Candidate Set

Final permission matrix remains Phase 0 work.

### Participant-side
- Visitor
- Prospective Participant
- Participant
- Author / Corresponding Author
- Co-author
- Presenter
- Non-presenting Participant
- Invited Speaker / Keynote — edition-dependent

### Internal
- Front Office
- Finance Staff / Verifier
- Scientific / Academic Committee
- Reviewer
- Academic Decision Authority
- Event Operations
- Session Chair
- Moderator
- Publication Team / Proceeding Editor
- Technical Administrator
- Conference Administrator
- Super Administrator

### Role principles — Product Owner approved

- One person may have multiple roles.
- Most conference roles are **edition-scoped**, not permanent global roles.
- `Participant` is a base conference membership/status concept, not an exclusive role that prevents additional roles.
- Author/Corresponding Author and Presenter are distinct concepts.
- A co-author/contributor does **not** need to own a login account merely to appear on a submission.
- Front Office, Finance, Academic, Publication, and Event functions have separate authority boundaries and must not casually override one another.
- Reviewer and Author roles may coexist for the same person/edition, but later permission/conflict-of-interest rules must prevent inappropriate review assignments.
- Scholarly identity must remain conceptually separate from authentication/account membership.

Conceptual scope:

```text
GLOBAL PLATFORM ROLE
└── Super Admin

EDITION-SCOPED ROLES / MEMBERSHIPS
├── Conference Admin
├── Front Office
├── Finance
├── Academic Committee / Decision Authority
├── Reviewer
├── Event Operations
├── Session Chair / Moderator
├── Publication Team
├── Participant
├── Author
└── Presenter
```

Detailed permission matrices remain Phase 0 work.

---

## 9. Open Product Decision — Authentication / Registration

**OPEN-001**

Earlier product intent included registration without login, while benchmark analysis showed advantages of an authenticated workspace.

Options requiring analysis:

### Option A — Full account
```text
Create account
→ verify/login
→ dashboard
→ submission
```

### Option B — Secure no-login management
```text
Submit/registration
→ verified email
→ secure link/code
→ manage record
```

### Option C — Progressive/hybrid
```text
Low-friction registration
→ verified email
→ workspace/account activated when needed
```

Do not implement authentication until this is accepted.

---

## 10. End-to-End Lifecycle — Candidate Baseline

```text
Conference Edition
→ Participant/Author Entry
→ Profile / Scholarly Identity
→ Abstract Draft
→ Abstract Submit
→ Payment
→ Payment Verification
→ Official Submission
→ Administrative / Academic Processing
→ Acceptance / Rejection / Revision
→ Refund Flow when applicable
→ Full Paper
→ Final Validation
→ Scheduling
→ Presentation
→ Post-Presentation Assessment
→ Revision when required
→ Publication Eligibility Gate
→ Proceedings Queue
→ OJS
→ Published / Archived
→ Certificate / Historical Record
```

This lifecycle will be formally validated during Phase 0.

---

## 11. Core Product Capabilities

### 11.1 Public Conference Portal

Expected capabilities:
- conference identity;
- theme;
- description;
- important dates;
- tracks/subthemes;
- speakers;
- call for papers;
- author guidelines;
- fees/payment policy;
- refund policy;
- venue;
- FAQ;
- downloads;
- contact/Front Office;
- news/announcements;
- supported languages.

Public CMS content must support `id`, `en`, and `ar`.

### 11.2 Conference Series & Edition

The product must distinguish a long-lived conference series from individual editions.

Edition-level configuration may include:
- theme;
- dates;
- venue;
- tracks;
- fees;
- refund policy;
- selection mode;
- review rules;
- submission deadlines;
- revision deadlines;
- publication destination;
- document templates;
- certificate rules.

No year-specific tables.

### 11.3 Registration & Identity

The system needs participant identity without assuming that every scholarly contributor must have a login account.

Requirements to analyze:
- name representation suitable for Indonesian/international names;
- email;
- phone/country code;
- country;
- institution/affiliation;
- preferred locale;
- scholarly identifiers;
- edition membership;
- participant category.

### 11.4 Scholarly Identity

Architecture should support:
- **optional ORCID**;
- multiple affiliations;
- organization identity;
- future ROR mapping.

ORCID is **not mandatory** for Participant, Author, Co-author/Contributor, Presenter, Reviewer, or other actors.

A person who does not have ORCID must still be able to register, submit, participate, review, present, and appear as a contributor according to their permissions.

If a person already has an ORCID, the identifier may be added to their scholarly identity/profile and reused where appropriate for submission/publication metadata.

If ORCID authentication/verification is implemented later, the system must be able to distinguish:
- ORCID entered manually;
- ORCID authenticated/verified through an official integration.

Absence of ORCID must never make a person academically ineligible unless a future conference policy explicitly introduces such a rule and records it as a new product decision.

### 11.5 Abstract Submission

Expected concepts:
- title;
- abstract;
- keywords;
- track/subtheme;
- contributors/authors;
- contributor order;
- corresponding contact;
- affiliations;
- primary language;
- translated metadata where required;
- submission history;
- public/business submission code.

Abstract and later full-paper/revision stages belong to one logical submission lifecycle.

### 11.6 Payment

Current direction:
- payment occurs around abstract submission;
- payment is required for official submission according to edition policy;
- payment does not imply acceptance.

System should support:
- invoice/charge reference;
- amount/currency;
- payment state;
- verification;
- evidence where operationally needed;
- audit history;
- participant-visible status.

Provider and automation level are not locked.

### 11.7 Refund

Refund policy must be configurable by edition.

Possible concepts:
- eligible;
- requested;
- approved;
- processing;
- refunded;
- failed;
- rejected.

The system must not promise a refund through WhatsApp while authoritative finance state says otherwise.

### 11.8 Administrative / Academic Processing

Edition may use:
- peer review;
- academic screening;
- administrative screening;
- open/broad acceptance subject to eligibility.

Exact workflow must be configurable enough to avoid rewriting the system between editions.

### 11.9 Review

If peer review is used, expected capabilities may include:
- reviewer assignment;
- review round;
- due date;
- recommendation;
- author-facing comment;
- confidential comment;
- review completion;
- decision support.

Exact blind-review policy remains to be confirmed per edition.

### 11.10 Academic Decision

Possible outcomes may include:
- accepted;
- minor revision;
- major revision;
- rejected;
- administratively ineligible;
- withdrawn.

Decision authority must be separate from Front Office.

### 11.11 Full Paper & Manuscript Versions

A submission may progress through:
- abstract version;
- full paper;
- pre-presentation revision;
- post-presentation revision;
- final publication version.

Version history must be traceable.

### 11.12 Scheduling & Presentation

Expected capabilities:
- venue;
- room;
- session;
- date/time;
- presenter;
- moderator/session chair;
- presentation status;
- attendance evidence where required.

### 11.13 Post-Presentation Revision

Key rule:

```text
PRESENTED
≠
PUBLICATION_READY
```

If revision is required:
- submission becomes revision-required;
- author receives clear status/deadline;
- revised file is submitted;
- responsible role approves/rejects/requests further changes;
- publication remains blocked until satisfied.

### 11.14 Publication Eligibility Gate

Candidate gate items:
- payment satisfied;
- required full paper exists;
- presentation requirement satisfied;
- attendance requirement satisfied;
- required revision approved;
- final approval;
- publication consent/metadata complete.

Exact rule is edition-configurable.

### 11.15 Publication / OJS

The conference platform controls conference readiness.

```text
Conference Platform
→ Publication Eligible
→ Publication Queue
→ OJS
```

OJS is not the source of truth for payment, presentation, refund, or conference status.

Initial OJS integration may be manual/assisted/export-based before deeper automation.

### 11.16 Documents

Candidate generated documents:
- Letter of Acceptance;
- invitation letter;
- invoice/receipt;
- payment confirmation;
- certificate;
- reviewer certificate;
- committee certificate;
- speaker certificate.

Document numbering, QR verification, and templates require separate specifications.

### 11.17 Certificates

Certificate eligibility should be rule-driven rather than manual mass generation alone.

Potential inputs:
- role;
- registration;
- payment;
- attendance;
- presentation;
- reviewer completion;
- committee membership.

### 11.18 Front Office & Support

Single WhatsApp FO is the participant communication gateway.

FO should be able to:
- search participant/submission;
- view permitted consolidated status;
- provide standard guidance;
- create/escalate support cases;
- view communication/support history as allowed.

FO must not automatically be able to:
- accept/reject papers;
- alter reviews;
- approve refunds;
- edit verified finance records;
- publish papers;
- bypass publication gates.

### 11.19 Notification & Communication

Potential channels:
- in-app;
- email;
- WhatsApp.

Canonical event/state must live in the platform, not only in outbound messages.

### 11.20 Reporting & Analytics

Initial reporting needs may include:
- registrations;
- submissions;
- paid/unpaid;
- accepted/rejected;
- refund states;
- full-paper completion;
- presentation readiness;
- revision outstanding;
- publication-ready;
- certificate eligibility;
- support cases.

---

## 12. Multilingual & Localization Requirements

Mandatory locales:
- `id` — Indonesian — LTR
- `en` — English — LTR
- `ar` — Arabic — RTL

Localization categories:
1. UI/system text;
2. CMS/public content;
3. scholarly metadata;
4. generated communications/documents.

Arabic requires:
- RTL layout;
- directional navigation;
- form/table alignment;
- bidirectional-text safety for email, DOI, ORCID, submission codes, and numbers;
- responsive testing.

Exact persistence strategy is not decided by this PRD.

---

## 13. Scholarly Metadata & Standards Readiness

The canonical product model should be capable of mapping to:
- optional ORCID for researchers/contributors when available;
- ROR for organizations;
- OJS for publication workflow;
- Crossref/DOI metadata;
- ISBN/ISSN when applicable;
- standard country codes;
- standard language tags.

External identifiers must not become internal primary keys.

The system should be standards-native rather than copying OJS database structure.

---

## 14. Auditability Requirements

Consequential actions should be traceable where appropriate:
- payment verification;
- refund decisions;
- academic decisions;
- reviewer assignments;
- manuscript version changes;
- scheduling;
- presentation status;
- revision approval;
- publication eligibility;
- document/certificate issuance;
- privilege/role changes.

The final audit model belongs in architecture/data specifications.

---

## 15. Security & Privacy — Product Requirements

The product must:
- protect participant personal data;
- enforce authorization server-side;
- separate communication privilege from decision privilege;
- prevent one user from accessing another user's protected records without permission;
- keep production secrets outside the public repository;
- avoid exposing reviewer/private finance/internal notes publicly;
- maintain clear public vs private tracking boundaries.

Detailed threat model is a later architecture deliverable.

---

## 16. Non-Functional Requirements — Initial

Formal NFR targets remain Phase 0 work, but product expectations include:

### Usability
- mobile-friendly;
- understandable by non-technical conference participants;
- clear status and next action;
- low administrative friction.

### Accessibility
- keyboard/focus fundamentals;
- readable responsive layouts;
- Arabic RTL support;
- semantic forms and messages.

### Maintainability
- documented domain boundaries;
- tests;
- migration discipline;
- no duplicated business logic across channels.

### Reliability
- authoritative state preserved;
- retry/failure states for external integrations;
- backup/recovery considered before production.

### Performance
The first release should comfortably serve expected conference traffic without enterprise over-engineering.

### Scalability
Architecture should not require a rewrite merely because future editions grow to hundreds or thousands of participants.

---

## 17. Initial Release / V1 Direction

Likely V1 focus:
- reusable conference edition;
- public conference portal;
- participant/author entry;
- abstract submission;
- payment/verification;
- configurable screening/decision;
- full paper;
- schedule/presentation;
- post-presentation revision;
- publication gate;
- OJS handoff;
- documents/certificates;
- Front Office visibility;
- `id/en/ar` including RTL;
- reporting sufficient for committee operations.

This is a direction, not yet the final V1 scope.

---

## 18. Future Capability Candidates

Potential future growth:
- larger participant volume;
- more countries;
- international payment;
- multi-currency;
- ORCID OAuth;
- ROR lookup;
- automated Crossref deposit;
- deeper OJS API integration;
- hybrid/online conference;
- QR attendance;
- advanced scheduling;
- reviewer pool management;
- multiple concurrent series/editions.

Architecture readiness does not mean immediate implementation.

---

## 19. Key Open Questions

- OPEN-001 Authentication/registration model.
- OPEN-002 Exact initial payment method/provider and verification model.
- OPEN-003 Exact abstract acceptance/review model for first edition.
- OPEN-004 Refund policy percentages/fees/deadlines.
- OPEN-005 Full-paper requirement and timing.
- OPEN-006 Presentation attendance evidence.
- OPEN-007 Who approves post-presentation revision?
- OPEN-008 Initial OJS integration: export/manual-assisted/API?
- OPEN-009 Certificate eligibility rules.
- OPEN-010 Initial participant types and fee categories.
- OPEN-011 Whether conference series name/brand is already final.
- OPEN-012 Initial production hosting constraints/budget.

These must be resolved or intentionally deferred before dependent implementation.

---

## 20. Product Documentation Model

```text
PRD
  ↓
Feature Specification
  ↓
READY Ticket
  ↓
Implementation Plan
  ↓
Code / Migration
  ↓
Tests / Regression
  ↓
UAT
  ↓
Closeout
```

PRD defines product truth. It does not define exact tables or framework code.

---

## 21. Product Acceptance Philosophy

A capability is not complete merely because:
- code compiles;
- a route exists;
- a screen renders.

Completion requires:
- business rule correctness;
- authorization correctness;
- expected localization;
- regression evidence;
- documentation consistency;
- human UAT when required.

---

## 22. PRD Baseline Gate

This document becomes `v1.0-baseline` only after:
- actor/lifecycle consistency audit;
- open high-impact questions resolved or explicitly deferred;
- requirement register reconciled;
- Product Owner approval;
- no contradiction with accepted decisions.

Until then it remains a Phase 0 draft.
