# Warek I — Product Decision Sheet

**Status:** DRAFT FOR PRESENTATION / VALIDATION  
**Purpose:** Confirm business/domain policy only. Technical stack and database design are intentionally excluded.

## Product in one sentence

A reusable ICHES platform that manages participant registration, payment, academic submission/review, LoA, Full Article, scheduling, event-day operations, publication handoff, awards, documents, and certificates in one state-aware experience.

## Core lifecycle for validation

Register
→ Select Package
→ Pay
→ Registration Confirmed
→ Submit Abstract
→ Review
→ Accepted
→ LoA
→ Full Article
→ Scheduling
→ Presentation
→ Assessment
→ Revision / No Revision
→ Final ACC
→ Proceedings / Selected Journal
→ Certificates / Awards

## Decisions requiring Warek I confirmation

### D-01 — Paid participant whose abstract is rejected
Recommended operational model:
payment is for event participation. Rejected paper ends presenter path, but participant registration remains active.

Alternative:
full/partial refund according to policy.

Decision: __________

### D-02 — Participation packages
Recommended:
fixed edition-configurable packages visible as simple cards.

Alternative:
participants may customize activities/facilities after choosing a preset.

Decision: __________

### D-03 — Fee matrix
Need confirmation whether fees vary by:
- package only;
- student/general;
- domestic/international;
- other institutional categories.

Decision: __________

### D-04 — Abstract reviewer count
Review model remains single-anonymous.

Need default reviewer count per abstract:
1 / 2 / other.

Decision: __________

### D-05 — Abstract revision cycle
Recommended V1:
one correction/revision cycle before final Accept/Reject, unless Academic authority grants exception.

Decision: __________

### D-06 — Presentation slides
Should PPT/slides upload be:
required / optional / not used?

Decision: __________

### D-07 — Event delivery mode
Current edition:
offline / online / hybrid?

Decision: __________

### D-08 — Actual presenter
Recommended:
after acceptance/Full Article, corresponding author confirms one actual presenter from contributor list before scheduling freeze.

Decision: __________

### D-09 — Best Presenter
Current recommended product direction:
one Best Presenter Overall for the edition.

System provides scores/candidate evidence; Committee makes final decision and may disregard ranking.

Decision: __________

### D-10 — Best Article
Current recommended product direction:
winning paper is finalized by Committee and every listed author receives an individual Best Article certificate.

Decision: __________

### D-11 — Presenter certificates
Current recommended direction:
presenter can receive both Participant Certificate and Presenter Certificate if attendance/presentation requirements are fulfilled.

Decision: __________

### D-12 — Committee certificates
Current recommended direction:
committee/appreciation certificates are supported and issued in bulk from final committee roster.

Decision: __________

### D-13 — Publication destination
Recommended:
Proceedings as default destination for final-approved papers, with selected papers optionally assigned to partner journals.

Alternative:
Publication Team assigns each paper individually.

Decision: __________

### D-14 — Publication documents
Recommended:
- Presentation LoA after abstract acceptance;
- Proceedings Publication Acceptance after final approval if needed;
- Selected Journal uses Selection/Handoff notice until the journal itself formally accepts.

Decision: __________

### D-15 — Application authority
Need named/structural authority for:
- Finance verification;
- Academic final decision;
- schedule publication;
- award finalization;
- publication destination;
- certificate issuance.

Decision: __________

## Items intentionally NOT asked of Warek

Technical implementation choices such as:
- Laravel/Vue;
- database;
- ERD;
- queues;
- storage provider;
- deployment topology.

Those are architecture decisions after business validation.

## Outcome

After Warek I validation:
→ Product Blueprint v1
→ Corrective Phase 0 Re-baseline
→ Submission & Scholarly Metadata Contract
→ Stack + ERD Freeze
→ Development Plan
→ Coding
