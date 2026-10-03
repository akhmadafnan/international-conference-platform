# ICHES Product Blueprint v1

**Document ID:** ICHES-PROD-BP-001  
**Version:** 1.0  
**Status:** APPROVED WORKING BLUEPRINT FOR ACCELERATED V1 PLANNING  
**Updated:** 2026-09-30  
**Implementation authorization:** NOT YET — corrective re-baseline, metadata contract, stack/ERD freeze, and development plan remain required.

> **Pre-Phase 02 supersession notice (2026-10-03):** Payment-before-abstract and related publication-boundary statements in this historical working blueprint are superseded by `docs/governance/PRE_PHASE02_PRODUCT_REBASELINE.md` and the current Decision Register. Non-conflicting blueprint content remains authoritative.

## 1. Purpose

This blueprint converts Product Discovery into an implementation-oriented product contract for the first operational ICHES release.

It is optimized for an aggressive **two-week V1 delivery target** by preserving the complete conference lifecycle while deliberately excluding non-essential automation and enterprise complexity.

## 2. Product Position

ICHES is a reusable **Conference & Event Experience Platform** for recurring academic conference editions.

The product joins:

Public Website
→ Registration
→ Participation Package
→ Payment
→ Participant Dashboard
→ Abstract Submission
→ Academic Review
→ Presentation LoA
→ Full Article
→ Scheduling
→ Event-Day Operations
→ Presentation Assessment
→ Revision
→ Publication Handoff
→ Awards
→ Certificates
→ Historical Edition Archive

The application is not:
- an ERP;
- an OJS replacement;
- a hotel/travel platform;
- a contract-management system;
- an automatic academic decision engine;
- an automatic award winner engine.

## 3. V1 Delivery Principle

V1 must be **operationally complete**, not feature-maximal.

Preferred implementation style:
- simple CRUD where sufficient;
- configuration instead of hardcoding;
- bulk actions for committee operations;
- strong server-side authorization;
- explicit lifecycle facts;
- state-aware participant UX;
- audit trail for consequential actions;
- one Laravel modular monolith;
- no unnecessary internal API layer.

## 4. Accelerated Domain Assumptions

For the two-week target, the Product Owner authorizes the following assumptions as the working V1 baseline pending any later real-world institutional correction.

### 4.1 Payment and rejected abstract
Payment is for event participation.

If an abstract is rejected:
- presenter path ends;
- participant registration remains active;
- no automatic academic-rejection refund.

Refund remains an exceptional/manual policy for cases such as duplicate payment, event cancellation, or authorized administrative correction.

### 4.2 Participation packages
Packages are fixed choices per edition, but admin-configurable.

Initial conceptual presets:
- Conference Only
- Conference + Evening/MoU
- Full Program
- International Community Service Only

Participants do not build arbitrary activity bundles in V1.

### 4.3 Fees
V1 pricing model:

`Participation Package + optional Participant Category`

Keep category configuration simple. No coupon/tax/dynamic pricing engine.

### 4.4 Abstract review
- default anonymity: single-anonymous;
- default reviewer count: 1;
- reviewer count remains configurable;
- default maximum normal revision cycle: 1;
- authorized Academic exception may reopen/allow correction.

### 4.5 Presenter
Actual presenter is selected from contributors after Full Article submission and before final schedule publication.

Corresponding Author and Presenter may differ.

### 4.6 Presentation slides
Slides are optional/configurable and are not a V1 hard blocker.

### 4.7 Event mode
First accelerated V1 assumes an offline-first edition.

Architecture remains capable of edition-level OFFLINE / ONLINE / HYBRID configuration later.

### 4.8 Awards
- Best Presenter: Overall edition award;
- Best Article: winning paper;
- Best Article certificate: individual certificate for every listed author;
- application provides evidence/candidate data only;
- Committee records the final decision and may disregard system ranking.

### 4.9 Certificates
- Participant certificate: attendance-based;
- Presenter certificate: PRESENTED-based;
- Presenter may receive both Participant and Presenter certificates;
- Reviewer certificate: duty-completion based;
- Moderator certificate: duty-completion based;
- Committee/Appreciation certificate supported;
- Award certificate after award finalization.

### 4.10 Publication
Proceedings is the default destination for Final-ACC papers.

Authorized Publication/Academic authority may override a paper to Selected Journal.

Selected for Journal does not mean Accepted by Journal.

### 4.11 Publication documents
- Presentation LoA: after abstract acceptance;
- Proceedings Publication Acceptance: after final academic approval and proceedings destination when needed;
- Selected Journal: Selection/Handoff Notice until the journal itself confirms acceptance.

### 4.12 Application authorities
V1 separates at least these authorities:
- Finance Authority → payment verification/correction;
- Academic Authority → abstract final decision and academic final approval;
- Event/Program Authority → schedule publication and operational event facts;
- Award/Committee Authority → final award decision;
- Publication Authority → publication destination/handoff;
- Certificate Authority → certificate issue/revoke/reissue.

A person's committee title does not automatically grant every application authority.

## 5. Primary Product Actors

Public / participant side:
- Visitor
- Registered User
- Participant
- Corresponding Author
- Contributor / Co-author
- Presenter
- Reviewer
- Moderator
- Committee member / staff
- Invited Speaker / Keynote when applicable

Operational authorities:
- Conference Admin
- Finance
- Academic Team / Academic Decision Authority
- Reviewer
- Event / Program Operations
- Moderator
- Publication Team
- Certificate/Admin Authority
- Technical / Super Admin

One account may hold multiple edition-scoped roles/relationships.

Contributor records do not require user accounts.

## 6. Series and Edition Model

```text
CONFERENCE SERIES
ICHES
   ↓
CONFERENCE EDITIONS
ICHES 2027
ICHES 2029
...
```

Series-level:
- name;
- brand;
- logo;
- general identity/about.

Edition-level:
- theme;
- dates;
- host;
- organizers;
- venue;
- mode;
- packages;
- fees;
- important dates;
- tracks;
- speakers;
- activities;
- publication destinations;
- committee/authorities;
- templates/documents;
- schedule.

UNISYA may host an edition without being hardcoded as permanent owner of the series.

## 7. Registration and Participation

```text
REGISTER
→ PROFILE
→ SELECT PACKAGE
→ PAYMENT DUE
→ UPLOAD PROOF
→ FINANCE VERIFY
→ REGISTRATION CONFIRMED
→ EVENT PASS + QR
```

Registration state and academic submission state are separate.

QR is an identity/lookup key, not an attendance event by itself.

## 8. Payment V1

Manual bank transfer only.

Data:
- expected amount;
- payment instructions/account;
- sender;
- transfer date;
- transferred amount;
- proof file;
- verification status;
- correction note;
- verification actor/time.

V1 status model:
- UNPAID
- PROOF_SUBMITTED
- PAID
- CORRECTION_REQUIRED
- CANCELLED

Replacing payment proof preserves history.

## 9. Participant Dashboard

Primary UX question:

> What should I do next?

Core areas:
- Overview
- My Activities
- My Submission — conditional
- My Reviews — conditional
- Payment
- Schedule
- My Documents
- Help
- Notifications
- Profile

Participant dashboard is state-aware and should never expose technical enum values directly.

## 10. Abstract Submission

Wizard:
1. Details
2. Contributors
3. References
4. Files
5. Review & Submit

Details:
- submission language: id/en/ar;
- track;
- title;
- optional subtitle;
- abstract;
- structured keywords.

UI language is independent from submission language.

## 11. Contributors

Fields:
- given name;
- family/surname with single-name support;
- email;
- country;
- affiliation;
- optional department/faculty;
- optional ORCID;
- corresponding author;
- author order.

Affiliation:
- ROR-first when available;
- manual fallback always allowed.

## 12. References

Each reference preserves:
- order;
- raw citation;
- optional DOI;
- optional enriched structured metadata later.

No large mandatory bibliographic form per citation in V1.

## 13. Administrative Screening

Before academic review, authorized staff may check:
- metadata completeness;
- track/scope;
- abstract length;
- file/template conformance if enabled;
- similarity requirement if edition policy enables it.

Screening correction is distinct from academic revision.

## 14. Abstract Review

Default V1:
- single-anonymous;
- one reviewer;
- Accept / Revision / Reject recommendation;
- one normal revision cycle;
- Academic Authority records final decision.

Reviewer recommendation never automatically becomes final decision.

Self-review is blocked.

Same-affiliation assignment may warn.

## 15. Presentation LoA

Issued after abstract acceptance.

LoA means:
**Accepted for Presentation at ICHES**

It does not include:
- room;
- reviewer;
- presentation slot;
- publication acceptance.

LoA availability unlocks Full Article upload.

## 16. Full Article and Versioning

Accepted submission:
→ Full Article upload enabled.

First Full Article becomes manuscript version 1.

Later revision never overwrites version 1.

After Full Article submission:
→ actual presenter confirmation
→ READY FOR SCHEDULING.

## 17. Scheduling

Only papers with Full Article submitted enter Scheduling Pool.

Core scheduling facts:
- Session
- Room
- Presentation Slot
- Reviewer assignment
- Moderator assignment
- date/time

Internal assignments remain draft until Publish Schedule.

Participant sees only published schedule.

System warns about obvious time/person conflicts.

## 18. Event-Day Operations

Participant QR:
- lookup;
- activity eligibility;
- check-in support.

Attendance is recorded per activity.

`ATTENDED != PRESENTED`

For paper presentations:
- Moderator/Event Operations records PRESENTED or NO_SHOW;
- reviewer records academic assessment.

## 19. Presentation Assessment

One assessment form may capture:
- publication/revision recommendation;
- article-quality criteria;
- presenter-performance criteria;
- reviewer notes.

Core recommendation:
- No Revision Required
- Minor Revision
- Major Revision
- Not Recommended for Publication

Exact scoring rubric may remain edition-configurable.

## 20. Post-Presentation Revision

No Revision:
- no extra upload;
- existing Full Article proceeds.

Revision Required:
- reviewer notes shown;
- deadline shown;
- Revised Article upload opens;
- new version created;
- final academic check/ACC.

## 21. Publication Readiness

Final ACC:
→ READY FOR PRODUCTION.

Before production/handoff:
- metadata quality check;
- final manuscript version;
- publication destination;
- immutable publication snapshot.

Readiness result:
- READY
- WARNING
- BLOCKED

No synthetic numeric quality score.

## 22. Publication Destinations

### Proceedings
Default route:

READY FOR PRODUCTION
→ metadata validation
→ publication snapshot
→ IN PRODUCTION
→ PUBLISHED

May record:
- final PDF;
- DOI;
- pages/article number;
- publication date;
- landing URL.

### Selected Journal
Override route:

READY FOR PRODUCTION
→ SELECTED FOR JOURNAL
→ metadata validation
→ publication snapshot
→ OJS/JOURNAL HANDOFF
→ downstream journal process

May record:
- target journal;
- external submission/reference ID;
- known downstream status;
- handoff date.

Conference application must not fabricate journal acceptance.

## 23. Canonical Scholarly Metadata

Internal canonical metadata is authoritative.

Conceptual domains:
- submission;
- submission translations;
- contributors;
- contributor affiliations;
- institutions;
- keywords;
- references;
- submission files/versions;
- reviews;
- publication record/snapshot;
- external identifiers;
- handoff records.

Adapters consume canonical data for:
- OJS
- Crossref
- future JATS/other targets.

Do not mirror OJS or Crossref schemas as the internal domain model.

## 24. Awards

System:
→ collects assessment evidence
→ produces candidate pool

Committee:
→ reviews evidence
→ selects final winner
→ may choose a different candidate
→ may decide no winner if policy allows

System stores:
- candidate evidence at decision time;
- final winner;
- finalized by;
- finalized at;
- optional decision note.

No silent winner replacement after finalization.

## 25. Certificate Engine

Generic engine supports:
- Participant
- Presenter
- Reviewer
- Moderator
- Committee/Appreciation
- Community Service
- Best Article
- Best Presenter
- optional Speaker/Keynote

Lifecycle:
`ELIGIBLE != GENERATED != ISSUED`

Issued certificate:
- immutable recipient snapshot;
- configurable certificate number;
- verification token;
- QR;
- issue date/display date;
- actual generated timestamp internally.

Correction:
→ revoke/reissue or superseding version.

Public verification reveals only minimum necessary fields.

## 26. Document Center

Personal generated:
- Event Pass
- Presentation LoA
- Certificates
- optional Proceedings Publication Acceptance
- optional Journal Selection/Handoff Notice

Edition documents:
- Author Guidelines
- Abstract Template
- Full Article Template
- Presentation Template
- Guidebook
- Published Schedule

User uploads:
- Payment Proof
- Abstract file if required
- Full Article
- Revised Article
- Slides if enabled

Official edition documents are versioned and should not be silently replaced.

## 27. Public Website

Required locales:
- Indonesian
- English
- Arabic

Arabic RTL is first-class.

Primary pages:
- Home
- About
- Call for Papers
- Program
- Speakers
- Activities
- Publication
- Registration & Fees
- Important Dates
- Venue
- Downloads
- FAQ
- News
- Contact
- Past Editions
- Verify
- Login/Register

Homepage changes from conversion mode before event to archive/highlight mode after event.

## 28. Frontend Technical Direction

Candidate to be formally frozen later:

- Laravel 13 official Vue Starter Kit as application skeleton;
- Vue 3 Composition API;
- TypeScript;
- Inertia;
- Tailwind CSS;
- shadcn-vue;
- Vite;
- Vue I18n;
- Lucide Vue;
- Inertia SSR for public-facing pages where adopted.

External templates are visual references only.

No Vue Router as the application router.
No separate internal REST architecture for normal page flows.

## 29. Admin / Backoffice Experience Direction

Visual shell:
- collapsible sidebar;
- clean topbar;
- contextual search;
- badges;
- filters;
- data tables;
- sheets/dialogs;
- bulk actions;
- responsive behavior;
- permission-aware menus.

Primary admin domains:
- Overview
- Edition Configuration
- Participation Packages / Activities / Dates / Tracks / Speakers
- Registrations
- Payments
- Submissions
- Review Assignments
- Academic Decisions
- Sessions / Rooms / Scheduling
- Check-in / Attendance / Presentation
- Publication Queue
- Awards
- Certificates
- Website Content
- Users & Access
- Audit Log

Dashboard emphasizes **operational attention**, not decorative analytics.

Detailed backoffice page specification follows after ERD/authority contract is frozen.

## 30. Frontend Parallel Work Boundary

Frontend AI/developer may proceed in parallel on:
- public website shell;
- participant/presenter/reviewer experience;
- reusable component system;
- id/en/ar + RTL;
- responsive layouts;
- typed mock scenarios.

Frontend parallel work must not:
- invent backend business rules;
- define database schema;
- redefine lifecycle states;
- build separate API/auth architecture;
- build internal admin domain behavior before the corresponding backend contract is frozen.

## 31. V1 Explicit Non-Goals

Not in accelerated V1:
- automated payment gateway;
- automatic refund engine;
- OJS API submission automation;
- Crossref automatic deposit;
- mandatory live ROR integration;
- hotel booking;
- travel management;
- full MoU contract lifecycle;
- AI schedule generation;
- AI award winner;
- complex reviewer multi-round engine;
- full copyediting/layout workflow;
- drag-and-drop certificate designer;
- helpdesk/ticketing system;
- microservices.

## 32. Two-Week V1 Definition of Done

V1 is considered operational only when the following can be demonstrated end-to-end through the browser without database manipulation:

Visitor
→ Register
→ Choose Package
→ Upload Payment
→ Finance Verify
→ Event Pass
→ Submit Abstract
→ Admin Screen
→ Reviewer Review
→ Academic Accept
→ LoA
→ Upload Full Article
→ Confirm Presenter
→ Schedule
→ Publish Schedule
→ Check-in
→ Mark PRESENTED
→ Presentation Assessment
→ Revision or No Revision
→ Final ACC
→ Publication Queue
→ Committee Award Decision
→ Certificate Generation/Issue
→ Public Verification

Additionally:
- permissions are enforced server-side;
- id/en/ar base experience works;
- RTL is functional;
- mobile event-day flows are usable;
- consequential state changes are auditable;
- no critical regression remains.

## 33. Remaining Gates

This Product Blueprint does not authorize coding by itself.

Next:
1. Corrective Phase 0 Re-baseline
2. Submission & Scholarly Metadata Contract
3. Stack + ERD Freeze
4. Two-Week Development Plan
5. Coding
