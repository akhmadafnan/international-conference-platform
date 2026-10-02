# Two-Week Development Plan — ICHES V1

**Document ID:** ICHES-PLAN-001
**Status:** ACTIVE — DAY 1 / PHASE 01 FOUNDATION
**Duration:** 10 working days / approximately 2 calendar weeks
**Updated:** 2026-10-02

## 1. Delivery target

Deliver an operational V1 that demonstrates the complete browser-based lifecycle without manual database manipulation:

Visitor
→ Register
→ Choose Package
→ Pay
→ Finance Verify
→ Event Pass
→ Submit Abstract
→ Administrative Screening
→ Reviewer Review
→ Academic Accept
→ LoA
→ Full Article
→ Confirm Presenter
→ Scheduling
→ Publish Schedule
→ Check-in
→ PRESENTED / NO_SHOW
→ Presentation Assessment
→ Revision / No Revision
→ Final ACC
→ Publication Queue
→ Committee Award Decision
→ Certificate Generation / Issue
→ Public Verification

## 2. Scope rule

For these 10 working days:

**No new feature enters V1 unless it is required for the above end-to-end path or an already-frozen security/integrity requirement.**

Everything else becomes V1.1.

## 3. Daily plan

### Day 1 — Engineering Foundation

Scope:
- official Laravel 13 Vue Starter Kit bootstrap;
- PHP/composer/npm baseline;
- MySQL 9.7 configuration;
- UUIDv7 model convention;
- edition timezone convention;
- Spatie Permission with Teams enabled and conference_edition_id scope;
- Laravel Policies baseline;
- authentication/email verification/password confirmation baseline; 2FA and passkeys are excluded from V1;
- Vue I18n id/en/ar shell;
- Arabic RTL shell;
- ICHES design-token foundation;
- database queue/cache/session;
- private/public filesystem disks;
- core CI/test/lint/static-analysis commands;
- foundational migrations/seeds.

#### Current Day 1 checkpoint

Completed:
- Laravel 13 Vue/Inertia application baseline;
- PHP/composer/npm engineering baseline;
- MySQL 9.7 local configuration;
- UUIDv7 identity convention;
- UTC application persistence timezone;
- Authentication V1;
- Spatie Permission Teams infrastructure with `conference_edition_id`;
- UUIDv7 User/Role/Permission compatibility;
- Spatie Activitylog UUID-aware audit infrastructure;
- Active Conference Edition permission context and global superadmin enforcement;
- edition-scoped authority enforcement smoke tests;
- repository runtime/cache hygiene;
- focused authorization/audit smoke tests;
- Pint/PHPStan/regression gates.

Remaining before Day 1 / Phase 01 closure:
- concrete domain Policies/Gates as real domain models are introduced;
- Vue I18n id/en/ar shell;
- Arabic RTL shell;
- ICHES design-token foundation;
- private/public filesystem foundation;
- foundational seeders.

Exit gate:
- app boots;
- auth works;
- MySQL migrations GREEN;
- edition-scoped authority smoke test GREEN;
- id/en/ar + RTL shell visible;
- test/Pint/static-analysis baseline runs;
- worktree clean after checkpoint.

### Day 2 — Conference Configuration + Registration + Payment

Scope:
- Series / Edition;
- activities;
- participation packages + entitlements;
- tracks / dates / venue basic data;
- membership;
- participant registration;
- package/fee snapshot;
- manual payment;
- proof versioning;
- Finance verification/correction;
- registration confirmation;
- Event Pass record + QR lookup;
- participant dashboard initial Next Action states.

Exit gate:
Register → Package → Payment Proof → Finance Verify → Registration Confirmed → Event Pass works through browser.

### Day 3 — Canonical Submission + Metadata

Scope:
- Submission/Paper ID;
- translations;
- keywords;
- submission contributors;
- institution-first ROR/manual affiliation structure;
- optional subdivision;
- ORCID validation semantics;
- references;
- stored files + abstract file policy;
- submission snapshots;
- metadata readiness;
- five-step abstract wizard;
- draft/submit state.

Exit gate:
Confirmed participant can create and officially submit a metadata-valid abstract without bypassing rules.

### Day 4 — Screening + Review + Academic Decision + LoA

Scope:
- administrative screening;
- correction required;
- reviewer assignment;
- self-review block / affiliation warning;
- single-anonymous reviewer workspace;
- review report;
- one normal abstract revision cycle;
- Academic final decision;
- accepted/rejected behavior;
- Presentation LoA generation;
- personalized notification hooks.

Exit gate:
Abstract → Screening → Review → Academic Decision → LoA is GREEN.
Rejected paper leaves participant registration active.

### Day 5 — Full Article + Presenter + Scheduling

Scope:
- Full Article upload/version 1;
- actual presenter confirmation from contributor list;
- rooms;
- sessions;
- presentation slots;
- bulk reviewer assignment to scheduled papers;
- moderator assignment;
- conflict warnings;
- schedule draft;
- Publish Schedule;
- participant personal schedule.

Exit gate:
Accepted paper → Full Article → Presenter → Scheduled → Published Schedule works.

### Day 6 — Event-Day Operations + Presentation Assessment

Scope:
- QR lookup/check-in;
- registration activity attendance;
- community-service lightweight groups where required;
- room/session operational screen;
- PRESENTED / NO_SHOW;
- reviewer presentation assignment;
- Full Article pre-read access;
- presentation assessment;
- article/presenter scoring evidence;
- revision recommendation.

Exit gate:
Event operator and reviewer can complete Day-H flow with server-side scoped authorization.

### Day 7 — Revision + Final ACC + Publication

Scope:
- Revised Article versioning;
- No Revision path;
- Final Academic Approval;
- publication readiness READY/WARNING/BLOCKED;
- publication outlets;
- Proceedings default;
- Selected Journal override;
- immutable publication snapshot;
- external identifier baseline;
- OJS handoff/export preview;
- Crossref readiness preview;
- canonical JSON metadata export.

Exit gate:
PRESENTED paper can reach READY FOR PRODUCTION with immutable canonical snapshot and truthful destination state.

### Day 8 — Awards + Certificates + Documents + Public Content Binding

Scope:
- Best Article / Best Presenter award programs;
- candidate evidence;
- Committee finalization independent from ranking;
- award recipients;
- certificate templates;
- eligibility records;
- bulk Generate → Preview → Issue;
- revoke/reissue/supersede;
- Event Pass / LoA / publication-document center;
- public verification;
- edition documents;
- news / FAQ / speakers / partners / public website data binding;
- participant My Documents.

Exit gate:
Committee Decision → Award → Certificate → Verification works.
Document Center reflects issued artifacts.

### Day 9 — Frontend Integration + Full Regression

No new feature scope.

Scope:
- merge/integrate parallel public/participant/reviewer frontend;
- replace mock adapters with typed Inertia props;
- backoffice shell integration;
- full lifecycle regression;
- permission/COI audit;
- private-file access regression;
- id/en/ar regression;
- RTL regression;
- responsive/mobile event-day UAT;
- accessibility pass;
- browser compatibility smoke;
- bug fixing only.

Exit gate:
full regression GREEN or bounded known non-critical defects with explicit disposition.
No RED auth/privacy/lifecycle defect.

### Day 10 — Release Candidate + Deployment/UAT

No new feature scope.

Scope:
- production/staging bootstrap;
- environment/secrets;
- DB migration from clean database;
- seed current Edition;
- queue worker;
- scheduler;
- PDF/Chromium smoke;
- mail smoke;
- file permissions/storage;
- HTTPS/config cache;
- backup/restore smoke;
- complete browser end-to-end UAT;
- release notes;
- known limitations;
- operational handoff;
- final checkpoint/tag candidate.

Exit gate:
**V1 RELEASE CANDIDATE GREEN**

## 4. Parallel frontend workstream

Frontend work begins **after Day 1 foundation is merged** so it uses the actual Laravel/Inertia/Vue skeleton.

Preferred isolated branch:
- feat/frontend-experience-v1

Frontend AI scope:
- public website pages;
- participant/presenter dashboard;
- submission wizard UI;
- schedule;
- documents;
- reviewer-facing workspace;
- id/en/ar;
- RTL;
- responsive/mobile;
- reusable components;
- mock scenario fixtures.

Frontend AI must not:
- change business states;
- change migrations/ERD;
- add Vue Router;
- create a separate REST client architecture;
- change auth;
- implement independent server business logic.

Day 9 is the hard integration point.

## 5. Backoffice strategy

Do not create every admin screen as a bespoke design.

Use one consistent shell:
- permission-aware sidebar;
- topbar/search;
- filters;
- DataTable;
- Sheet/Dialog detail;
- bulk actions;
- StatusBadge;
- Action Required cards.

Operational dashboard answers:
**What requires attention now?**

Avoid decorative SaaS analytics.

## 6. Branch/checkpoint strategy

Integration branch:
- develop

Each bounded implementation batch uses a scoped branch.

Recommended:
- phase/01-foundation
- phase/02-registration-payment
- phase/03-submission-metadata
- phase/04-review-loa
- phase/05-scheduling
- phase/06-event-assessment
- phase/07-publication
- phase/08-awards-certificates
- phase/09-integration-regression
- phase/10-release-candidate

Never run simultaneous dirty work on develop.

Every branch:
1. orient from canonical docs;
2. implement exact scope;
3. focused tests;
4. related regression;
5. Pint/static checks;
6. browser UAT when applicable;
7. git diff/check;
8. exact-scope commit;
9. PR to develop;
10. merge only GREEN.

## 7. Test gates

Every business state transition gets feature coverage.

Mandatory high-risk coverage:
- payment cannot self-verify;
- rejected abstract does not cancel registration;
- author cannot see reviewer identity;
- reviewer cannot access unassigned paper;
- reviewer cannot self-review;
- LoA only after accepted decision;
- Full Article required before scheduling;
- draft schedule not public;
- attendance does not imply PRESENTED;
- PRESENTED/NO_SHOW authority scoped;
- revision does not overwrite prior file;
- publication snapshot immutable;
- journal selection does not become journal acceptance;
- award ranking does not auto-finalize winner;
- certificate issuance does not fabricate underlying facts;
- revoked document verifies as revoked;
- private files cannot be accessed by unauthorized users.

## 8. Definition of Done

Release candidate requires:
- end-to-end lifecycle works through browser;
- server-side authorization enforced;
- migrations run clean from zero;
- tests GREEN;
- no critical static-analysis error in project scope;
- no critical browser UAT defect;
- id/en/ar base flows work;
- Arabic RTL works;
- mobile QR/check-in/schedule flow usable;
- PDF generation works;
- mail/queue works;
- backup/restore smoke performed;
- Git develop clean and synchronized;
- canonical docs updated.

## 9. Explicit V1.1 deferrals

- payment gateway;
- automated refund engine;
- live ROR dependency;
- ORCID OAuth;
- OJS API push;
- Crossref automatic deposit;
- Redis;
- external search engine;
- AI scheduling;
- AI award selection;
- complex multi-round review;
- full copyediting/layout workflow;
- hotel/travel;
- MoU contract management;
- WhatsApp automation;
- drag-and-drop certificate designer;
- advanced analytics.

## 10. Schedule risk policy

Days 9–10 are protected stabilization days.

If Days 1–8 slip:
- remove deferred polish first;
- never remove authorization/privacy/integrity gates;
- never replace tests with manual assumptions;
- never add new features to compensate for schedule pressure.

The fastest acceptable delivery is a smaller **GREEN** V1, not a larger RED V1.
