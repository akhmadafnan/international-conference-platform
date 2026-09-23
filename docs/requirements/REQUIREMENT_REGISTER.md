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
| REQ-PRD-001 | PRD must reach baseline before Phase 1 begins | ACCEPTED |

Statuses: PROPOSED, ANALYSIS, ACCEPTED DIRECTION, ACCEPTED, IMPLEMENTED, VERIFIED, SUPERSEDED.
