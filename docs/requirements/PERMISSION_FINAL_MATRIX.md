# Final Application Permission Matrix

**ID:** ICP-REQ-PERM-001-FINAL  
**Status:** PRODUCT OWNER APPROVED — CANONICAL / GREEN  
**Approved:** 2026-09-24

## Interpretation

Effective authorization is evaluated from:

```text
ROLE / CAPABILITY
+ SCOPE
+ RESOURCE RELATIONSHIP
+ RESOURCE STATE
+ DOMAIN AUTHORITY
- RESTRICTION / COI
= EFFECTIVE PERMISSION
```

Default is DENY.

This matrix is product-level authorization truth. It does not prescribe the physical RBAC/ABAC schema.

## Scope legend

- **GLOBAL** — platform-wide.
- **EDITION** — only assigned conference edition.
- **SESSION** — only assigned session.
- **SUBMISSION** — only related/assigned submission.
- **REVIEW ASSIGNMENT** — assigned stage/round/version/task only.
- **OWN** — user's own account/membership/resource.
- **PUBLIC** — no authenticated private access.

## Canonical actor matrix

| Actor / capability | Scope | Primary allowed authority | Explicit high-risk boundary |
|---|---|---|---|
| Public Visitor | PUBLIC | Public site, published schedule/speakers, public certificate verification, public proceedings links | No private participant/submission/payment/review data |
| Prospective Participant | OWN | Register, verify email, complete profile, join edition | No private edition capabilities before active membership |
| Participant | OWN + EDITION membership | Own profile, membership, registration, announcements, own attendance/certificates | No submission/review/Finance authority merely from membership |
| Corresponding Author | SUBMISSION | Manage draft/submission, payment proof, author-facing decisions/revisions, full/final paper, presenter designation, withdrawal request | No reviewer identity/confidential comments/raw Finance; no hard-delete official paper |
| Co-author / Contributor | SUBMISSION relationship | Scholarly contributor metadata; account optional | No automatic edit/workspace authority |
| Submission Collaborator | SUBMISSION | Delegated VIEW/EDIT_METADATA/UPLOAD_FILES/RESPOND_REVISION as granted | No broad edition role; sensitive actions require stronger authority |
| Presenter | SUBMISSION / presentation | Confirm designation, view presentation details, presentation materials where enabled | Cannot self-set PRESENTED/NO_SHOW |
| Non-presenting Participant | OWN + EDITION | Attendance/member workflow and eligible participant certificate | No presentation/submission authority from attendance alone |
| Invited Speaker / Keynote | EDITION + own speaking assignment | Own bio/talk/session/speaker certificate | No automatic admin/reviewer/Finance authority |
| Front Office | EDITION | Safe status lookup, guidance, permitted resend, support case, escalation | No raw Finance/confidential review, business decisions, impersonation/account takeover |
| Conference Admin | EDITION | Edition configuration, deadlines, tracks, workflow configuration, operational dashboards, ordinary edition coordination | Configuration ≠ Finance/Academic/Event/Publication decision; no protected self-escalation |
| Technical Admin | Technical/global assignment | System health/configuration/diagnostics/maintenance | Minimum-necessary business data; no default business decisions/confidential access |
| Super Admin | GLOBAL | Platform governance, global access/security administration, editions, audit oversight | Not automatic Finance/Academic/Event/Publication authority |
| Finance | EDITION | Payment verification, reconciliation, refund execution, controlled financial correction | Raw finance restricted; no Academic authority; no own-case verification/refund |
| Academic Committee | EDITION / assigned academic resources | Manage review process, assignments, COI, monitoring, relevant review visibility | No automatic final academic decision |
| Reviewer | REVIEW ASSIGNMENT | Accept/decline, COI declaration, assigned packet, review/comments/recommendation | Assignment/round/version only; no final decision; anonymity enforced |
| Academic Decision Authority | Review-stage / submission scope | Authoritative academic decision for assigned stage | No own/conflicted-paper decision; no default Finance/Event/OJS authority |
| Event Operations | EDITION | Sessions/slots/schedule, check-in/attendance, presentation verification, no-show, reschedule | No raw Finance/confidential review; no Academic/Publication decision |
| Session Chair | SESSION | Session operation, relevant attendance, PRESENTED/NO_SHOW verification | Assigned session only; cannot self-verify own presentation |
| Moderator | SESSION | Session moderation, timing, notes; verification if policy grants | Verification configurable; cannot self-verify |
| Publication Team / Proceeding Editor | EDITION | Publication Queue, final metadata validation, OJS/proceedings handoff, external refs/status | Cannot create academic approval or bypass eligibility; no raw Finance by default |
| Certificate Authority capability | EDITION / certificate resource | Configure/issue/manual/bulk/revoke/reissue as separately granted | Manual issuance does not rewrite lifecycle facts; privileged self-issuance denied |
| Archive / Historical Correction capability | EDITION/archive resource | Closeout, archive, narrow historical correction/unarchive where granted | ARCHIVED read-only by default; no unrestricted archive editing |
| Break-glass capability | Explicit temporary scope | Serious technical/security emergency access only | Temporary, reasoned, audited; not ordinary business override |

## Sensitive lifecycle capabilities

| Capability | Baseline authority | Guardrail |
|---|---|---|
| submission.admin_screen | Conference Admin or designated edition/Academic Committee screening authority | Administrative completeness/eligibility only; no scholarly judgment |
| payment.verify | Finance | No self-case; auditable |
| refund.execute | Finance | Eligibility originates from policy/business event; no self-case |
| review.assign | Academic Committee / authorized academic manager | COI-aware, auditable |
| review.submit | Assigned Reviewer | Assignment/round/version scoped; submitted review locked |
| academic.decide | Academic Decision Authority | Stage-scoped; COI restriction; decision supersession for corrections |
| submission.withdraw.request | Corresponding Author / authorized submission manager | Request only |
| submission.withdraw.approve | Authorized edition withdrawal authority | Controlled state transition; preserves history/refund consequence |
| submission.contributor_change.approve | Stage-aware designated admin/academic/editorial authority | Before/after contributor snapshot + reason/audit |
| schedule.publish / mutate published schedule | Event Operations / authorized edition event authority | Published changes auditable |
| presentation.verify | Event Operations; Session Chair; Moderator if policy permits | No self-verification |
| presentation.exception.approve | Separately protected event/edition authority | Preserves NO_SHOW/original fact and exception history |
| publication.decide | Academic/Publication Decision Authority for review stage | Reviewer recommendations advisory; COI applies |
| publication.eligibility.evaluate | System/policy gate from authoritative facts | Operator cannot falsify source facts |
| publication.eligibility.override | Protected edition-specific override authority | Domain-specific; reason/evidence; source facts preserved |
| publication.transfer | Publication Team | Gate-satisfied papers only; external refs auditable |
| certificate.issue_manual / bulk | Designated Certificate Authority | Unique record/token per certificate; manual issuance ≠ lifecycle rewrite |
| certificate.revoke / reissue | Designated Certificate Authority | Preserve old record/verification history |
| edition.archive | Authorized archive authority | Formal closeout/checklist |
| historical.correct | Protected historical-correction authority | Narrow correction with before/after/reason/audit |
| edition.unarchive | Privileged exceptional authority | Explicit reason/audit; not normal historical maintenance |
| protected role assignment | Authorized protected-role assigner | No self-assignment; scope + audit |
| break-glass | Explicit emergency authority | Temporary/scoped/reasoned/fully audited |

## Data-boundary matrix

| Data class | Normal authorized visibility |
|---|---|
| Public conference content | Public |
| Participant private profile | Self + authorized operational roles on need-to-know basis |
| Submission protected files | Related Author/delegate + assigned academic/publication roles as lifecycle requires |
| Payment proof | Related payer/Author + Finance |
| Bank mutation/reconciliation/refund bank data | Finance only by default |
| Reviewer identity under anonymous modes | Authorized academic administration/decision roles only as mode permits |
| Confidential editor/reviewer comments | Authorized academic/editorial roles only |
| Author-facing review comments | Related Author + authorized academic roles |
| Event operational status | Event roles + safe derived views for support/participant as applicable |
| Publication metadata snapshot | Publication Team + related Author view + relevant academic/editorial roles |
| Certificate public verification data | Public minimum credential metadata |
| Audit/security logs | Scope-appropriate authorized admin/audit roles |

## Core non-negotiable separations

```text
STATUS VISIBILITY ≠ MUTATION AUTHORITY
CONFIGURATION ≠ DOMAIN DECISION
PAYMENT PAID ≠ ACADEMIC ACCEPTANCE
PRESENTED ≠ PUBLICATION APPROVED
PUBLICATION APPROVED ≠ PUBLICATION ELIGIBLE
PUBLICATION ELIGIBLE ≠ PUBLISHED
CERTIFICATE ISSUED ≠ UNDERLYING EVENT FACT CREATED
ARCHIVED ≠ DELETED
BREAK-GLASS ≠ BUSINESS OVERRIDE
```

## Completion

Parts 1–10 are approved.

The final permission consistency audit is GREEN.

`REQ-PERM-001` is DONE at product-requirement level.


## NFR Privacy Overlay

Permission grants define who may access data, while privacy rules additionally constrain how that data may be exposed, exported, logged, notified, integrated, retained, and copied across environments.

Key overlay rules:
- confidential/restricted data stays private by default;
- reviewer anonymity applies across UI/API/files/export/email/metadata/logs;
- Finance-restricted evidence does not become visible merely because another role can see a derived status;
- exports and APIs cannot become authorization bypasses;
- public certificate verification remains minimum-data only;
- current-profile edits never silently rewrite historical snapshots;
- third-party integrations receive only minimum purpose-required data.
