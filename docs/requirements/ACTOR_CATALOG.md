# Actor Catalog — Approved Baseline

**Status:** PRODUCT OWNER APPROVED BASELINE — permission model Parts 1–10 complete; REQ-PERM-001 DONE

## Participant-side actors

- **Visitor** — public user browsing conference information.
- **Prospective Participant** — user preparing to join an edition.
- **Participant** — base membership/status for a person registered in a conference edition.
- **Author / Corresponding Author** — manages a submission and primary correspondence as applicable.
- **Co-author / Contributor** — scholarly contributor who may exist without a login account.
- **Presenter** — person designated to present a submission; not automatically identical to the corresponding author.
- **Non-presenting Participant** — attends without presenting a paper.
- **Invited Speaker / Keynote** — edition-dependent special participant role.

## Internal actors

- **Front Office** — participant-facing communication/support and escalation.
- **Finance Staff / Verifier** — payment/refund/finance operations.
- **Scientific / Academic Committee** — academic governance, screening, reviewer management/monitoring.
- **Reviewer** — assigned academic evaluation.
- **Academic Decision Authority** — final decision authority where the edition policy requires it.
- **Event Operations** — venue/session/presentation/attendance operations.
- **Session Chair** — session-scoped authority.
- **Moderator** — session moderation.
- **Publication Team / Proceeding Editor** — publication gate, final metadata, proceedings/OJS handoff.
- **Technical Administrator** — technical system administration without automatically receiving academic/finance authority.
- **Conference Administrator** — edition-level administrative configuration/operations.
- **Super Administrator** — platform-level global administration.

## Approved role principles

1. One person may hold multiple roles.
2. Most application roles are edition-scoped.
3. Super Administrator is the primary global platform role.
4. Participant is a base edition membership/status concept, not an exclusive single role.
5. Author and Presenter are distinct.
6. Co-authors/contributors need not have login accounts.
7. Scholarly identity is conceptually separate from authentication/account identity.
8. ORCID is optional. A person does not need an ORCID to register, become an author/co-author, present, review, or otherwise participate.
9. If available, ORCID may be attached to scholarly identity and later distinguished as manually supplied vs authenticated/verified.
10. Domain authorities remain separated:
   - Front Office communicates/escalates;
   - Finance controls finance states;
   - Academic roles control academic decisions;
   - Event roles control event operations;
   - Publication roles control publication processing.
11. Reviewer and Author may coexist, subject to later conflict-of-interest controls.

## Conceptual multi-edition example

```text
Person A
├── Edition 2027
│   ├── Participant
│   ├── Author
│   └── Presenter
└── Edition 2029
    ├── Participant
    ├── Reviewer
    └── Academic Committee
```

## Still pending

Phase 0 must still define:
- detailed permission matrix;
- role assignment/revocation authority;
- conflict-of-interest rules;
- edition membership lifecycle;
- invitation/onboarding for internal roles;
- whether special actors such as Sponsor or Steering Committee require system roles or only metadata/content representation.


## Approved authorization foundation

The permission model is not a flat role checkbox list. Authorization evaluates:

```text
ROLE
+ SCOPE
+ RESOURCE RELATIONSHIP
+ DOMAIN AUTHORITY
+ RESOURCE STATE
+ RESTRICTIONS / COI
= EFFECTIVE PERMISSION
```

Core rules:
- default deny;
- least privilege;
- visibility does not imply authority;
- technical authority does not imply business authority;
- Super Administrator does not automatically receive Finance/Academic/Event/Publication decision powers;
- Conference Administrator coordinates an edition but does not automatically own every authoritative state change;
- permissions are scope-aware: global, edition, session, submission/membership, review assignment;
- multi-role is allowed, but explicit restrictions/COI override a normal allow;
- sensitive data follows need-to-know;
- override/exception permissions are distinct and auditable;
- assignment authority and final decision authority are distinct;
- archived/historical corrections require special controlled authority;
- authorization must be enforced server-side, not only through hidden UI controls.


## Administrative role boundaries — approved

- **Super Administrator** = global platform governance and protected access administration; not automatic business-domain decision authority.
- **Technical Administrator** = system operations/diagnostics; confidential business data is minimum-necessary and business decisions remain denied by default.
- **Conference Administrator** = edition operations/configuration and cross-domain status visibility; authoritative Finance/Academic/Event/Publication decisions require additional roles.
- Protected-role self-escalation is denied.
- Global roles cannot be assigned by ordinary edition-level administration.
- Emergency/break-glass access, when implemented, must be temporary/reasoned/audited and does not rewrite the underlying business history.


## Participant-side permission boundaries — approved

- Participant access is limited to own account/membership unless another resource relationship grants more.
- Corresponding Author is the primary submission manager.
- Other Authors/Co-authors do not automatically receive submission-edit authority.
- Co-author/contributor may remain accountless.
- Submission collaboration/delegation should be submission-scoped, not a broad global role.
- Author-facing review information excludes anonymous reviewer identity, confidential editor comments, internal COI notes, and academic deliberation.
- Presenter cannot self-verify PRESENTED.
- Exceptional non-author Presenter receives presentation-only access, not Author permissions.
- Current profile edits do not rewrite historical submission/publication/certificate/archive snapshots.
- Official submissions are withdrawn through workflow rather than unrestricted deletion.
- Contributor changes after official submission become controlled corrections.


## Finance permission boundaries — approved

- Finance is edition-scoped and is the authoritative role for payment/refund state execution.
- Raw payment proof and bank reconciliation detail are Finance-restricted by default, while other roles receive derived status as needed.
- Finance does not control academic decisions.
- Refund eligibility comes from policy/business events; Finance executes eligible refunds.
- Refund bank/recipient data is Finance-restricted.
- Financial corrections preserve before/after state and audit rather than silently rewriting history.
- Payment/refund records are not freely hard-deleted.
- Overpayment/partial mismatch does not automatically become PAID.
- Finance cannot normally verify or refund its own submission/financial case.
- V1 may use one Finance role while remaining structurally ready for future Finance sub-roles.


## Academic permission boundaries — approved

- Academic Committee manages review operations and reviewer assignments but does not automatically hold final-decision authority.
- Reviewer is assignment/round/version scoped.
- Reviewer access is subject to COI and assignment-specific anonymity.
- Double-anonymous review blocks access to identity-bearing author data/files.
- Reviewer cannot see other reviewers' reports by default.
- Reviewer recommendation is advisory; final decision belongs to Academic Decision Authority.
- Academic Decision Authority may differ by review stage.
- Submitted reviews are locked by default and reopened only through controlled audit.
- Reviewer assignment history is preserved.
- Confidential editor comments never become author-facing data.
- Author/Reviewer/Decision Authority self-conflict blocks review/decision on own paper.
- Academic decisions are corrected through controlled supersession, not silent overwrite.


## Event permission boundaries — approved

- Event Operations is edition-scoped event authority.
- Session Chair and Moderator are session-scoped.
- Event roles receive operationally necessary data only; raw Finance/reviewer/confidential academic data is denied by default.
- Attendance/check-in is distinct from presentation verification.
- Event Operations and Session Chair may verify PRESENTED/NO_SHOW within scope; Moderator verification is policy-configurable.
- Presentation verification is auditable.
- Normal verifier does not automatically possess presentation-exception/makeup approval.
- Authoritative schedule changes belong to Event Operations/authorized edition operations; published changes are traceable.
- Event actors cannot normally verify their own presentation.
- Event-domain state does not grant Academic/Publication/Certificate issuance authority.


## Publication permission boundaries — approved

- Publication Team / Proceeding Editor is edition-scoped publication-operations authority.
- PUBLICATION_APPROVED, PUBLICATION_ELIGIBLE, and PUBLISHED remain distinct.
- Publication Team does not automatically hold Academic Decision Authority.
- Publication Team only processes papers that have passed the required publication gate.
- Publication Team receives derived Finance/eligibility status, not raw Finance evidence by default.
- Final publication metadata is snapshotted.
- Substantive authorship/manuscript changes require controlled correction/authority.
- V1 OJS handoff is manual/assisted and handled by Publication Team.
- OJS/DOI/URL/ISBN/ISSN identifiers remain external references.
- Publication transfer/status changes are auditable.
- Downstream failure/withdrawal preserves conference/presentation history.
- Publication Team cannot bypass review/eligibility gates, including on its own papers.
- Publication records are corrected through controlled history, not hard-deleted.
- V1 may combine Publication Team/Proceeding Editor while remaining ready for future publication sub-roles.


## Certificate & archive permission boundaries — approved

- Certificate issuance/configuration/revoke/reissue are separate capabilities rather than automatic consequences of a broad admin role.
- V1 may assign certificate capabilities to Conference Admin or another designated operator.
- Rule-based and manual individual/bulk issuance are supported.
- Every generated certificate has its own record/number/token/link.
- Public verification exposes minimum credential metadata only.
- Issued certificates use controlled revoke/supersede/reissue rather than free edit or hard delete.
- Manual issuance does not silently rewrite event/academic/publication states.
- Privileged manual self-issuance is denied by default.
- Edition closeout is checklist-driven.
- ARCHIVED is read-only by default.
- Narrow certificate/historical correction capabilities may remain active on archived editions without reopening the entire edition.
- Unarchive is exceptional privileged authority.


## Assignment / revocation / COI governance — approved

- Role/capability assignments are scoped auditable records, separate from resource assignments.
- Temporary/expiring authority is supported.
- Revocation removes future authority without erasing historical attribution.
- Active work can be reassigned when a role/resource assignment is revoked.
- Protected authorities cannot be self-assigned.
- V1 uses authorized assigner + anti-self-escalation + audit rather than mandatory dual approval everywhere.
- COI/restriction is a generic layer that overrides normal permissions across Finance, Academic, Event, Certificate, and Reviewer contexts.
- Overrides are domain-specific and must preserve before/after state, reason, actor, and audit.
- Break-glass is separate from business override, temporary/scoped/reasoned/audited, and never permanent.
- Silent impersonation/account takeover is denied; V1 does not require impersonation.
- Sensitive-role revocation must stop new authorization immediately at the product-policy level.


## Final permission-ownership closure

### Front Office
Edition-scoped support role with broad safe-status visibility but no authoritative Finance/Academic/Event/Publication decision power and no confidential/raw sensitive-data access by default.

### Administrative Screening Authority
`submission.admin_screen` is a capability assignable to Conference Admin or designated edition/Academic Committee staff. Administrative screening is distinct from scholarly judgment.

### Withdrawal Authority
Corresponding Author may request withdrawal. `submission.withdraw.approve` belongs to an authorized edition authority; V1 may assign it to Conference Admin/designee.

### Contributor Change Authority
Protected post-submission contributor/authorship change requires `submission.contributor_change.approve` with stage-aware academic/editorial authority.

### Publication Eligibility Override Authority
Normal eligibility is policy/system evaluated from authoritative source facts. `publication.eligibility.override` is a protected exception capability that cannot rewrite the underlying Finance/Academic/Event fact.
