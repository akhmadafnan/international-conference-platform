# Permission Model — Part 5 Academic Review & Decision

**ID:** ICP-REQ-PERM-001-P5  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-23

## Separation of academic responsibilities

```text
ACADEMIC COMMITTEE
→ review-process management

REVIEWER
→ assignment-scoped evaluation

ACADEMIC DECISION AUTHORITY
→ final authoritative academic decision
```

These responsibilities may be held by the same person only where separately assigned and not conflicted.

## Academic Committee

May:
- perform assigned academic/eligibility screening;
- select/invite reviewers;
- create/cancel/reassign reviewer assignments;
- manage review rounds and deadlines;
- monitor progress;
- send reminders;
- perform COI screening;
- replace reviewers;
- view relevant reviewer identity;
- view reviewer recommendations;
- view author-facing review comments;
- view confidential editor comments;
- view assignment/COI history.

Does not automatically make final academic decisions.

## Reviewer scope

Reviewer permission is restricted to:
- assigned submission;
- assigned review stage;
- assigned round;
- assigned manuscript version;
- assignment-specific task/form.

No general browse access to all submissions.

## Reviewer actions

May:
- accept/decline invitation;
- declare COI;
- access assigned review packet;
- draft review;
- submit author-facing comments;
- submit confidential editor comments where enabled;
- submit recommendation;
- view own submitted review.

May not:
- assign other reviewers;
- modify author manuscript/metadata;
- access unrelated submissions;
- access raw Finance data;
- make final academic decision.

## Assigned manuscript version

Access is version-specific.

A revised manuscript is not automatically exposed until an authorized assignment/round grants access.

## COI

Reviewer declaration and Committee screening are supported.

Typical flow:
```text
ASSIGNED
→ COI DECLARATION / SCREENING
→ ACCEPT
   or
→ DECLINE/BLOCK/CANCEL
→ REASSIGN
```

COI/restriction overrides reviewer/decision permissions.

## Single-anonymous

```text
Reviewer sees Author identity
Author does not see Reviewer identity
```

Academic administration may see Reviewer identity as needed.

## Double-anonymous

Reviewer packet excludes identity-bearing Author information.

Reviewer must not access:
- Author/contributor names;
- affiliation;
- email;
- account/profile;
- ORCID;
- identity-bearing original files;
- other metadata/resources that reveal identity.

Author does not see Reviewer identity.

## Mixed anonymity

Different assignments on one submission may use different anonymity modes.

Visibility is assignment-specific.

An identity-visible assignment must not cause identity leakage into a double-anonymous assignment.

## Reviewer-to-reviewer visibility

Default:
```text
Reviewer A
→ cannot see Reviewer B report/identity
```

Academic Committee/Decision Authority may access the set of relevant reports.

Future policy may allow limited post-completion peer-review visibility; not default V1.

## Recommendation vs decision

Reviewer recommendation is advisory.

```text
Reviewer recommendations
→ Academic Decision Authority
→ final decision
```

No automatic majority-vote decision.

## Academic Decision Authority

May record final decisions for the applicable stage.

### Abstract review
- ACCEPTED;
- REVISION_REQUIRED;
- REJECTED.

### Publication review
- REVISION_REQUIRED;
- PUBLICATION_APPROVED;
- PUBLICATION_REJECTED.

May access relevant:
- manuscript/submission;
- version history;
- assignments;
- reviewer identity;
- recommendations;
- author-facing comments;
- confidential comments;
- COI;
- academic history.

Does not automatically receive:
- Finance authority;
- presentation verification;
- OJS transfer/publication operations.

## Stage-specific authority

Different review stages may designate different final authorities.

The data/permission design must not hardcode one universal decision authority.

## Divergent/exceptional decision

Where final decision diverges materially from normal reviewer synthesis or uses an override/exception path, an auditable rationale is required according to edition policy.

## Review lock

```text
DRAFT
→ Reviewer may edit

SUBMITTED
→ locked
```

Reopening requires authorized action and audit.

## Assignment history

Assignments are retained through states such as:
- invited;
- accepted;
- declined;
- cancelled;
- overdue;
- completed;
- reassigned/superseded.

Exact physical enum is deferred.

Historical assignments are not silently deleted.

## Confidential comments

Two channels:
- AUTHOR_FACING;
- CONFIDENTIAL_EDITOR.

Confidential editor content must never leak through author-facing:
- UI;
- API;
- export;
- email;
- decision letter;
- generated document.

## Identity leakage prevention

Anonymous-review protection includes technical surfaces:
- file/download authorization;
- file names;
- URLs;
- document metadata;
- exports;
- notifications;
- email templates;
- author-visible logs/history.

## Multi-role conflict

Academic Committee may separately act as Reviewer.

Reviewer context still follows assignment-specific anonymity.

Academic Decision Authority who is also an Author/Contributor or otherwise conflicted on a submission cannot decide that submission.

## Decision correction

Final decision correction uses controlled supersession.

Must preserve:
- previous decision;
- replacement decision;
- reason;
- authority;
- timestamp;
- relevant reference/evidence.

## Summary matrix

| Action | Academic Committee | Reviewer | Decision Authority |
|---|---|---|---|
| View relevant submissions | Allow | Assigned only | Allow |
| Assign/replace reviewer | Allow | Deny | Policy-dependent |
| COI screening | Allow | Declare own | Allow |
| View reviewer identity | Allow | Self only | Allow |
| View all relevant review reports | Allow | Own only default | Allow |
| View confidential comments | Allow | Own authored content | Allow |
| Submit review | Only with Reviewer assignment | Allow | Only with Reviewer assignment |
| Final academic decision | Deny default | Deny | Allow |
| Edit submitted review directly | Deny | Deny | Deny |
| Controlled reopen review | Allow/authorized | No | Policy-dependent |
| Decide own/conflicted paper | Deny | Deny | Deny |
| Raw Finance data | Deny | Deny | Deny |

## Next

Part 6 defines Event Operations, Session Chair, and Moderator permissions.
