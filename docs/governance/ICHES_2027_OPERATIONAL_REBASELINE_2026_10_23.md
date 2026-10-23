# ICHES 2027 Operational Rebaseline

**Status:** PRODUCT_OWNER_APPROVED — AUTHORITATIVE OPERATIONAL REBASELINE
**Date:** 2026-10-23
**Authority:** Product Owner-approved operational model supplied on 23 October 2026.

This document supersedes conflicting earlier requirements for the designated ICHES 2027 academic, review, scheduling and publication scope. Nonconflicting reusable product, security, metadata and financial invariants remain binding. Conference Series and Conference Edition remain separate domains; 2027 dates, Topics and operating choices are Edition configuration, not universal obligations for future Editions.

Approval of this model is not implementation evidence or an automatic coding, merge, push, deployment or destructive database authorization. See the [current checkpoint](../ai-context/CURRENT_STATE.md), [accepted decisions](DECISION_REGISTER.md) and [bounded execution roadmap](../project-management/ICHES_2027_EXECUTION_ROADMAP.md). Existing Academic Core still uses mandatory screening and reviewer-email assignment; Self-Claim and Room Mapping are approved design, not implemented features.

## 1. Official Conference Identity

- **Conference:** The 6th International Conference on Humanity, Education, and Society (ICHES 2027).
- **Academic theme:** Islamic Values in a Changing World: Education, Accountability, and Social Resilience for Inclusive Development.
- **Short promotional theme:** Islamic Values in a Changing World.

These values belong to Edition 2027. Hosting identity and future Edition configuration remain reusable.

## 2. Official Timeline

| Milestone | Official date/window |
|---|---|
| Abstract Submission | 1–20 December 2026 |
| Abstract Acceptance Announcement | 22 December 2026 |
| Full Paper Submission | 23–28 December 2026 |
| Registration/Presenter Payment | 24–28 December 2026 |
| Conference Event | 5 January 2027 |
| Proceedings Publication Target | 28 January 2027 |

The proceedings date is a target, not a guarantee of publication. Selected-journal publication is not necessarily on 28 January; journal editorial timelines are independently controlled. Any internal targets must be explicitly labelled **operational recommendations**, not additional official public dates. This approval specifies dates, not an invented release hour or additional deadline.

## 3. Official Conference Topics

| Code | Edition track / Topic |
|---|---|
| T01 | Islamic Studies, Ethics, and Contemporary Civilization |
| T02 | Education, Pedagogy, and Human Development |
| T03 | Leadership, Governance, and Institutional Accountability |
| T04 | Economics, Sustainable Business, and Inclusive Development |
| T05 | Law, Human Rights, Social Justice, and Public Policy |
| T06 | Social Sciences, Humanities, Culture, and Community Resilience |
| T07 | Digital Transformation, Responsible Innovation, and Society |
| T08 | Environmental Sustainability, and Human Well-Being |

One primary Topic is required on official abstract submission. Topic selection and track configuration are Edition-configurable. A reviewer has one Reviewer role/function; Topic interest and competency are distinct from authorization. Do not create separate Spatie roles per Topic or grant private-file access through Topic interest alone.

## 4. Abstract Submission

- One abstract per submitting user / registration owner per Edition, across DRAFT and official academic states.
- An owned DRAFT without academic evidence may be edited or deleted. Official SUBMITTED cannot be freely edited or deleted, and its author cannot create a second submission for that Edition.
- Controlled corrections and revisions retain the **same Submission ID** and version history; they do not reopen unrestricted draft editing.
- Co-authorship on another person's submission is permitted and does not consume that person's submitting-owner limit. Another Edition has an independent limit.
- Validation protects immutable submitted snapshots, file versions and audit history.
- Presenter payment is not required before abstract acceptance. Valid Edition participation intent and selected package remain the submission context under the [Pre-Phase 02 payment rebaseline](PRE_PHASE02_PRODUCT_REBASELINE.md).

## 5. Abstract Reviewer Self-Claim

A technically valid SUBMITTED abstract automatically enters its Topic-based Review Pool. Ordinary abstracts do not require per-paper Administrative Screening PASS or manual reviewer-email assignment before review.

Reviewer flow:

1. View available abstracts in relevant Topics.
2. Select an unclaimed abstract and declare COI.
3. Claim it directly through an exclusive atomic assignment.
4. Act only on their current stage-specific assigned papers.
5. Save a review draft and submit the final recommendation.

There is **one exclusive active reviewer per submission per academic stage**. Concurrent claims must not create duplicate active owners; a second reviewer cannot review an already-claimed abstract. Topic relevance does not grant global access to confidential assignment information or unrelated private files. Only authorized Academic Committee may release or reassign an abstract assignment, with audit.

Administrative exceptions remain available for technical/scope issues with recorded reasons. They are not a routine mandatory gate. Single-anonymous remains the default: the reviewer may see permitted author metadata; the author does not see reviewer identity. COI, self-review protection and confidential comments remain enforced. Reviewer recommendations are advisory; Academic Decision Authority owns final acceptance.

## 6. Rolling Review and Announcement

Review proceeds progressively from the beginning of the 1–20 December submission window. Internal academic decisions may be prepared before the official announcement.

Author-facing official results and Presentation LoA availability follow the **22 December 2026 announcement/release policy**. Unreleased decisions must not be exposed prematurely through author-facing pages, notifications or document availability. The bounded release work must specify the release control without inventing another official date or hour.

The submitted paper retains its academic identity across all later stages.

## 7. Full Paper Submission

Accepted Author uploads the Full Paper against the existing Submission ID; no separate Submission record is created. Uploads are versioned and stored privately, with access limited to authorized assigned reviewers/staff and the owning author through their permitted flow.

Full Paper submission runs **23–28 December 2026**. Presenter payment runs separately **24–28 December 2026** after ACCEPT. The upload must not be blocked simply because the payment window opens one day later. Academic status, Finance status and presentation entitlements remain separate facts.

Deep Full Paper review is not required before the 5 January presentation. Authorized reviewers may prepare by reading assigned manuscripts.

## 8. Steering Committee Room Mapping

SC determines Room, Topic, Session, Presenter/Paper, Reviewer and schedule allocation. SC does not require a special application role solely for policy-making. Authorized Academic/Event operators enter or import the approved mapping through existing scoped authorities.

Support bulk room/session/paper/reviewer mapping with **preview-before-apply**. Conference-stage reviewer experience is room-based:

- Open **My Rooms** and see only assigned sessions and Full Papers.
- Find papers ordered by presentation schedule; read manuscripts and record assessment.
- Maintain one active responsible reviewer per paper at a time for that stage.
- When a room has multiple reviewers, partition or explicitly allocate its papers so responsibilities do not overlap.

Default continuity retains the abstract reviewer when SC has not reassigned the conference stage. SC-approved mapping may supersede that reviewer for Full Paper / presentation assessment. Bulk reassignment is allowed; it preserves immutable abstract reviews and earlier decisions.

Minimum reassignment audit: actor, time, old reviewer, new reviewer, paper/stage, and reason/source of SC instruction. Reviewer access is assignment-specific, not permission to browse unrelated authors' private files. Reassignment changes current access without rewriting earlier evidence.

## 9. During Conference — 5 January 2027

Hybrid participation is supported: reviewers may attend physically or through authorized online sessions. The reviewer opens the assigned Full Paper, records comments and observations, and reviews the presentation.

Operational attendance, PRESENTED/NO_SHOW, manuscript review and final academic decision remain distinct facts. Attendance or a QR scan alone does not establish PRESENTED or academic approval.

## 10. Post-Presentation Assessment

Deep Full Paper review occurs **after presentation**. Reviewer outcomes are recommendations:

- `NO_REVISION_REQUIRED`
- `MINOR_REVISION_REQUIRED`
- `MAJOR_REVISION_REQUIRED`
- `NOT_RECOMMENDED`

These recommendations are not final authority decisions. No revision does not require an unnecessary new manuscript upload. Required revision produces a new version without overwriting earlier manuscripts. Academic/Publication Authority completes Final ACC and validates the final approved manuscript. Publication destination is a separate decision from revision outcome.

## 11. Publication Routing

- `PROCEEDINGS` is the default destination.
- `SELECTED_JOURNAL` is an authorized override.
- Reviewer may recommend a destination and candidate journal. SC / authorized Academic or Publication Authority controls final routing; Author does not freely select it. SC policy is recorded by authorized operators and does not imply an invented application role.
- Final ACC and publication readiness precede downstream handoff, using the final approved manuscript and immutable canonical metadata/publication snapshot.
- Selected for Journal does not mean Accepted by Journal or Published. Journal editorial review, acceptance, DOI and publication remain downstream responsibilities of the relevant journal/OJS system.
- The proceedings publication target does not impose a selected-journal timeline. Handoff must prevent misleading acceptance/publication claims and duplicate publication.

## 12. Proposed Journal Partner Catalog

**Provenance:** Product Owner-supplied candidate list and SINTA tiers; not independently verified. Entries are proposed partners, not confirmed publication agreements or acceptance guarantees.

| No. | Candidate journal title as supplied | SINTA tier as supplied |
|---|---|---|
| 1 | Iqtishoduna: Jurnal Ekonomi Islam | SINTA 2 |
| 2 | Khidmatuna: Jurnal Pengabdian Masyarakat | SINTA 3 |
| 3 | Bidayatuna: Jurnal Guru Madrasah Ibtidaiyah | SINTA 3 |
| 4 | Dakwatuna: Jurnal Dakwah dan Komunikasi Islam | SINTA 4 |
| 5 | Lan Tabur: Jurnal Ekonomi Syariah | SINTA 2 |
| 6 | Al Qodiri: Jurnal Pendidikan, Sosial dan Keagamaan | SINTA 2 |
| 7 | Mumtaz: Jurnal Pendidikan Agama Islam | SINTA 2 |
| 8 | Tarbiyatuna: Jurnal Kajian Pendidikan Islam | SINTA 3 |
| 9 | Abdi Kami: Jurnal Pengabdian Kepada Masyarakat | SINTA 3 |
| 10 | Auladuna: Jurnal Prodi Pendidikan Guru Madrasah Ibtidaiyah | SINTA 4 |

For **every entry**, identity verification, official journal title, ISSN, accreditation validity, OJS URL, partnership confirmation and available journal slots are **PENDING VERIFICATION**. No journal identifiers, URLs, accreditation-validity claims or confirmed acceptance agreements are supplied or fabricated by this rebaseline.

## 13. Non-negotiable Invariants

- No manual Admin gate for every ordinary abstract; no per-Topic Reviewer role.
- One exclusive active reviewer per paper per stage; SC-approved conference-stage reassignment is supported.
- Reviewed snapshots, previous reviews and decisions remain immutable, traceable and auditable.
- Reviewer recommendation != final decision; academic status != payment status.
- Presentation LoA != proceedings/journal acceptance; Selected Journal != Journal Accepted.
- `READY_FOR_PRODUCTION` != `PUBLISHED`; OJS remains downstream.
- id/en/ar, Arabic RTL, private files, Edition scopes and audit trail remain mandatory.
- Lyra V1.1 + Fluid Workspace remain authoritative for authenticated UI.
- Google Login is postponed until near-production; authentication is not changed now.
- Do not introduce an unnecessary generic peer-review engine.

## Scoped Supersession and Delivery Boundary

[PD-085–PD-096](DECISION_REGISTER.md) record this acceptance. PD-017 mandatory screening is superseded for the 2027 operational model. PD-062 configurable abstract reviewer count remains historical/reusable outside that scope; ICHES 2027 requires exactly one exclusive active responsible reviewer per paper/stage. A conference-stage reassignment changes that stage's owner, not the authorship of an earlier review.

The [V1 academic](../requirements/v1/V1_ACADEMIC_REVIEW_BASELINE.md), [lifecycle](../requirements/v1/V1_LIFECYCLE_BASELINE.md) and [publication](../requirements/v1/V1_PUBLICATION_BASELINE.md) baselines apply this amendment. The [roadmap](../project-management/ICHES_2027_EXECUTION_ROADMAP.md) defines bounded future work; no Self-Claim, Room Mapping or publication implementation is claimed here.
