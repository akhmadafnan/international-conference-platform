# Permission Model — Part 6 Event Operations & Session Roles

**ID:** ICP-REQ-PERM-001-P6  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-23

## Scope model

```text
EVENT OPERATIONS
→ EDITION scope

SESSION CHAIR
→ SESSION scope

MODERATOR
→ SESSION scope
```

## Event Operations

May manage:
- sessions;
- rooms;
- presentation slots;
- presenter readiness;
- attendance/check-in;
- presentation verification;
- no-show;
- rescheduling;
- operational notes.

May view only information needed for event operations.

Derived readiness/eligibility indicators may be exposed without exposing underlying Finance/review-sensitive data.

## Data boundary

Event roles do not receive by default:
- payment proof;
- bank/reconciliation data;
- reviewer identity;
- reviewer reports;
- confidential academic deliberation;
- publication-review confidential notes.

## Scheduling

Authorized Event Operations may:
- create/edit sessions;
- create/edit slots;
- assign eligible presentations;
- change room/date/time;
- publish schedule;
- revise/unpublish schedule where policy permits.

Published schedule changes must record:
- changed_by;
- changed_at;
- previous value;
- new value;
- reason where required.

## Session Chair

May access assigned session and related operational data.

May verify:
- PRESENTED;
- NO_SHOW;

for presentations inside assigned session.

No authority over unrelated sessions unless separately assigned.

## Moderator

May access assigned session, sequence, timing, presenter details needed for moderation, and operational notes.

Presentation-verification permission is configurable by edition/session policy.

## Attendance vs presentation

```text
CHECKED_IN
≠
ATTENDED
≠
PRESENTED
```

Exact physical states are deferred, but conceptual separation is mandatory.

Participant self/QR check-in does not mean PRESENTED.

## Presentation verification

Authorized verifier may include:
- Event Operations;
- Session Chair for assigned session;
- Moderator if policy permits;
- another explicitly authorized event verifier.

Audit:
- presentation;
- verifier;
- resulting status;
- verified_at;
- session;
- note/reason;
- optional evidence/reference.

## No-show

Authorized event actor records factual NO_SHOW.

Downstream rules may derive PUBLICATION_BLOCKED.

Event roles do not make the academic publication decision.

## Presentation exception / makeup

Normal verification and exception approval are separate permissions.

```text
presentation.verify
≠
presentation.exception.approve
```

Exception/makeup authority is finalized in Permission Part 9.

## Presenter substitution

Presenter changes preserve history.

Exceptional non-author presenter uses approved exception/authorization path.

Post-event historical presenter correction is controlled/audited.

## Rescheduling

Event Operations owns authoritative schedule mutation.

Session Chair/Moderator may report/request changes and record delays, but do not automatically control the full edition schedule.

## Operational notes

May include participant-visible and restricted internal notes.

Must not become a backdoor repository for Finance/reviewer-confidential data.

## Evidence

Evidence is policy-driven.

May include:
- authorized verifier;
- attendance log;
- operational note;
- optional image/reference;
- optional Zoom/virtual-platform reference.

Universal photo/video evidence is not required by V1 baseline.

## Self-conflict

Normal rule:
```text
Event verifier
+ own presentation
→ verify PRESENTED/NO_SHOW DENIED
```

Another authorized verifier or controlled exceptional path is required.

## Certificate boundary

Event Operations records factual attendance/presentation.

Certificate issuance is handled by certificate-domain authority/rules and consumes the event state.

## Summary matrix

| Action | Event Operations | Session Chair | Moderator |
|---|---|---|---|
| View edition schedule | Allow | Assigned session | Assigned session |
| Create/edit sessions | Allow | Deny default | Deny |
| Assign presentation slots | Allow | Deny default | Deny |
| Publish schedule | Authorized | Deny | Deny |
| Check-in/attendance | Allow | Session relevant | Session relevant |
| Mark PRESENTED | Allow | Assigned session | Policy-dependent |
| Mark NO_SHOW | Allow | Assigned session | Policy-dependent |
| Verify own presentation | Deny | Deny | Deny |
| Approve exception/makeup | Special permission | Deny default | Deny default |
| Reschedule | Allow | Request/report | Request/report |
| Reviewer/confidential academic data | Deny | Deny | Deny |
| Raw Finance data | Deny | Deny | Deny |
| Academic decision | Deny | Deny | Deny |
| Publication approval | Deny | Deny | Deny |

## Next

Part 7 defines Publication Team / Proceeding Editor / OJS handoff permissions.
