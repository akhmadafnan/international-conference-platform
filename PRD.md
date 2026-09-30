# Product Requirements Document — ICHES Conference & Event Experience Platform

**Document ID:** ICHES-PRD-001  
**Version:** 0.2.0-product-discovery  
**Status:** WORKING PRODUCT DISCOVERY BASELINE — NOT IMPLEMENTATION-FROZEN  
**Updated:** 2026-09-30

## 1. Product Vision

Build a reusable conference platform that joins public conference information, participant registration, payment, scholarly submission, review, scheduling, event-day operations, publication handoff, documents, awards, and certificates into one coherent participant experience.

The platform is not:
- a one-event disposable website;
- a full ERP;
- a hotel/travel management system;
- a contract-management system;
- an OJS replacement;
- an automatic academic decision engine.

## 2. Product Principles

1. Simple CRUD where CRUD is enough.
2. Smart and personal UX rather than menu-heavy administration.
3. Multi-edition from day one.
4. Conference Series and Conference Edition are separate concepts.
5. UNISYA may host an edition without being hardcoded as permanent series owner.
6. UI locales are Indonesian, English, Arabic; Arabic RTL is first-class.
7. UI language is independent from scholarly submission language.
8. Email/WhatsApp are communication channels, not sources of truth.
9. Consequential decisions are auditable.
10. Academic and award decisions remain human-authoritative.
11. Canonical scholarly metadata is the internal truth; OJS/Crossref are adapters/targets.
12. OJS remains downstream publication infrastructure.

## 3. Core Public Experience

Public website functions:
- branding;
- information;
- conversion;
- event experience.

Primary public information architecture:
- Home
- About
- Call for Papers
- Program
- Speakers
- Activities
- Publication
- Registration & Fees
- Venue / Facilities
- Downloads
- FAQ
- News
- Contact
- Past Editions
- Verify
- Login / Register

Homepage should include hero, theme/date/venue, CTA, countdown, activities, important dates, tracks, speakers, publication opportunities, fees, program, venue, partners, news, and final CTA.

## 4. Participation & Registration

Participant registers an account, verifies identity/email as configured, selects a participation package, pays, submits proof, and becomes confirmed after Finance verification.

Initial edition may expose four easy-to-understand package cards:
- Conference Only
- Conference + Evening/MoU
- Conference + Evening/MoU + International Community Service
- International Community Service Only

These are configurable edition packages, not hardcoded permanent user roles.

Event Pass + Registration ID + QR become available after payment verification and registration confirmation.

QR is an identity/lookup key; scan alone does not create attendance.

## 5. Payment

All participants pay at the beginning as a commitment gate.

V1 payment is manual bank transfer:
- amount/instructions;
- proof upload;
- sender/date/amount metadata;
- Finance verification;
- correction/re-upload with history.

Minimal statuses:
- UNPAID
- PROOF_SUBMITTED
- PAID
- CORRECTION_REQUIRED
- CANCELLED

Open domain policy: what happens when a paid participant later has an abstract rejected.

## 6. Submission

Presenter is not selected as a role during registration. A confirmed conference participant enters the presenter path by submitting an abstract.

Submission UX is OJS-inspired but simpler:
1. Details
2. Contributors
3. References
4. Files
5. Review & Submit

Details include:
- primary submission language: id/en/ar;
- track;
- title;
- optional subtitle;
- abstract;
- structured keywords.

## 7. Contributors, ROR, ORCID

Contributor fields:
- given name;
- family/surname with single-name support;
- email;
- country;
- affiliation;
- optional department/faculty;
- optional ORCID;
- corresponding author flag;
- author order.

Affiliation is ROR-first with manual fallback.

Contributors do not need application accounts.

## 8. References

References are part of canonical scholarly metadata.

V1 should preserve ordered raw citations per reference and may enrich DOI/structured metadata where available. Do not force authors to manually fill many fields per reference.

## 9. Administrative Screening & Abstract Review

Administrative screening precedes academic review and may check:
- metadata completeness;
- scope/track;
- abstract length;
- template/file conformity when enabled;
- similarity policy when enabled.

Abstract review defaults to single-anonymous.

Academic outcomes:
- ACCEPT
- REVISION
- REJECT

Reviewer recommendation is advisory; authorized Academic authority records final decision.

## 10. Acceptance & LoA

When the abstract is accepted:
- Presentation LoA becomes available;
- Upload Full Article becomes available.

Presentation LoA means accepted for presentation at the conference. It does not contain room/session/reviewer assignment and does not mean accepted for publication.

## 11. Full Article & Presenter Confirmation

Full Article is uploaded after abstract acceptance and is versioned.

After Full Article submission, the paper becomes READY FOR SCHEDULING.

The actual presenter is explicitly confirmed from the contributor list before/finalizing scheduling. Corresponding author and actual presenter may differ.

## 12. Scheduling

Only papers with Full Article submitted enter the Scheduling Pool.

Admin/authorized event-academic operators can bulk assign:
- Session
- Room
- Reviewer(s)
- Moderator
- presentation date/time/slot

Schedule has draft and published states. Participants see only published schedule.

Conflict warnings should detect obvious person/time collisions and self-review. Same-affiliation reviewer assignment may warn without automatically blocking.

## 13. Reviewer Presentation Workspace

Reviewer sees only assigned session/papers.

Reviewer can:
- pre-read assigned Full Articles;
- complete presentation assessment;
- submit revision recommendation;
- provide article score;
- provide presenter score;
- provide notes.

Moderator/Event Operations records PRESENTED / NO_SHOW. Attendance and presentation are separate facts.

## 14. Post-Presentation Revision

If no revision is required:
- no Revised Article upload appears;
- current Full Article may proceed toward Ready for Production.

If revision is required:
- reviewer notes appear;
- Revised Article upload opens;
- a new file version is stored;
- final check/ACC leads to Ready for Production.

Never overwrite previous manuscript versions.

## 15. Awards

Native award features:
- Best Article
- Best Presenter

System assessment data creates candidate evidence only.

The Academic Committee may accept, reject, replace, or disregard system candidates. Final award authority remains with the Committee. The application records the final Committee decision and audit metadata.

Current product direction:
- Best Presenter: overall edition award;
- Best Article: all listed authors receive individual award certificates;
- Best Presenter certificate: actual presenter;
- award status does not determine publication eligibility.

## 16. Certificates

One generic certificate engine supports:
- Participant
- Presenter
- Reviewer
- Moderator
- Committee/Appreciation
- Community Service
- Best Article
- Best Presenter
- optional Speaker/Keynote types

Eligibility is separate from Generated and Issued.

Current product direction:
- Presenter may receive both Participant and Presenter certificates;
- Participant certificate is attendance-based;
- Presenter certificate requires PRESENTED;
- Reviewer certificate requires completed duties;
- Committee certificates are supported;
- awards trigger award-certificate eligibility after Committee finalization.

Certificates use individual records, unique verification identities, QR verification, revoke/reissue, and immutable issued snapshots.

## 17. Documents & Communication

Generated personal artifacts:
- Event Pass
- Presentation LoA
- Certificates
- optional Publication Acceptance

Edition documents:
- Abstract Template
- Full Article Template
- Presentation Template
- Guidebook
- Author Guidelines
- published schedule/export

User uploads:
- Payment Proof
- Abstract file when required
- Full Article
- Revised Article
- Presentation Slides when edition requires them

Dashboard is the source of truth. Email is personalized notification.

## 18. Participant Dashboard

Dashboard answers:
- Where am I now?
- What must I do next?
- What documents/information are available?

Core areas:
- Overview
- My Activities
- My Submission when applicable
- My Reviews when applicable
- Payment
- Schedule
- My Documents
- Help

A strong Next Action component is central. Technical states are translated into human language.

## 19. Publication

After PRESENTED:
- Presentation Assessment
- No Revision or Revision
- Final ACC
- READY FOR PRODUCTION

Publication destination is assigned by Academic/Publication Team:
- Proceedings
- Selected Journal

Proceedings path:
Ready for Production → metadata validation → publication snapshot → production → published.

Selected Journal path:
Ready for Production → selected for journal handoff → metadata validation → publication snapshot → OJS handoff → journal process.

Selected for Journal does not mean Accepted by Journal.

Proceedings may issue Publication Acceptance after final approval/destination confirmation. Selected Journal may issue a Selection/Handoff notice; publication acceptance remains the journal's authority unless formally delegated.

## 20. Scholarly Metadata

Internal database stores canonical scholarly metadata.

Core concepts:
- submission;
- translations;
- contributors;
- contributor affiliations;
- institutions/ROR;
- keywords;
- references;
- files/versions;
- publication record/snapshot;
- external identifiers;
- handoff records.

Adapters/targets:
- OJS
- Crossref
- future JATS/other outputs

Metadata grows progressively through the lifecycle.

Before publication handoff, a quality gate reports READY / WARNING / BLOCKED rather than a synthetic score.

## 21. Publication Snapshot

Finalize for Production creates a protected scholarly metadata snapshot:
- final title/abstract/keywords;
- final author order;
- final affiliations;
- final references;
- final manuscript version;
- destination context.

Later profile edits must not rewrite historical publication metadata.

## 22. Community Service

Keep lightweight:
- groups;
- category;
- location;
- coordinator;
- members;
- attendance;
- notes/documentation.

Not a mini-KKN system.

## 23. Evening / MoU

Keep lightweight:
- schedule;
- venue;
- institution/participants;
- attendance;
- document reference/status.

Not a contract lifecycle system.

## 24. Candidate Technical Stack

Not frozen:
- Laravel 13
- Vue 3
- TypeScript
- Inertia
- Tailwind CSS
- shadcn-vue

Preferred shape: Laravel modular monolith. Do not mix Livewire + Vue + Inertia in V1.

## 25. V1 Non-Goals

Do not build in V1:
- full ERP;
- hotel booking;
- travel management;
- full MoU contract management;
- OJS replacement;
- microservices;
- AI automatic scheduling;
- AI automatic award winner;
- no-code workflow builder;
- complex refund engine;
- Canva-like certificate designer;
- full helpdesk/ticketing;
- mandatory WhatsApp automation.

## 26. Current Gate

Product Discovery is substantially mature.

Before implementation:
- Warek I domain decisions must be validated;
- Product Blueprint v1 must be approved;
- old Phase 0 requirements must undergo corrective reconciliation;
- Submission & Scholarly Metadata Contract must be frozen;
- stack and ERD must then be frozen.
