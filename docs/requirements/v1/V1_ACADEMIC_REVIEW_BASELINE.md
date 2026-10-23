# V1 Academic Review & Decision Baseline

**ID:** ICHES-V1-ACA-001  
**Status:** AUTHORITATIVE V1 BASELINE  
**Updated:** 2026-10-23

> **ICHES 2027 scoped supersession:** The [Product Owner-approved operational rebaseline](../../governance/ICHES_2027_OPERATIONAL_REBASELINE_2026_10_23.md) replaces mandatory per-paper Administrative Screening PASS and reviewer-email assignment with technically valid submission routing and exclusive reviewer Self-Claim. PD-017 and PD-062 are reconciled in the [Decision Register](../../governance/DECISION_REGISTER.md). This is approved design; existing code has not yet implemented Self-Claim/Room Mapping. Nonconflicting review, confidentiality and integrity rules remain binding.

## ICHES 2027 Topic Pool and Self-Claim

- One Edition-configured primary Topic is required for official abstract submission.
- A technically valid SUBMITTED abstract automatically enters its Topic-based Review Pool; no routine Administrative PASS operation is required.
- Reviewer views available abstracts in relevant Topics, selects an unclaimed paper, declares COI and claims it directly.
- Claim must be atomic: one exclusive active reviewer per submission per academic stage. Concurrent claims cannot produce duplicate owners; another reviewer cannot review an already-claimed abstract.
- Reviewer saves draft reports and submits a final advisory recommendation only for current stage-specific assignments.
- Topic interest/competency is distinct from authorization. Use one Reviewer role/function, not separate Spatie roles per Topic; no global confidential-assignment or unrelated private-file access.
- Only authorized Academic Committee may release/reassign an abstract claim, with audit. Technical/scope exceptions require recorded reasons and do not become an ordinary mandatory gate.

## Historical screening rationale — superseded for the 2027 ordinary flow

```text
ABSTRACT_SUBMITTED
→ ADMINISTRATIVE_SCREENING
→ PASS | CORRECTION_REQUIRED | INELIGIBLE
```

May check metadata completeness, scope/track, abstract length, file/template rules if enabled, and similarity policy if enabled.

Administrative correction is not academic revision.

## Abstract review

For ICHES 2027:
- single-anonymous;
- one exclusive active responsible reviewer per paper/stage;
- reviewer sees author identity;
- author does not see reviewer identity.

The earlier configurable reviewer-count default (PD-062) is retained as historical/reusable context outside this designated 2027 scope. Multiple reviewers in one room must have partitioned/explicit paper allocations, not overlapping responsibility on the same paper/stage.

Reviewer recommendation:
- ACCEPT
- REVISION
- REJECT

Reviewer recommendation is advisory. Academic Authority records the final decision.

## Rolling review and official release

Review runs progressively from the beginning of the 1–20 December 2026 submission window. Internal decisions may be prepared early. Author-facing official results and Presentation LoA availability follow the 22 December 2026 announcement/release policy; unreleased decisions must not leak through author pages, notifications or document availability. This baseline does not invent a release hour. LoA is presentation acceptance, not proceedings/journal acceptance.

## Abstract revision

Normal V1:
- maximum one ordinary revision cycle;
- revised abstract is versioned;
- authorized Academic exception may reopen or allow another correction if required.

All controlled corrections/revisions retain the same Submission ID and immutable snapshots/history. They do not reopen unrestricted DRAFT editing or permit a second submission. One abstract per submitting owner per Edition does not prohibit co-authorship on another owner's paper.

## Conflict of interest

- reviewer cannot review own submission;
- direct self-conflict is blocked;
- same-affiliation may warn;
- assignment/review history is auditable.

## Full Paper and SC conference-stage reassignment

Accepted Author uploads private versioned Full Papers on the same Submission ID during 23–28 December. Post-ACCEPT presenter payment uses its separate 24–28 December window; the later payment opening must not block Full Paper upload on 23 December. Deep Full Paper review is not required before presentation.

SC determines Room/Topic/Session/Presenter/Paper/Reviewer/schedule mapping; authorized Academic/Event operators enter/import it with preview-before-apply and bulk assignment/reassignment. No new SC role is required solely for policy-making. Default continuity retains the abstract reviewer unless SC reassigns the Full Paper/presentation stage.

Reviewer opens My Rooms, sees only assigned sessions/private papers in presentation order and records observations in physical or authorized online participation. Each paper/stage has one active responsible reviewer. Preserve earlier abstract reviews and decisions when reassigned. Minimum audit: actor, time, old reviewer, new reviewer, paper/stage, reason/source of SC instruction. Access follows current assignments, not room membership as blanket access to every manuscript.

## Publication-stage academic process

Accelerated V1 does **not** implement a separate generic double-anonymous publication-review engine.

After presentation:
- reviewer completes deep Full Paper and presentation/article assessment;
- advisory outcomes are `NO_REVISION_REQUIRED`, `MINOR_REVISION_REQUIRED`, `MAJOR_REVISION_REQUIRED`, or `NOT_RECOMMENDED`;
- no revision requires no unnecessary new upload;
- if revision is required, Revised Article creates a new manuscript version;
- Academic/Publication Authority performs final check/ACC and validates the final approved manuscript;
- Final ACC leads to READY FOR PRODUCTION.

Revision outcome and publication destination are separate facts. Reviewer may recommend a destination/candidate journal; SC / authorized Academic or Publication Authority determines routing. Attendance, PRESENTED/NO_SHOW, review recommendation and final academic decision remain distinct. Final ACC/readiness precede downstream handoff; Selected Journal does not mean Accepted by Journal.

This replaces the earlier default of a separate post-presentation double-anonymous publication review for V1.
