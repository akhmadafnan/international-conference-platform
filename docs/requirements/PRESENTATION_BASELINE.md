# Presentation & Event Operations Baseline

**ID:** ICP-REQ-PRES-001  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-23

## Acceptance meaning

```text
ABSTRACT_ACCEPTED
= accepted for conference presentation
≠ accepted for publication
```

LoA must reflect this distinction.

## Full Paper

```text
ACCEPTED
→ FULL_PAPER_PENDING
→ FULL_PAPER_SUBMITTED
→ VALIDATION
→ VALID / ACTION_REQUIRED
```

- deadline is edition-configurable;
- validation is administrative/format-oriented before the conference;
- corrections create new versions;
- later publication review is a separate stage.

## Presenter

- presenter is explicitly designated;
- corresponding author is not automatically presenter;
- presenter normally comes from contributor list;
- exceptional non-author presenter requires approval;
- presenter must confirm;
- changes are auditable.

## Scheduling

```text
SESSION
└── PRESENTATION SLOT
    ├── submission
    ├── presenter
    ├── start/end
    ├── order
    └── room/track context
```

Schedule states:
- DRAFT;
- PUBLISHED.

Published changes are traceable.

## Attendance vs presentation

Separate concepts:

```text
ATTENDANCE STATUS
PRESENTATION STATUS
```

V1 may use:
- manual check-in;
- QR/barcode when available later.

Presentation completion is verified by authorized Event Operations / Session Chair / Moderator / equivalent.

## No-show

Default:

```text
NO_SHOW
→ PUBLICATION_BLOCKED
```

Exception:
- makeup presentation;
- approved waiver;
- other edition-defined exceptional outcome.

Exception must record authority, reason, and evidence/reference.

## Certificate governance

Manual/ad-hoc certificate issuance is supported for legitimate operational needs.

It does **not** permit false role claims.

Allowed pattern:

```text
Authorized Manual Issuance
→ choose truthful certificate type
→ recipient
→ reason
→ issuing authority
→ optional evidence/reference
→ generated certificate
→ audit history
```

Examples:
- Participant;
- Committee;
- Reviewer;
- Session Chair/Moderator;
- Supporting Contributor;
- Guest;
- Speaker/Keynote;
- edition-defined recognition certificate.

Presenter Certificate requires:
- PRESENTED status; or
- a documented qualifying presentation exception/makeup outcome.

Manual certificate issuance never silently modifies the underlying attendance/presentation record.
