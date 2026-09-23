# Product Requirements Document — International Conference Platform

**Document ID:** ICP-PRD-001  
**Version:** 0.1.0-draft  
**Status:** PHASE 0 — DRAFT, NOT IMPLEMENTATION BASELINE  
**Updated:** 2026-09-23  
**Owner:** Product Owner / Conference Organizer  
**Purpose:** Define the product to be built before domain/data/technical implementation begins.

**Product Owner Review Progress:** Part 1 — product identity/scale/recurrence/V1-vs-future **APPROVED**; Part 2 — actor model/multi-role/edition scope + optional ORCID **APPROVED**; Part 3 — Progressive/Hybrid Authentication Model **APPROVED**; Part 4A — Registration/Profile/Join Edition/Submission Entry **APPROVED**; Part 4B — Manual Payment → Finance Verification → Official Submission **APPROVED**; Part 4C — Administrative/Academic Processing → Decision → Refund **APPROVED on 2026-09-23**.

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

## 9. Authentication / Registration — Approved Progressive/Hybrid Model

**Decision:** Progressive/Hybrid Account Model.

### Participant entry

```text
Register / Submit
→ provide email
→ verify email
→ create/activate participant workspace
→ complete profile
→ join conference edition
→ use relevant participant/author workflow
```

Participant password is **not required at initial registration**. Verified email provides the initial low-friction access path. Secure email link/code may be used for normal participant sign-in.

### Account strengthening

A participant account may later support stronger credentials such as:
- password;
- passkey;
- multi-factor authentication.

Exact technical mechanism/provider is an architecture decision, not locked by this PRD.

### Reuse across editions

The same account is reused across conference editions. Edition membership, roles, and historical snapshots remain edition-specific.

### Co-authors

A co-author/contributor does not need an account merely to be listed on a submission.

### Reviewer

A Reviewer must use an authenticated account before accessing assigned manuscripts, confidential comments, recommendations, or review workspace.

### Privileged internal roles

Privileged internal users require stronger authentication than ordinary participant access. Roles such as Super Admin, Conference Admin, Finance, Academic Decision Authority, and Publication administration must be designed for MFA/strong-auth enforcement.

### Recovery and sensitive account changes

Front Office may guide and escalate authentication problems but must not perform informal account takeover or identity recovery.

Account recovery and sensitive changes such as primary email replacement require controlled verification/re-authentication workflows.

### Identity separation

Authentication identity is distinct from scholarly identity. ORCID remains optional and is never used as the required login identity.

### Approved principles

1. Workspace-managing participants have reusable accounts.
2. Initial participant registration does not require a password.
3. Email verification is required for participant account activation/access.
4. Secure email link/code is an accepted participant access pattern.
5. Account security may be strengthened with password/passkey/MFA.
6. Account is reusable across editions.
7. Co-author/contributor account remains optional.
8. Reviewer requires authenticated account access.
9. Privileged internal roles require stronger authentication/MFA readiness.
10. Front Office cannot perform informal recovery/account takeover.
11. Recovery and primary-email changes are sensitive workflows.
12. Scholarly identity and ORCID remain separate from login identity.

---

## 10. End-to-End Lifecycle — Validation In Progress

### 10.1 Stage 4A — Registration → Profile → Join Edition → Submission Entry

**Status: APPROVED**

```text
Visitor
→ choose conference edition
→ Register / Join Conference
→ provide email
→ if existing account: authenticate
→ if new account: verify email and activate reusable account
→ complete/update global profile
→ join edition
→ edition membership created
→ choose current activity:
   ├─ Participant Only
   └─ Submit Paper / Author
        → create Submission in DRAFT
```

Approved business rules:

1. The same verified email/account is reused across conference editions; a new account is not created for each edition.
2. Email must be verified before edition membership is considered active.
3. A reusable global person/profile may contain identity/contact/current affiliation/preferred locale and optional ORCID.
4. Joining a conference creates an **edition membership**, not another account.
5. Participant-only and Author/Submit-Paper are workflow choices, not permanent mutually exclusive identities. A participant may later become an Author while submission is open.
6. One account may own/manage more than one submission when the edition policy allows it. Submission limits are edition-configurable and must not be hardcoded.
7. Choosing Submit Paper creates a **DRAFT** submission. A draft is not yet an official conference submission.
8. Current profile data and historical conference/submission data must be separable. Later profile changes must not silently rewrite historical edition/submission metadata.
9. Payment does not occur merely because a person created an account, profile, edition membership, or draft submission.

### 10.2 Stage 4B — Abstract Draft → Manual Payment → Finance Verification → Official Submission

**Status: APPROVED**

```text
DRAFT
→ author completes abstract metadata
→ Submit & Proceed to Payment
→ validation
→ AWAITING_PAYMENT
→ manual bank transfer
→ upload payment proof
→ PAYMENT_SUBMITTED
→ Finance cross-checks actual receipt outside the conference system
   ├─ VERIFIED
   │    → PAID
   │    → OFFICIAL_SUBMISSION
   │    → submitted-version snapshot
   │    → Academic Processing
   └─ ACTION_REQUIRED
        → reason recorded
        → author corrects / re-uploads proof
```

Approved business rules:

1. V1 uses **manual bank transfer**, not a payment gateway.
2. Payment obligation begins only after a valid draft is submitted for payment.
3. The system shows the authoritative destination account, amount due, reference/submission code, and payment deadline.
4. The amount due is determined by edition policy/configuration; the author does not freely type the amount owed.
5. Uploading transfer proof creates `PAYMENT_SUBMITTED`; it does **not** mean paid/verified.
6. Finance must cross-check the actual incoming transfer using the organization's bank/mobile-banking record outside the conference platform.
7. Finance records a verification result in the platform:
   - verified → `PAID`;
   - not verifiable/incorrect → `PAYMENT_ACTION_REQUIRED` with an auditable reason.
8. Only `PAID` may transition the paper to `OFFICIAL_SUBMISSION`.
9. `PAID` does not imply academic acceptance.
10. Before successful payment, the author may cancel the submit intent and return to `DRAFT`, subject to edition deadline/policy.
11. After payment/official submission, withdrawal is a formal workflow; it does not silently return the paper to draft.
12. Official submission creates a stable submitted-version snapshot for academic processing.
13. Subsequent substantive changes require a controlled revision/correction workflow; submitted content is not silently overwritten.
14. Payment proof, Finance verification, verifier identity, timestamps, and rejection/action-required reason must be auditable.
15. Front Office may explain payment status but cannot mark payment as verified.
16. Sensitive bank-account/mutation information remains Finance-restricted and is not exposed to Author/FO.
17. The V1 domain should remain capable of supporting a future payment provider without changing the conference lifecycle semantics.

### 10.3 Stage 4C — Administrative / Academic Processing → Decision → Refund

**Status: APPROVED**

```text
OFFICIAL_SUBMISSION
→ ADMINISTRATIVE_SCREENING
   ├─ ADMIN_CORRECTION_REQUIRED → correction → re-check
   ├─ ADMIN_INELIGIBLE → terminal academic path + refund policy
   └─ PASS
        → ACADEMIC_PROCESSING
           ├─ ACCEPTED → proceed to Full Paper stage
           ├─ REVISION_REQUIRED
           │    → Abstract Revision Version N+1
           │    → Academic Re-check
           └─ REJECTED
                → REFUND_ELIGIBLE
                → manual Finance refund
                → REFUNDED
```

Approved business rules:

1. Official submissions undergo administrative screening before academic decision processing.
2. Administrative screening checks eligibility/completeness, not scholarly quality.
3. Academic-processing method is configurable per edition; the platform must not hardcode one universal review model.
4. Core academic outcomes for abstract selection are `ACCEPTED`, `REVISION_REQUIRED`, and `REJECTED`.
5. Abstract revision creates a new version; prior submitted/reviewed versions remain traceable.
6. Academic decision authority and Finance authority are separate.
7. Academic rejection automatically creates **refund eligibility**.
8. For V1, an academically rejected submission receives **100% refund of the conference fee actually paid**.
9. Refund execution is manual and handled by authorized Finance personnel.
10. Refund status, amount, recipient data, processor, timestamps, proof/record, and reasons are auditable.
11. Author withdrawal is not treated as academic rejection; refund follows edition withdrawal policy.
12. Administrative ineligibility has a separate edition refund policy and is not automatically treated as academic rejection.
13. Front Office may explain status but cannot change academic decisions or execute/mark refunds.
14. Refund completion does not erase the historical payment, submission, or academic-decision records.

#### Review anonymity note

The product does **not** require abstract review to be double-blind. The exact abstract-review mode remains edition-configurable/open for the first edition. A practical initial direction is administrative/academic screening or single-anonymous committee/reviewer assessment rather than forcing double-blind review.

A **separate publication-quality review gate for the full paper after presentation** remains a candidate design for later lifecycle stages. Whether that later review is single-anonymous, double-anonymous, committee review, or another documented model remains **OPEN** and must be decided before publication workflow implementation.

### 10.4 Remaining lifecycle stages to validate

```text
Stage 4D
Full Paper
→ Validation
→ Scheduling
→ Presentation

Stage 4E
Post-Presentation Assessment
→ Revision when required
→ Publication Eligibility Gate

Stage 4F
Proceedings Queue
→ OJS
→ Published / Archived
→ Certificate / Historical Record
```

These later stages remain unapproved until reviewed by the Product Owner.

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

**V1 payment model: manual bank transfer with Finance verification.**

The system should support:
- edition-configured payment amount/fee category;
- authoritative bank-transfer instructions;
- payment/submission reference;
- payment deadline;
- payment-proof upload;
- `AWAITING_PAYMENT`;
- `PAYMENT_SUBMITTED`;
- `PAYMENT_ACTION_REQUIRED`;
- `PAID`;
- `PAYMENT_EXPIRED` where edition policy uses expiry;
- Finance-only verification action;
- auditable verifier, time, result, and reason;
- participant-visible non-sensitive payment status.

Uploading a transfer proof is not equivalent to payment verification. Only authorized Finance verification may make the payment `PAID`.

V1 does not require payment-gateway, QRIS, virtual-account, or similar integration. Future providers may be added behind the same payment-domain/lifecycle boundary.

### 11.7 Refund

For V1:
- academic rejection → refund eligible;
- academic rejection refund amount → **100% of conference fee actually paid**;
- refund execution → manual Finance workflow.

Refund policy remains configurable for other causes such as:
- author withdrawal;
- administrative ineligibility;
- cancellation or exceptional organizer policy.

Possible refund states include:
- eligible;
- requested / data required;
- approved;
- processing;
- refunded;
- failed;
- rejected/not eligible.

The system must not promise or mark a refund through Front Office/WhatsApp while authoritative Finance state says otherwise.

### 11.8 Administrative / Academic Processing

Every official submission enters administrative screening before academic processing.

Edition academic selection may use:
- academic committee screening;
- single-anonymous review;
- double-anonymous review;
- other documented peer-review model;
- open/broad acceptance subject to eligibility.

The platform must support edition-configurable selection/review policy and must not hardcode double-blind review.

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

For the initial edition, abstract review is **not required to be double-blind**. Exact reviewer count, anonymity model, conflict-of-interest rules, reviewer honorarium policy, and whether the post-presentation full paper receives a separate publication-quality review remain to be finalized.

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
- OPEN-002 Initial payment model/provider and verification: **RESOLVED — V1 manual bank transfer + Finance verification; no payment gateway required.**
- OPEN-003 Exact abstract review model for first edition: **PARTIALLY RESOLVED — double-blind is not mandatory; exact screening/reviewer/anonymity model remains OPEN.**
- OPEN-004 Refund policy: **PARTIALLY RESOLVED — academic rejection = 100% refund of conference fee paid; withdrawal/admin-ineligible rules remain OPEN.**
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
