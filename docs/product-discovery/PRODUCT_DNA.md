# ICHES Product DNA

**Status:** WORKING PRODUCT DISCOVERY DNA  
**Updated:** 2026-09-30

## Product statement

ICHES is designed as a Conference & Event Experience Platform: simple enough for a roughly 100-participant edition, but structured for recurring editions, scholarly metadata quality, event-day usefulness, and clean publication handoff.

## Experience promise

A participant should always understand:
- where they are in the lifecycle;
- what they must do next;
- what documents/information are available.

A committee member should see:
- what requires operational attention;
- what evidence supports a decision;
- what authority they do or do not hold.

## End-to-end participant path

Public Website
→ Register
→ Select Participation
→ Pay
→ Finance Verify
→ Registration Confirmed
→ Event Pass / QR

If presenting:
→ Submit Abstract
→ Administrative Check
→ Single-anonymous Review
→ Academic Decision
→ Accepted
→ Presentation LoA
→ Upload Full Article
→ Confirm Presenter
→ Scheduling Pool
→ Session / Room / Reviewer / Moderator / Slot
→ Schedule Published
→ Presentation
→ Assessment
→ Revision or No Revision
→ Final ACC
→ Ready for Production
→ Proceedings or Selected Journal Handoff

Parallel:
→ Attendance
→ Awards
→ Certificates
→ Document Center

## Participation model

Public frontend may show four simple package cards for the current edition. Backend treats them as configurable edition packages/entitlements, not permanent roles.

## Payment model

Everyone pays first.

Purpose:
- commitment;
- operational certainty;
- registration confirmation.

V1 uses manual transfer + proof + Finance verification.

The rejected-abstract policy remains a Warek domain decision.

## Scholarly submission

OJS-inspired, simpler:
- Details
- Contributors
- References
- Files
- Review & Submit

ORCID is optional.
ROR is preferred but has manual fallback.
References are structured as individual ordered citations with raw text preserved.

## Review

Administrative screening first.

Abstract review defaults to single-anonymous.

Accept / Revision / Reject.

Reviewer advice does not automatically decide; Academic authority decides.

## Acceptance

LoA means accepted for presentation.

It does not include room information and does not mean publication acceptance.

LoA unlocks Full Article upload.

## Scheduling

Scheduling starts only after Full Article submission.

Actual presenter is explicitly confirmed.

Admin performs bulk assignment to session/room/reviewer/moderator/slot.

Reviewer sees only assigned papers.

## Day-H

Participant QR supports fast lookup/check-in.

Attendance is per activity.

Attendance != Presented.

Moderator/Event Operations records PRESENTED/NO_SHOW.

Reviewer performs academic assessment.

## Assessment and revision

One reviewer form can capture:
- revision recommendation;
- article score;
- presenter score;
- notes.

No Revision:
current Full Article proceeds; no redundant upload.

Revision Required:
new Revised Article version appears; previous files remain.

## Awards

Best Article and Best Presenter are native.

Application calculates/organizes evidence and candidate data.

Committee makes the final decision.

Committee may disregard system ranking.

Application records the final Committee decision.

Best Article:
all authors receive individual certificates.

Best Presenter:
actual presenter receives the award; current direction is one overall edition award.

## Certificates

Generic engine.

Eligibility != Generated != Issued.

Presenter may receive both Participant and Presenter certificates.

Committee certificates are supported.

Every issued certificate has:
- immutable recipient snapshot;
- certificate number;
- unique verification token;
- QR;
- revoke/reissue semantics.

## Documents

Personal generated:
- Event Pass
- Presentation LoA
- Certificates
- optional Publication Acceptance

Edition documents:
- templates
- guidebook
- guidelines
- schedule/export

User uploads:
- payment proof
- manuscript versions
- slides if required

Dashboard is canonical; email is notification.

## Publication

Conference platform is publication-ready infrastructure, not OJS replacement.

Proceedings and Selected Journal are distinct destinations.

Selected for Journal is not Journal Acceptance.

Before handoff:
- metadata quality gate;
- final manuscript;
- publication snapshot;
- provenance.

Canonical metadata exports/adapts to OJS and Crossref.

## Public website

Three languages: id/en/ar.

Arabic RTL is first-class.

Core IA:
Home, About, Call for Papers, Program, Speakers, Activities, Publication, Registration & Fees, Venue, Downloads, FAQ, News, Contact, Past Editions, Verify, Login/Register.

## Dashboard

State-aware and personal.

Main concept: Next Action.

Menus appear only if relevant.

One account may simultaneously hold participant, presenter, reviewer, moderator/committee functions.

## Technical direction

Candidate only:
Laravel + Vue + TypeScript + Inertia + Tailwind + shadcn-vue.

Do not freeze before Product Blueprint and metadata contract.

## V1 discipline

Prefer:
- CRUD;
- configuration;
- bulk actions;
- audit;
- conditional UI.

Avoid:
- ERP behavior;
- microservices;
- AI decision authority;
- hotel/travel engines;
- full OJS workflow;
- no-code workflow builder;
- complex rule engines.
