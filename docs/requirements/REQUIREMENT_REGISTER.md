# Requirement Register

| ID | Requirement | Status |
|---|---|---|
| REQ-L10N-001 | Support Indonesian `id` | ACCEPTED |
| REQ-L10N-002 | Support English `en` | ACCEPTED |
| REQ-L10N-003 | Support Arabic `ar` | ACCEPTED |
| REQ-L10N-004 | Arabic interface supports RTL | ACCEPTED |
| REQ-CONF-001 | Support recurring conference editions | ACCEPTED |
| REQ-PAY-001 | Payment is part of abstract-submission lifecycle | ACCEPTED |
| REQ-PAY-002 | Payment cannot automatically determine academic acceptance | ACCEPTED |
| REQ-PAY-003 | V1 uses manual bank transfer; no payment gateway is required | ACCEPTED |
| REQ-PAY-004 | Uploading payment proof creates submitted-for-verification state, not PAID | ACCEPTED |
| REQ-PAY-005 | Only authorized Finance personnel may verify payment after cross-checking actual receipt | ACCEPTED |
| REQ-PAY-006 | Finance verification result, verifier, timestamp, and action-required reason are auditable | ACCEPTED |
| REQ-PAY-007 | Only PAID may transition a paper into OFFICIAL_SUBMISSION | ACCEPTED |
| REQ-PAY-008 | Sensitive bank/mutation information is Finance-restricted | ACCEPTED |
| REQ-PAY-009 | Payment-domain design must remain compatible with future providers without requiring one in V1 | ACCEPTED |
| REQ-REF-001 | Academic rejection makes the V1 submission refund-eligible | ACCEPTED |
| REQ-REF-002 | V1 academic-rejection refund equals 100% of conference fee actually paid | ACCEPTED |
| REQ-REF-003 | Refund execution is manual and restricted to authorized Finance personnel | ACCEPTED |
| REQ-REF-004 | Withdrawal and administrative-ineligibility refund policies remain separately configurable by edition | ACCEPTED |
| REQ-REF-005 | Refund status, amount, processor, timestamps, and proof/history are auditable | ACCEPTED |
| REQ-ACA-001 | Edition may configure abstract selection/review mode | ACCEPTED |
| REQ-ACA-002 | Official submission undergoes administrative screening before academic processing | ACCEPTED |
| REQ-ACA-003 | Abstract decision outcomes include ACCEPTED, REVISION_REQUIRED, and REJECTED | ACCEPTED |
| REQ-ACA-004 | Abstract revision creates a new traceable version | ACCEPTED |
| REQ-ACA-005 | Academic decision authority is separate from Finance authority | ACCEPTED |
| REQ-ACA-006 | Abstract review is not required to be double-blind at platform level | ACCEPTED |
| REQ-ACA-007 | First-edition abstract review defaults to single-anonymous while remaining edition/stage configurable | ACCEPTED |
| REQ-ACA-009 | Review engine supports configurable review stages and multiple rounds | ACCEPTED |
| REQ-ACA-010 | Review stage supports variable reviewer counts rather than a hardcoded fixed number | ACCEPTED |
| REQ-ACA-011 | Each reviewer assignment may use an assignment-specific task/purpose and review form | ACCEPTED |
| REQ-ACA-012 | Authorized academic/editorial staff may choose reviewers individually and apply controlled review-mode overrides | ACCEPTED |
| REQ-ACA-013 | Double-anonymous assignments must use anonymized files/metadata and isolated identity visibility | ACCEPTED |
| REQ-ACA-014 | Reviewer recommendations inform but do not automatically determine the final academic decision | ACCEPTED |
| REQ-ACA-015 | Review assignments include conflict-of-interest declaration/check and auditable status/history | ACCEPTED |
| REQ-ACA-008 | Post-presentation full-paper Publication Review is required for the publication path and defaults to double-anonymous in V1 | ACCEPTED |
| REQ-PUB-003 | Session/presentation feedback is distinct from formal Publication Review | ACCEPTED |
| REQ-PUB-004 | Publication Review uses configurable Review Stage/Round/Assignment architecture | ACCEPTED |
| REQ-PUB-005 | V1 Publication Review default is double-anonymous while remaining edition/stage configurable | ACCEPTED |
| REQ-PUB-006 | Publication Review supports multi-round revision and versioned final manuscripts | ACCEPTED |
| REQ-PUB-007 | Publication decisions include REVISION_REQUIRED, PUBLICATION_APPROVED, and PUBLICATION_REJECTED | ACCEPTED |
| REQ-PUB-008 | PUBLICATION_APPROVED must still pass an auditable Publication Eligibility Gate | ACCEPTED |
| REQ-PUB-009 | Publication rejection preserves conference/presenter history and does not automatically trigger conference-fee refund | ACCEPTED |
| REQ-PUB-010 | Conference platform controls PUBLICATION_ELIGIBLE before downstream OJS/proceedings handoff | ACCEPTED |
| REQ-PRES-001 | Abstract acceptance/LoA means accepted for presentation, not publication | ACCEPTED |
| REQ-PRES-002 | Accepted Author must submit Full Paper by edition-configurable deadline | ACCEPTED |
| REQ-PRES-003 | Pre-conference Full Paper receives administrative/format validation with version history | ACCEPTED |
| REQ-PRES-004 | Presenter must be explicitly designated and confirmed | ACCEPTED |
| REQ-PRES-005 | Scheduling uses Session and Presentation Slot with draft/published states | ACCEPTED |
| REQ-PRES-006 | Attendance status and presentation status are separate | ACCEPTED |
| REQ-PRES-007 | Presentation completion requires authorized verification | ACCEPTED |
| REQ-PRES-008 | NO_SHOW blocks publication by default unless an authorized exception/makeup applies | ACCEPTED |
| REQ-CERT-001 | Certificate issuance is rule-driven by default but supports authorized manual/ad-hoc issuance | ACCEPTED |
| REQ-CERT-002 | Manual certificate type/wording must match documented role/status and cannot fabricate presentation | ACCEPTED |
| REQ-CERT-003 | Presenter Certificate requires PRESENTED status or an authorized qualifying presentation exception/makeup | ACCEPTED |
| REQ-CERT-004 | Manual certificate issuance records recipient, type, reason, authority, and immutable internal audit timestamps/history | ACCEPTED |
| REQ-CERT-005 | Manual Certificate Builder supports individual and bulk/collective issuance, including external/manual recipients | ACCEPTED |
| REQ-CERT-006 | Certificate display date is an activity/event date configurable for the credential and is distinct from internal record creation/generation timestamps | ACCEPTED |
| REQ-CERT-007 | Each generated certificate has an individual record, identifier/number, verification code, and verification link | ACCEPTED |
| REQ-CERT-008 | Public certificate/verification output need not expose technical generation timestamps or internal administrative notes | ACCEPTED |
| REQ-PUB-001 | Presented manuscript may remain publication-blocked | ACCEPTED |
| REQ-PUB-002 | Required revision must be approved before publication eligibility | ACCEPTED |
| REQ-INT-OJS-001 | OJS is downstream publication integration | ACCEPTED |
| REQ-INT-OJS-002 | V1 OJS/proceedings handoff is manual/assisted; API integration is not required | ACCEPTED |
| REQ-INT-OJS-003 | Publication Queue tracks handoff/status independently from conference academic/payment state | ACCEPTED |
| REQ-INT-OJS-004 | Final publication metadata is stored as a stable snapshot before/at publication handoff | ACCEPTED |
| REQ-INT-OJS-005 | OJS IDs, DOI, URLs, ISBN/ISSN references remain external identifiers, not internal primary keys | ACCEPTED |
| REQ-CERT-009 | Certificates support public verification and QR representation linked to unique verification identity | ACCEPTED |
| REQ-CERT-010 | Certificate correction supports controlled revocation/reissue or versioned replacement | ACCEPTED |
| REQ-ARCH-001 | Edition closeout uses an auditable policy-driven checklist | ACCEPTED |
| REQ-ARCH-002 | Archived editions are historically preserved and primarily read-only | ACCEPTED |
| REQ-ARCH-003 | Historical corrections after archive require controlled authority, reason, and audit trail | ACCEPTED |
| REQ-ARCH-004 | Archive retains lifecycle history subject to access/privacy policy | ACCEPTED |
| REQ-FO-001 | One FO entry point supports participant inquiries | ACCEPTED |
| REQ-FO-002 | FO is not authoritative for academic/finance/publication decisions | ACCEPTED |
| REQ-ID-001 | Scholarly identity supports optional ORCID when available; ORCID is not required for participation, authorship, presentation, or review | ACCEPTED |
| REQ-ID-002 | If ORCID verification is implemented later, manually supplied and authenticated/verified ORCID states must be distinguishable | ACCEPTED DIRECTION |
| REQ-ORG-001 | Organization/affiliation should be ROR-ready | ACCEPTED DIRECTION |
| REQ-PUBMETA-001 | Publication metadata should be Crossref/DOI-ready | ACCEPTED DIRECTION |
| REQ-AUTH-001 | Use Progressive/Hybrid Account Model: verified-email participant workspace with no mandatory password at initial registration | ACCEPTED |
| REQ-AUTH-002 | Participant account is reusable across conference editions | ACCEPTED |
| REQ-AUTH-003 | Co-author/contributor does not require an account merely to be listed on a submission | ACCEPTED |
| REQ-AUTH-004 | Reviewer requires authenticated account access | ACCEPTED |
| REQ-AUTH-005 | Privileged internal roles require stronger-authentication/MFA readiness | ACCEPTED |
| REQ-AUTH-006 | FO cannot perform informal account recovery/account takeover | ACCEPTED |
| REQ-AUTH-007 | Account recovery and primary-email changes are controlled sensitive workflows | ACCEPTED |
| REQ-AUTH-008 | Authentication identity remains separate from scholarly identity/ORCID | ACCEPTED |
| REQ-LIFE-001 | Reuse the same verified participant account across conference editions | ACCEPTED |
| REQ-LIFE-002 | Verified email is required before edition membership becomes active | ACCEPTED |
| REQ-LIFE-003 | Joining a conference creates an edition membership rather than another account | ACCEPTED |
| REQ-LIFE-004 | Participant-only and Author/Submit-Paper pathways may coexist/change while edition policy permits | ACCEPTED |
| REQ-LIFE-005 | Submission count limit is edition-configurable and not hardcoded | ACCEPTED |
| REQ-LIFE-006 | Starting a paper creates a DRAFT submission that is not yet official submission | ACCEPTED |
| REQ-LIFE-007 | Historical edition/submission metadata must not be silently rewritten by later global-profile changes | ACCEPTED |
| REQ-LIFE-008 | Account/profile/edition-membership/draft creation does not itself trigger payment | ACCEPTED |
| REQ-LIFE-009 | End-to-end conference lifecycle stages 4A–4F are Product Owner approved | ACCEPTED |
| REQ-PERM-001 | Authorization uses default-deny and least-privilege principles | ACCEPTED |
| REQ-PERM-002 | Effective permission depends on role, scope, resource relationship/state, domain authority, and restrictions | ACCEPTED |
| REQ-PERM-003 | Most business roles are edition-scoped; session/submission/review-assignment scope applies where relevant | ACCEPTED |
| REQ-PERM-004 | Super Admin and Technical Admin do not automatically inherit Finance, Academic, Event, or Publication business authority | ACCEPTED |
| REQ-PERM-005 | Conference Admin coordinates/configures an edition but does not automatically own all authoritative business decisions | ACCEPTED |
| REQ-PERM-006 | Multi-role permissions may combine, but explicit restriction/COI rules override normal allows | ACCEPTED |
| REQ-PERM-007 | Sensitive data visibility follows need-to-know and is separable from status visibility | ACCEPTED |
| REQ-PERM-008 | Override/exception authority is a distinct permission and requires audit trail | ACCEPTED |
| REQ-PERM-009 | Assignment authority and final decision authority are distinct concepts | ACCEPTED |
| REQ-PERM-010 | Server-side authorization is mandatory; UI visibility alone is not security | ACCEPTED |
| REQ-PERM-011 | Historical/archive corrections require controlled special authority and audit | ACCEPTED |
| REQ-PERM-012 | Super Admin is global platform governance and does not automatically receive business-domain decision permissions | ACCEPTED |
| REQ-PERM-013 | Technical Admin is limited to system/technical operations and minimum-necessary business-data access by default | ACCEPTED |
| REQ-PERM-014 | Conference Admin is edition-scoped and may configure workflows/policies without automatically executing domain decisions | ACCEPTED |
| REQ-PERM-015 | Global protected roles cannot be assigned by ordinary edition-level administration | ACCEPTED |
| REQ-PERM-016 | Administrators cannot self-assign protected roles through ordinary role management | ACCEPTED |
| REQ-PERM-017 | Protected-role assignment and revocation are auditable | ACCEPTED |
| REQ-PERM-018 | Controlled break-glass access may exist for serious technical/security incidents with reason, scope, temporariness, and audit trail | ACCEPTED |
| REQ-PERM-019 | Break-glass access does not silently convert technical/platform administrators into the normal business-domain decision authority | ACCEPTED |
| REQ-PERM-020 | Participant permission is limited to own account/membership unless another scoped relationship grants access | ACCEPTED |
| REQ-PERM-021 | Corresponding Author is the primary submission manager/contact | ACCEPTED |
| REQ-PERM-022 | Co-author/contributor relationship does not automatically grant submission-edit authority and may exist without an account | ACCEPTED |
| REQ-PERM-023 | Submission collaboration/delegation is submission-scoped rather than a broad global role | ACCEPTED |
| REQ-PERM-024 | Author access to review information is limited to author-facing content and excludes anonymous/confidential/internal-review data | ACCEPTED |
| REQ-PERM-025 | Participant-side users cannot access internal Finance/reconciliation data merely through submission involvement | ACCEPTED |
| REQ-PERM-026 | Presenter cannot self-verify PRESENTED status | ACCEPTED |
| REQ-PERM-027 | Exceptional non-author Presenter receives limited presentation access without automatic Author permissions | ACCEPTED |
| REQ-PERM-028 | Invited Speaker/Keynote is an edition-scoped special participant role and does not imply administrative/review authority | ACCEPTED |
| REQ-PERM-029 | Current profile edits do not silently rewrite historical submission/publication/certificate/archive snapshots | ACCEPTED |
| REQ-PERM-030 | Official submissions use formal withdrawal rather than unrestricted hard deletion | ACCEPTED |
| REQ-PERM-031 | Contributor mutations after official submission require controlled correction workflow | ACCEPTED |
| REQ-PERM-032 | Finance is an edition-scoped authoritative financial role | ACCEPTED |
| REQ-PERM-033 | Only authorized Finance may perform authoritative payment verification and refund execution | ACCEPTED |
| REQ-PERM-034 | Raw bank mutation/reconciliation data is Finance-restricted; other domains receive derived financial status only | ACCEPTED |
| REQ-PERM-035 | Payment proof is visible to the related payer/Author and Finance; other-role access is denied by default | ACCEPTED |
| REQ-PERM-036 | Finance authority does not include academic decision authority | ACCEPTED |
| REQ-PERM-037 | Refund eligibility comes from policy/business events; Finance executes eligible refunds rather than freely creating academic eligibility | ACCEPTED |
| REQ-PERM-038 | Refund recipient/bank data is Finance-restricted | ACCEPTED |
| REQ-PERM-039 | Refund amount derives from applicable policy and amount actually paid; exceptions require explicit auditable override | ACCEPTED |
| REQ-PERM-040 | Payment/refund corrections preserve before/after state, reason, actor, timestamp, and evidence/reference | ACCEPTED |
| REQ-PERM-041 | Operational payment/refund records are not freely hard-deleted | ACCEPTED |
| REQ-PERM-042 | Overpayment/partial/mismatched receipt does not automatically result in normal PAID status | ACCEPTED |
| REQ-PERM-043 | Finance cannot normally verify payment or process refund for its own submission/financial case | ACCEPTED |
| REQ-PERM-044 | V1 may use one Finance role but permission architecture must support future Finance sub-role separation | ACCEPTED |
| REQ-PERM-045 | Academic Committee manages review operations but does not automatically hold final academic decision authority | ACCEPTED |
| REQ-PERM-046 | Reviewer access is restricted to the assigned submission, review stage/round, and assigned manuscript version | ACCEPTED |
| REQ-PERM-047 | Reviewer assignment requires COI declaration/screening and COI restriction overrides normal reviewer permission | ACCEPTED |
| REQ-PERM-048 | Review anonymity is enforced per assignment, including mixed single/double-anonymous assignments | ACCEPTED |
| REQ-PERM-049 | Double-anonymous Reviewer access excludes identity-bearing Author metadata/files and identity leakage through technical surfaces | ACCEPTED |
| REQ-PERM-050 | Reviewers cannot view other reviewers' reports by default | ACCEPTED |
| REQ-PERM-051 | Reviewer recommendation is advisory and does not automatically determine final decision | ACCEPTED |
| REQ-PERM-052 | Academic Decision Authority records authoritative academic decisions and may differ by review stage | ACCEPTED |
| REQ-PERM-053 | Exceptional/divergent academic decisions require an auditable rationale according to policy | ACCEPTED |
| REQ-PERM-054 | Submitted reviewer reports are locked by default and may be reopened only through controlled audited workflow | ACCEPTED |
| REQ-PERM-055 | Reviewer assignment history is preserved and not silently deleted | ACCEPTED |
| REQ-PERM-056 | Confidential editor comments must not be exposed through author-facing UI/API/export/email/documents | ACCEPTED |
| REQ-PERM-057 | Academic Decision Authority cannot decide a submission where they are an Author/Contributor or otherwise conflicted | ACCEPTED |
| REQ-PERM-058 | Final academic decision corrections use controlled supersession with prior/new decision, reason, authority, and timestamp | ACCEPTED |
| REQ-PERM-059 | Event Operations is an edition-scoped event authority | ACCEPTED |
| REQ-PERM-060 | Session Chair and Moderator permissions are limited to assigned sessions | ACCEPTED |
| REQ-PERM-061 | Event roles receive operationally necessary/derived readiness data, not raw Finance or confidential review data by default | ACCEPTED |
| REQ-PERM-062 | Authoritative schedule creation/publishing is controlled by Event Operations/authorized edition operations and published changes are auditable | ACCEPTED |
| REQ-PERM-063 | Check-in/attendance status is separate from presentation status | ACCEPTED |
| REQ-PERM-064 | Participant self check-in does not create authoritative PRESENTED status | ACCEPTED |
| REQ-PERM-065 | PRESENTED/NO_SHOW may be recorded only by an authorized event verifier and is auditable | ACCEPTED |
| REQ-PERM-066 | Session Chair may verify presentation status within assigned session; Moderator verification is policy-configurable | ACCEPTED |
| REQ-PERM-067 | Presentation exception/makeup approval is a separate permission from normal verification | ACCEPTED |
| REQ-PERM-068 | Presenter substitutions and post-event presenter corrections are traceable/controlled | ACCEPTED |
| REQ-PERM-069 | Event actors cannot normally verify their own presentation | ACCEPTED |
| REQ-PERM-070 | Event-domain status authority does not confer Academic, Publication, or Certificate issuance authority | ACCEPTED |
| REQ-PERM-071 | Publication Team/Proceeding Editor is an edition-scoped publication-operations authority | ACCEPTED |
| REQ-PERM-072 | PUBLICATION_APPROVED, PUBLICATION_ELIGIBLE, and PUBLISHED are distinct authorization/state concepts | ACCEPTED |
| REQ-PERM-073 | Publication Team does not automatically hold Academic Decision Authority and cannot bypass publication-review/eligibility gates | ACCEPTED |
| REQ-PERM-074 | Publication Team may process only submissions that have satisfied the required publication gate | ACCEPTED |
| REQ-PERM-075 | Publication Team consumes derived Finance/eligibility status rather than raw Finance evidence by default | ACCEPTED |
| REQ-PERM-076 | Final publication metadata is preserved as a stable snapshot before/at handoff | ACCEPTED |
| REQ-PERM-077 | Substantive authorship/manuscript changes after protected stages require controlled correction/authority | ACCEPTED |
| REQ-PERM-078 | V1 manual/assisted OJS/proceedings handoff is an authorized Publication Team operation | ACCEPTED |
| REQ-PERM-079 | OJS/DOI/URL/ISBN/ISSN and similar identifiers remain external references, not internal primary keys | ACCEPTED |
| REQ-PERM-080 | Publication transfer/status changes are auditable and must reflect known downstream fact | ACCEPTED |
| REQ-PERM-081 | Publication failure/withdrawal preserves legitimate conference/presentation/certificate history | ACCEPTED |
| REQ-PERM-082 | Publication Team cannot bypass review/eligibility gates on its own papers | ACCEPTED |
| REQ-PERM-083 | Publication records are not freely hard-deleted; corrections use controlled audited history | ACCEPTED |
| REQ-PERM-084 | V1 may combine Publication Team/Proceeding Editor responsibilities while remaining ready for future publication sub-role separation | ACCEPTED |
| REQ-PERM-085 | Certificate configuration, issuance, manual/bulk issuance, revoke, reissue, and history are distinct capabilities | ACCEPTED |
| REQ-PERM-086 | Rule-based and Manual Certificate Builder issuance paths are both supported | ACCEPTED |
| REQ-PERM-087 | Manual Certificate Builder supports individual/bulk issuance and external/manual recipients | ACCEPTED |
| REQ-PERM-088 | Certificate display date uses configured activity/event date while immutable technical timestamps remain truthful internal audit data | ACCEPTED |
| REQ-PERM-089 | Certificate issuance provenance distinguishes rule-based/manual/bulk/reissue internally | ACCEPTED |
| REQ-PERM-090 | Bulk issuance creates an individual certificate record, identifier/number, verification token, and link per recipient | ACCEPTED |
| REQ-PERM-091 | Public certificate verification exposes minimum credential metadata and hides internal audit/PII by default | ACCEPTED |
| REQ-PERM-092 | Certificate QR resolves verification identity/link rather than exposing unrestricted PII | ACCEPTED |
| REQ-PERM-093 | Issued certificates are corrected through revoke/supersede/reissue rather than free in-place edit or hard delete | ACCEPTED |
| REQ-PERM-094 | Manual certificate issuance does not silently mutate attendance, presentation, academic, or publication states | ACCEPTED |
| REQ-PERM-095 | Privileged manual certificate self-issuance is denied by default | ACCEPTED |
| REQ-PERM-096 | Edition closeout is checklist-driven and may allow downstream publication work to continue according to policy | ACCEPTED |
| REQ-PERM-097 | Archived editions are read-only by default while narrow controlled certificate/publication-reference/historical corrections remain possible | ACCEPTED |
| REQ-PERM-098 | Historical correction is a dedicated audited capability, not unrestricted archive editing | ACCEPTED |
| REQ-PERM-099 | Unarchive is an exceptional privileged audited operation and is not required for ordinary post-archive certificate work | ACCEPTED |
| REQ-PERM-100 | Archive preserves lifecycle records rather than deleting them | ACCEPTED |
| REQ-PERM-101 | Role/capability assignment is a scoped auditable record rather than a single mutable user-role field | ACCEPTED |
| REQ-PERM-102 | Role assignment and resource assignment are distinct concepts | ACCEPTED |
| REQ-PERM-103 | Assignment lifecycle supports activation/invitation, expiry/revocation, and preserved history | ACCEPTED |
| REQ-PERM-104 | Temporary/expiring role and capability assignments are supported | ACCEPTED |
| REQ-PERM-105 | Revocation removes future access but does not erase historical attribution | ACCEPTED |
| REQ-PERM-106 | Active responsibilities can be cancelled/reassigned when authority is revoked | ACCEPTED |
| REQ-PERM-107 | Protected roles/capabilities require authorized protected-role assignment and cannot be self-assigned | ACCEPTED |
| REQ-PERM-108 | V1 requires authorized assigner + anti-self-assignment + audit, while remaining future-ready for dual-approval assignment | ACCEPTED |
| REQ-PERM-109 | COI is a generic restriction layer and overrides normal permission across domains | ACCEPTED |
| REQ-PERM-110 | Direct self-conflict is blocked for Reviewer, Decision Authority, Finance, Event verification, and privileged manual certificate issuance | ACCEPTED |
| REQ-PERM-111 | Overrides are domain-specific; no universal override-everything permission exists | ACCEPTED |
| REQ-PERM-112 | Every override records actor, resource, before/after state, reason, timestamp, and evidence/reference where applicable | ACCEPTED |
| REQ-PERM-113 | Overrides preserve original facts/history rather than erasing them | ACCEPTED |
| REQ-PERM-114 | Break-glass is distinct from business override and is limited to serious technical/security incidents | ACCEPTED |
| REQ-PERM-115 | Break-glass access is temporary, scoped, reasoned, auditable, and does not create permanent authority | ACCEPTED |
| REQ-PERM-116 | Silent impersonation/account takeover is denied and V1 does not require impersonation | ACCEPTED |
| REQ-PERM-117 | Delegation revocation removes future permission without erasing historical attribution | ACCEPTED |
| REQ-PERM-118 | Replacing a role holder does not rewrite prior action attribution | ACCEPTED |
| REQ-PERM-119 | Revoked sensitive authority must stop authorizing new actions immediately at product-policy level | ACCEPTED |
| REQ-PERM-120 | Revocation workflow may surface active-dependency warnings without preventing emergency revocation | ACCEPTED |
| REQ-PERM-121 | Assignment/revocation/expiry/scope-change/reassignment events are auditable | ACCEPTED |
| REQ-PERM-122 | Front Office is an edition-scoped support role with safe operational-status visibility and no default authoritative business decision power | ACCEPTED |
| REQ-PERM-123 | Front Office cannot access raw Finance/confidential review data or impersonate/take over user accounts by default | ACCEPTED |
| REQ-PERM-124 | Administrative screening uses a distinct submission.admin_screen capability and is separated from academic judgment | ACCEPTED |
| REQ-PERM-125 | Formal withdrawal separates author request from authorized edition approval and never hard-deletes official submissions | ACCEPTED |
| REQ-PERM-126 | Protected contributor/authorship changes use stage-aware submission.contributor_change.approve authority with before/after audit | ACCEPTED |
| REQ-PERM-127 | Normal publication eligibility is evaluated from authoritative source facts plus edition policy rather than discretionary operator mutation | ACCEPTED |
| REQ-PERM-128 | Publication Team may remediate publication-domain blockers but cannot falsify Finance/Academic/Event source facts | ACCEPTED |
| REQ-PERM-129 | publication.eligibility.override is a protected domain-specific override that preserves source facts and records blocking condition, authority, reason, result, and audit | ACCEPTED |
| REQ-PERM-130 | Final canonical permission matrix covers all approved actors/domains and lifecycle 4A–4F | ACCEPTED |
| REQ-PERM-131 | Full permission consistency audit is GREEN with no unresolved authority-ownership gap at product-requirement level | ACCEPTED |
| REQ-PERM-132 | REQ-PERM-001 is complete; physical RBAC/ABAC implementation remains a later architecture/implementation decision | ACCEPTED |
| REQ-PRD-001 | PRD must reach baseline before Phase 1 begins | ACCEPTED |

Statuses: PROPOSED, ANALYSIS, ACCEPTED DIRECTION, ACCEPTED, IMPLEMENTED, VERIFIED, SUPERSEDED.


| NFR-SEC-001 | Production traffic must use HTTPS/secure transport | ACCEPTED |
| NFR-SEC-002 | Protected actions require server-side authorization on every request using default-deny, least-privilege, scope/resource-aware rules | ACCEPTED |
| NFR-SEC-003 | High-risk internal authorities require MFA in production | ACCEPTED |
| NFR-SEC-004 | Participant MFA is not mandatory for V1; Reviewer MFA is supported and policy-configurable | ACCEPTED |
| NFR-SEC-005 | Highly sensitive security/authority actions may require re-authentication/step-up verification | ACCEPTED |
| NFR-SEC-006 | Revoked authority must stop authorizing the next protected action even if an existing session remains open | ACCEPTED |
| NFR-SEC-007 | Authentication, verification, recovery, and other sensitive endpoints require abuse/rate-limit protection | ACCEPTED |
| NFR-SEC-008 | Public authentication/recovery flows must avoid unnecessary account-existence disclosure | ACCEPTED |
| NFR-SEC-009 | Magic-link/OTP tokens must be unpredictable, short-lived, single-use, action/account-bound, and not exposed in plaintext logs/analytics | ACCEPTED |
| NFR-SEC-010 | Account recovery and primary-email change are controlled sensitive workflows; Front Office cannot take over accounts | ACCEPTED |
| NFR-SEC-011 | Uploaded files are treated as untrusted input and private files cannot become permanently public merely because upload succeeded | ACCEPTED |
| NFR-SEC-012 | Secrets/credentials/tokens must not be stored in source repository, client bundle, or plaintext logs | ACCEPTED |
| NFR-SEC-013 | Production error responses must not expose stack traces, secrets, sensitive paths, SQL details, or internal credentials | ACCEPTED |
| NFR-SEC-014 | Security-sensitive events including abnormal login/recovery, role changes, MFA/security changes, break-glass, and sensitive overrides must be auditable | ACCEPTED |
| NFR-SEC-015 | Silent impersonation/account takeover remains prohibited; V1 does not require impersonation | ACCEPTED |
| NFR-SEC-016 | Detailed authentication libraries, MFA providers, session mechanisms, and security-control implementation remain deferred to architecture/implementation | ACCEPTED |