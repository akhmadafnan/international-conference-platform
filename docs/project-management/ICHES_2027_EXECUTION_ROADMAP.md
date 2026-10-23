# ICHES 2027 Execution Roadmap

**Date:** 2026-10-23
**Status:** PRODUCT_OWNER_APPROVED OPERATIONAL DESIGN — BOUNDED DELIVERY ROADMAP

Authority: [ICHES 2027 Operational Rebaseline](../governance/ICHES_2027_OPERATIONAL_REBASELINE_2026_10_23.md). Implementation evidence: [CURRENT_STATE](../ai-context/CURRENT_STATE.md). This roadmap creates no ticket IDs, implementation completion claims or automatic coding/merge/push authorization. Each future unit needs its bounded plan and explicit applicable ready gate. Preserve the existing uncommitted working tree and Phase03 browser UAT PASS; do not reopen that accepted gate.

Official dates and Topics come only from the operational rebaseline. No additional public deadline is introduced. Existing mandatory-screening/email-assignment code must be treated as the implementation starting point, not evidence that the new model is delivered.

## 1. Documentation Rebaseline — Docs Only

- **Goal:** Record the approved operational model and make canonical entrypoints consistent.
- **Scope / out-of-scope:** New operational baseline and roadmap, scoped supersessions, current checkpoint and reading pointers. No application, data, authentication or infrastructure changes.
- **Dependencies:** Product Owner decisions and verified checkpoint supplied on 23 October 2026.
- **Backend/frontend/data requirements:** Documentation accurately separates existing Academic Core from future Self-Claim/Room/publication behavior; no schema or UI implementation.
- **Security/audit invariants:** Preserve history, scope authority to ICHES 2027, retain Git/database safeguards and existing edits; candidate journals remain unverified.
- **Focused checks / browser UAT:** Check approved dates, eight Topics, ten candidates, sequential decisions, Markdown links and docs-only diff. No new browser UAT for a documentation-only unit; no reopening Phase03 UAT.

## 2. Reviewer Topic Pool and Exclusive Atomic Self-Claim

- **Goal:** Route technically valid SUBMITTED abstracts directly to relevant Topic Pools and allow COI-aware reviewer claims.
- **Scope / out-of-scope:** Edition-configurable primary Topic, reviewer relevance/competency, available pool, atomic claim and authorized release/reassignment; remove mandatory screening/email-assignment dependency from ordinary flow. Keep reasoned technical/scope exceptions. Exclude Room Scheduling, Full Paper review, final decisions and new per-Topic roles.
- **Dependencies:** Unit 1; existing submitted snapshots, ownership/one-abstract policy, scoped authorization and private files.
- **Backend/frontend/data requirements:** Define exclusive active paper/stage ownership and concurrency constraints; server-side Topic/Edition/COI guards; reviewer pool and claim UX with loading/error states; audit claim/release/reassignment. Reuse existing submission identity and evidence.
- **Security/audit invariants:** One exclusive active reviewer per paper/stage; no self-review or unrelated private-file access; only authorized Academic Committee releases/reassigns abstracts; preserve single-anonymous and confidential assignment boundaries.
- **Focused tests / browser UAT:** Two simultaneous claims produce one active owner; duplicate/foreign/COI claims fail safely; valid abstract enters pool without PASS; exceptions require reasons. Two reviewer sessions demonstrate claim exclusivity and access denial, with id/en/ar, RTL, fluid mobile/desktop and light/dark checks.

## 3. Abstract Review Decision Queue, Release Date and LoA

- **Goal:** Support rolling review and human decisions while releasing author-facing results/LoA under the 22 December announcement policy.
- **Scope / out-of-scope:** Assigned review drafts/final locking, Academic Decision Authority queue, controlled abstract revision and official result/LoA release. Exclude Full Paper assessment, Finance decisions and journal acceptance.
- **Dependencies:** Unit 2, immutable snapshots and existing Academic Core decision/LoA mechanisms; reconcile existing behavior rather than claiming a new implementation is already present.
- **Backend/frontend/data requirements:** Distinguish internal decision from author-facing release; specify release control/timezone without inventing a public release hour; preserve version-specific reports and final locks. Add committee queue and author released-result/document states.
- **Security/audit invariants:** Reviewer recommendation is advisory; only authorized authority decides; unreleased results and LoA stay hidden on author-facing surfaces; no reviewer identity/confidential notes leak; all revision/decision/release actions traceable.
- **Focused tests / browser UAT:** Pre-release internal decisions remain private; approved release exposes correct result and LoA only to the owner; final reports stay locked; stale revisions fail. Committee and author browsers verify rolling decisions, release boundaries and document access without premature exposure.

## 4. Accepted Full Paper Upload/Versioning and Post-ACCEPT Payment Integration

- **Goal:** Enable accepted authors to upload Full Papers under the same Submission ID and follow the separate Finance path.
- **Scope / out-of-scope:** Private immutable upload/versioning for 23–28 December, presenter payment path for 24–28 December after ACCEPT, authorized manuscript access and existing Finance integration. No new Submission record, pre-accept payment gate or deep preconference Full Paper review.
- **Dependencies:** Unit 3; existing Registration/Payment authorities, package configuration, workflow windows and file integrity contracts.
- **Backend/frontend/data requirements:** Stage/window/ownership guards, version history and same-paper associations; accepted-author upload and payment Next Actions reflecting independent states. Keep existing Finance verification authoritative.
- **Security/audit invariants:** Academic acceptance, payment and presentation entitlements remain separate; private files are owner/assignment-scoped; uploads and financial evidence retain audit/history.
- **Focused tests / browser UAT:** Full Paper upload succeeds on 23 December while payment is not yet open; payment follows its own window; no second Submission; invalid files/stages/foreign access fail; prior versions survive. Accepted author and Finance browsers verify independent upload/payment flows.

## 5. SC Bulk Room/Session/Schedule/Reviewer Assignment

- **Goal:** Apply SC-approved conference mappings through authorized Academic/Event operators.
- **Scope / out-of-scope:** Bulk entry/import of Room, Topic, Session, Presenter/Paper, Reviewer and schedule; preview-before-apply; partition room papers and support bulk reassignment. No special SC application role solely for policy-making, no automatic policy decisions or academic-history rewrite.
- **Dependencies:** Units 2–4; Full Paper scheduling eligibility, actual presenter and scoped schedule/publication rules.
- **Backend/frontend/data requirements:** Define stage-specific assignment/mapping records and validation, deterministic preview and guarded apply; operator bulk UI. Default to abstract-reviewer continuity unless SC instruction reassigns the conference stage.
- **Security/audit invariants:** One active responsible reviewer per paper/stage, even in rooms with multiple reviewers; only approved mapping entered by authorized operators; immutable abstract history. Audit actor, time, old/new reviewer, paper/stage and SC reason/source.
- **Focused tests / browser UAT:** Overlapping assignments/foreign Editions fail; preview makes no changes; valid bulk apply is consistent; reassignment revokes old stage access without changing earlier reports; unpublished schedules remain private. Operator reviews preview, applies mapping and checks multi-reviewer room partitioning.

## 6. Reviewer My Rooms, Full Paper Access and Conference Presentation Assessment

- **Goal:** Give each reviewer their assigned room/session papers in presentation order for the 5 January hybrid event.
- **Scope / out-of-scope:** My Rooms, schedule-ordered assigned papers, private Full Paper reading, conference comments/observations and presentation assessment. Do not conflate attendance, PRESENTED/NO_SHOW, manuscript review or final academic decision; deep review follows presentation.
- **Dependencies:** Unit 5, authorized online/physical participation and Event Operations facts.
- **Backend/frontend/data requirements:** Assignment-filtered room/session/paper views and file downloads; reviewer assessment draft/persistence; usable fluid touch/desktop interface and room-order context.
- **Security/audit invariants:** Assignment-specific file access on each request; no unrelated papers or confidential assignment disclosure; reassignment updates access; reviewer observations do not grant Event/Academic authority.
- **Focused tests / browser UAT:** Reviewer sees only allocated papers in order; old reviewer loses current-stage file access after reassignment; attendance is independent from presentation and academic outcomes. Reviewers test assigned rooms during physical/authorized online scenarios and record observations without changing final decisions.

## 7. Post-Presentation Revision, Final ACC and Publication Routing

- **Goal:** Complete deep Full Paper review after presentation, resolve revisions and authorize the final manuscript/destination.
- **Scope / out-of-scope:** Four reviewer outcomes from the operational baseline; versioned minor/major revisions; no extra upload for no revision; Academic/Publication Final ACC; separate destination/candidate-journal recommendation and authorized routing. No generic publication peer-review engine, Author-selected destination or journal acceptance claim.
- **Dependencies:** Unit 6, presentation facts, canonical metadata and immutable manuscript history.
- **Backend/frontend/data requirements:** Distinct recommendation, revision/version, final approval/manuscript and destination facts; reviewer, author correction and authority queue UX; proposed candidate catalog with verification status.
- **Security/audit invariants:** Advisory reviewer outcomes never finalize academic decisions; preserve same Submission ID and all prior evidence; routing is SC/authorized Academic or Publication authority; no invented journal identifiers or confirmed partnerships.
- **Focused tests / browser UAT:** Every recommendation follows its permitted revision path; no-revision creates no artificial upload; Final ACC selects the exact approved manuscript; reviewer routing suggestion cannot change final destination. Reviewer, author and authority browsers verify separate revision and destination decisions with traceable versions.

## 8. Proceedings Production and Selected-Journal Manual/Assisted OJS Handoff

- **Goal:** Prepare publication-ready proceedings or selected-journal handoffs from approved manuscripts and canonical metadata.
- **Scope / out-of-scope:** Readiness checks, protected publication snapshot, single/bulk production bundle and manual/assisted OJS handoff; track downstream facts. Target proceedings publication on 28 January. Exclude automatic DOI/Crossref operations, guaranteed journal acceptance or a forced 28 January journal deadline.
- **Dependencies:** Unit 7, readiness rules, final approved manuscript, verified destination details/partner capacity before actual journal handoff and existing OJS adapter contract.
- **Backend/frontend/data requirements:** Distinguish Final ACC, READY/WARNING/BLOCKED, destination, snapshot, handoff and externally verified publication; publication queue and manifest/evidence UX. Catalog verification must precede assertions about real partners/slots.
- **Security/audit invariants:** Handoff requires Final ACC/readiness; bundles exclude reviewer-confidential/internal notes; immutable metadata/file identity; prevent duplicate publication and misleading statuses. OJS/journal owns editorial acceptance, DOI and publication.
- **Focused tests / browser UAT:** Block incomplete/unapproved bundles; verify exact files/checksums/metadata and confidential-data exclusion; Selected Journal never renders Accepted/Published from handoff alone; duplicate handoff/publication is controlled. Publication operator previews proceedings/journal bundles and records a manual handoff while author-facing status remains truthful.
