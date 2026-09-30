# ICHES Conference & Event Experience Platform — Canonical Context

**ID:** ICHES-CANONICAL-001  
**Version:** 1.0-product-discovery  
**Status:** ACTIVE WORKING CONTEXT — NOT IMPLEMENTATION FROZEN  
**Updated:** 2026-09-30

## Purpose

Durable cross-session context for the ICHES / international conference platform.

A new AI/developer session must read:
- AGENTS.md
- docs/product-discovery/PRODUCT_DNA.md
- docs/ai-context/CURRENT_STATE.md
- PRD.md
- docs/product-discovery/PRE_PRESENTATION_PRODUCT_AUDIT.md
- docs/product-discovery/WAREK_I_PRODUCT_DECISION_SHEET.md
- docs/governance/DECISION_REGISTER.md

## Product identity

A reusable Conference & Event Experience Platform for recurring academic conference editions.

Core promise:
public information → registration → payment → scholarly submission → review → LoA → Full Article → scheduling → event operations → assessment/revision → publication handoff → awards/certificates.

## Product character

- Simple where possible.
- CRUD-first.
- Strong participant UX.
- Multi-edition.
- Mobile-first.
- id/en/ar with first-class Arabic RTL.
- Standards-aware scholarly metadata.
- Auditable consequential actions.
- Human academic/committee authority retained.

## Series and edition

ICHES = conference series.

ICHES 2027 or another edition = configurable edition containing:
- theme;
- host;
- organizer;
- dates;
- venue;
- packages/fees;
- activities;
- speakers;
- tracks;
- deadlines;
- documents;
- schedule;
- publication destinations;
- committee/authorities.

Do not hardcode UNISYA as permanent series owner merely because UNISYA hosts a particular edition.

## Participation lifecycle

Register
→ select participation package
→ payment
→ proof
→ Finance verification
→ Registration Confirmed
→ Event Pass / QR

Participant package presets are edition-configurable.

## Academic lifecycle

Confirmed conference participant
→ Submit Abstract
→ Administrative Check
→ Single-anonymous Review
→ Academic Decision

If Accepted:
→ Presentation LoA
→ Upload Full Article
→ Confirm Actual Presenter
→ Ready for Scheduling
→ Session/Room/Reviewer/Moderator/Slot assignment
→ Schedule Publish
→ Reviewer Pre-read
→ Presentation
→ PRESENTED / NO_SHOW
→ Presentation Assessment
→ No Revision or Revision Required
→ Final ACC
→ Ready for Production

## LoA

Presentation LoA means accepted for presentation only.

It must not be conflated with:
- room assignment;
- publication acceptance;
- certificate.

## Submission metadata

Submission language is independent from UI locale.

Contributor model supports:
- ordered contributors;
- corresponding author;
- single-name authors;
- optional ORCID;
- ROR-first affiliation;
- manual affiliation fallback;
- contributors without accounts.

References are stored as ordered scholarly metadata with raw citation preserved.

## Scheduling

Scheduling occurs only after Full Article submission.

Core objects:
- Session
- Room
- Presentation Slot
- Reviewer assignment
- Moderator assignment

Draft schedule is internal. Published schedule becomes participant truth.

## Event facts

Attendance != Presented.

QR lookup != attendance.

Moderator/Event Operations records event fact.

Reviewer records academic assessment.

## Awards

System assessment may produce candidate data.

Committee owns the final decision.

Committee may:
- accept system candidates;
- reject candidates;
- select a different winner;
- decide no winner;
- decide multiple winners if policy allows.

System records:
- candidate evidence;
- final winner;
- decision date;
- finalized by;
- optional internal note.

Current direction:
- Best Presenter: overall edition;
- Best Article: all authors receive individual certificates.

## Certificates

Generic certificate engine.

Eligibility != Generated != Issued.

Supports Participant, Presenter, Reviewer, Moderator, Committee/Appreciation, Community Service, Best Article, Best Presenter, and optional speaker types.

Presenter may receive both Participant and Presenter certificate.

Issued certificates have immutable snapshots, unique verification identities, QR, and revoke/reissue semantics.

## Documents and communication

Dashboard is source of truth.

Email is personalized notification.

Document Center includes relevant:
- Event Pass;
- LoA;
- templates;
- guidebook;
- schedule;
- certificates;
- optional publication acceptance.

## Publication

Conference platform drives paper to publication-ready state.

Proceedings:
Ready for Production → metadata validation → publication snapshot → production → published.

Selected Journal:
Ready for Production → selected for handoff → metadata validation → publication snapshot → OJS handoff → journal process.

Selected for Journal != Accepted by Journal.

Proceedings may issue Publication Acceptance after final approval. Journal acceptance remains journal authority unless formally delegated.

## Scholarly metadata

Canonical internal metadata is the source of truth.

Adapters:
- OJS
- Crossref
- future JATS/etc.

Never model the database as ojs_title/crossref_title copies.

Final publication snapshot protects historical metadata from later profile edits.

## Public frontend

Public website is edition-aware and supports:
- Home
- About
- Call for Papers
- Program
- Speakers
- Activities
- Publication
- Registration & Fees
- Venue
- Downloads
- FAQ
- News
- Contact
- Past Editions
- Verify
- Login/Register

Homepage is state-aware before and after event.

## Dashboard

Participant-facing dashboard is state-aware, not role-template-only.

Central concept: Next Action.

Show relevant menus only.

A single person may simultaneously be Participant, Presenter, Reviewer, and Committee member without separate accounts.

## Non-goals

No:
- ERP;
- hotel booking engine;
- travel management;
- contract lifecycle management;
- OJS replacement;
- microservices;
- AI winner;
- AI scheduler;
- no-code workflow builder;
- full helpdesk;
- complex refund engine.

## Governance

Current Product DNA supersedes conflicting exploratory/old lifecycle assumptions for discussion purposes.

Formal authoritative reconciliation happens only after Warek I validation during Corrective Phase 0 Re-baseline.

Implementation remains prohibited until that gate is closed.
