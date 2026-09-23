# Decision Register

| ID | Decision | Status |
|---|---|---|
| DEC-001 | Mandatory locales are `id`, `en`, `ar` | ACCEPTED |
| DEC-002 | Arabic RTL is required from first UI foundation | ACCEPTED |
| DEC-003 | Multi-edition conference architecture | ACCEPTED |
| DEC-004 | Payment occurs in abstract-submission lifecycle | ACCEPTED |
| DEC-005 | Payment does not imply academic acceptance | ACCEPTED |
| DEC-006 | Refund workflow is configurable by edition | ACCEPTED |
| DEC-007 | Abstract selection/review mode configurable by edition | ACCEPTED |
| DEC-008 | Presented does not automatically mean publication-ready | ACCEPTED |
| DEC-009 | Required post-presentation revision blocks publication | ACCEPTED |
| DEC-010 | OJS is downstream publication infrastructure | ACCEPTED |
| DEC-011 | One WhatsApp number acts as FO communication gateway | ACCEPTED |
| DEC-012 | WhatsApp is not authoritative system state | ACCEPTED |
| DEC-013 | Scholarly model anticipates ORCID/ROR/OJS/Crossref/DOI | ACCEPTED DIRECTION |
| DEC-014 | GitHub + docs-as-code is engineering source of truth | ACCEPTED |
| DEC-015 | Project → Phase → Epic → Ticket | ACCEPTED |
| DEC-016 | No implementation before Definition of Ready | ACCEPTED |
| DEC-017 | PRD is a core Phase 0 product-truth artifact | ACCEPTED |
| DEC-018 | Authentication/registration model: Progressive/Hybrid Account Model | ACCEPTED |
| DEC-019 | Product is a full academic conference lifecycle platform, not merely website/submission tooling | ACCEPTED |
| DEC-020 | Initial planning baseline is approximately 100 participants/submissions, primarily Indonesia | ACCEPTED |
| DEC-021 | One reusable application serves recurring editions while preserving historical editions | ACCEPTED |
| DEC-022 | V1 must complete first-conference operations without forcing future-scale features into V1 | ACCEPTED |
| DEC-023 | One person may hold multiple conference roles simultaneously | ACCEPTED |
| DEC-024 | Most conference roles are edition-scoped; Super Admin is global platform scope | ACCEPTED |
| DEC-025 | Participant is a base edition membership/status concept, not an exclusive role | ACCEPTED |
| DEC-026 | Author/Corresponding Author and Presenter are distinct | ACCEPTED |
| DEC-027 | Co-author/contributor may exist without a login account | ACCEPTED |
| DEC-028 | FO, Finance, Academic, Event, and Publication authority boundaries must remain separated | ACCEPTED |
| DEC-029 | Scholarly identity is conceptually separate from authentication/account identity | ACCEPTED |
| DEC-030 | ORCID is optional; lack of ORCID must not block registration, authorship, presentation, review, or participation | ACCEPTED |
| DEC-031 | If provided, ORCID may be stored on scholarly identity; future verification must distinguish manual vs authenticated/verified state | ACCEPTED |
| DEC-032 | Initial participant registration requires verified email but not mandatory password | ACCEPTED |
| DEC-033 | Participant account/workspace is reusable across conference editions | ACCEPTED |
| DEC-034 | Reviewer must use authenticated account access | ACCEPTED |
| DEC-035 | Privileged internal roles require stronger-authentication/MFA readiness | ACCEPTED |
| DEC-036 | FO cannot perform informal identity recovery/account takeover | ACCEPTED |
| DEC-037 | Account recovery and primary-email changes are sensitive controlled workflows | ACCEPTED |
| DEC-038 | The same participant account is reused across conference editions | ACCEPTED |
| DEC-039 | Verified email is required before edition membership becomes active | ACCEPTED |
| DEC-040 | Joining an edition creates edition membership, not a new account | ACCEPTED |
| DEC-041 | Participant-only and Author pathways are not permanently mutually exclusive | ACCEPTED |
| DEC-042 | Submission-count limits are configurable per edition rather than hardcoded | ACCEPTED |
| DEC-043 | Starting a paper creates a DRAFT submission; draft is not an official submission | ACCEPTED |
| DEC-044 | Historical edition/submission metadata is protected from silent retroactive profile changes | ACCEPTED |
| DEC-045 | Payment does not start merely from account/profile/membership/draft creation | ACCEPTED |
| DEC-046 | V1 payment method is manual bank transfer; payment gateway is not required | ACCEPTED |
| DEC-047 | Payment-proof upload is not equivalent to PAID/verified status | ACCEPTED |
| DEC-048 | Only authorized Finance verification after actual-receipt cross-check may set PAID | ACCEPTED |
| DEC-049 | Only PAID transitions a paper to OFFICIAL_SUBMISSION | ACCEPTED |
| DEC-050 | Official submission creates a stable submitted-version snapshot | ACCEPTED |
| DEC-051 | Before payment succeeds, submit intent may return to DRAFT subject to policy; after payment, withdrawal is formal | ACCEPTED |
| DEC-052 | Sensitive bank/mutation information is restricted to Finance | ACCEPTED |
| DEC-053 | V1 remains future-payment-provider-ready without implementing a provider now | ACCEPTED |
| DEC-054 | Administrative screening precedes academic processing | ACCEPTED |
| DEC-055 | Core abstract academic outcomes are ACCEPTED, REVISION_REQUIRED, and REJECTED | ACCEPTED |
| DEC-056 | Abstract revisions are versioned and traceable | ACCEPTED |
| DEC-057 | Academic authority and Finance authority are separate | ACCEPTED |
| DEC-058 | Academic rejection automatically creates refund eligibility | ACCEPTED |
| DEC-059 | V1 academic-rejection refund is 100% of conference fee actually paid | ACCEPTED |
| DEC-060 | Refund is executed manually by authorized Finance personnel | ACCEPTED |
| DEC-061 | Withdrawal and administrative-ineligibility refund rules are separate edition policies | ACCEPTED |
| DEC-062 | Abstract review is not required to be double-blind at platform level | ACCEPTED |
| DEC-063 | First-edition abstract review defaults to single-anonymous while remaining configurable | ACCEPTED |
| DEC-064 | Post-presentation full-paper Publication Review is part of the publication path; V1 default is double-anonymous while reviewer count remains policy-configurable | ACCEPTED |
| DEC-065 | Review architecture is stage/round/assignment based and must not hardcode one global review mode | ACCEPTED |
| DEC-066 | Reviewer count is variable and policy-driven; 1, 2, 3, or more assignments are supported | ACCEPTED |
| DEC-067 | Each review assignment may have a distinct purpose/task and review form | ACCEPTED |
| DEC-068 | Authorized academic/editorial staff select reviewers individually and may use controlled anonymity-mode overrides | ACCEPTED |
| DEC-069 | Double-anonymous assignments require isolated anonymized review packets and identity-safe metadata visibility | ACCEPTED |
| DEC-070 | Final academic decision belongs to the authorized decision authority/editor, not automatic reviewer majority voting | ACCEPTED |
| DEC-071 | Review assignments require COI controls and auditable assignment/review history | ACCEPTED |
| DEC-072 | Abstract acceptance and LoA mean accepted for presentation, not publication | ACCEPTED |
| DEC-073 | Full Paper is required after acceptance and receives pre-conference administrative/format validation | ACCEPTED |
| DEC-074 | Presenter is explicitly designated/confirmed; Author and Presenter remain distinct | ACCEPTED |
| DEC-075 | Scheduling uses Session + Presentation Slot with draft/published states | ACCEPTED |
| DEC-076 | Attendance and presentation are separate statuses and presentation requires authorized verification | ACCEPTED |
| DEC-077 | NO_SHOW blocks publication by default unless an authorized exception/makeup applies | ACCEPTED |
| DEC-078 | Manual/ad-hoc certificate issuance is supported with audit trail and truthful certificate type | ACCEPTED |
| DEC-079 | Presenter Certificate cannot be issued for a non-presenter unless a documented qualifying presentation exception/makeup exists | ACCEPTED |
| DEC-080 | Session/presentation feedback is distinct from formal Publication Review | ACCEPTED |
| DEC-081 | V1 Publication Review defaults to double-anonymous but remains configurable by edition/stage | ACCEPTED |
| DEC-082 | Publication Review supports multiple rounds and versioned revised/final manuscripts | ACCEPTED |
| DEC-083 | Publication decisions are REVISION_REQUIRED, PUBLICATION_APPROVED, and PUBLICATION_REJECTED | ACCEPTED |
| DEC-084 | Reviewer recommendations do not automatically decide publication; authorized Publication/Academic Decision Authority records the decision | ACCEPTED |
| DEC-085 | PUBLICATION_APPROVED must pass an auditable Publication Eligibility Gate before PUBLICATION_ELIGIBLE | ACCEPTED |
| DEC-086 | Publication rejection preserves presenter/conference history and does not automatically trigger conference-fee refund | ACCEPTED |
| DEC-087 | Conference platform is authoritative for PUBLICATION_ELIGIBLE before OJS/proceedings handoff | ACCEPTED |
| DEC-088 | Certificate module includes an authorized Manual Certificate Builder for individual and bulk/collective issuance | ACCEPTED |
| DEC-089 | Certificate display date is the configured activity/event date and is distinct from immutable internal creation/generation timestamps | ACCEPTED |
| DEC-090 | Technical certificate generation timestamps remain internal and need not appear on the certificate or normal public verification page | ACCEPTED |
| DEC-091 | Every generated certificate receives its own record and unique verification identity/link | ACCEPTED |
| DEC-092 | PUBLICATION_ELIGIBLE is distinct from PUBLISHED | ACCEPTED |
| DEC-093 | V1 OJS/proceedings handoff is manual/assisted; API integration is deferred | ACCEPTED |
| DEC-094 | Publication Team owns the Publication Queue while OJS remains downstream | ACCEPTED |
| DEC-095 | Final publication metadata is snapshotted before/at handoff and protected from silent profile rewrites | ACCEPTED |
| DEC-096 | External publication identifiers remain references and never become internal primary keys | ACCEPTED |
| DEC-097 | Certificate verification includes a public verification identity/link and QR representation | ACCEPTED |
| DEC-098 | Issued certificate corrections use controlled revocation/reissue or versioned correction | ACCEPTED |
| DEC-099 | Edition closeout is a formal policy-driven checklist process | ACCEPTED |
| DEC-100 | Archived editions are preserved, primarily read-only, and corrected only through auditable controlled changes | ACCEPTED |
| DEC-101 | End-to-end lifecycle stages 4A–4F are approved and REQ-LIFE-001 is complete at PRD level | ACCEPTED |
| DEC-102 | Authorization baseline uses default deny and least privilege | ACCEPTED |
| DEC-103 | Effective authorization is role + scope + resource relationship/state + domain authority + restriction aware | ACCEPTED |
| DEC-104 | Visibility/read access does not imply authority to change an authoritative business state | ACCEPTED |
| DEC-105 | Super Admin is not an automatic Finance/Academic/Event/Publication decision-maker | ACCEPTED |
| DEC-106 | Technical Admin does not receive confidential/business-domain authority by default | ACCEPTED |
| DEC-107 | Conference Admin coordinates an edition without automatically inheriting all domain decisions | ACCEPTED |
| DEC-108 | Multi-role permissions combine subject to restriction/COI rules; explicit deny/restriction wins | ACCEPTED |
| DEC-109 | Sensitive-data access follows need-to-know and may be narrower than status visibility | ACCEPTED |
| DEC-110 | Override/exception authority is distinct, reasoned, and auditable | ACCEPTED |
| DEC-111 | Assignment authority and decision authority are separate | ACCEPTED |
| DEC-112 | Authorization must be enforced server-side regardless of UI visibility | ACCEPTED |

Statuses: PROPOSED, ANALYZED, ACCEPTED DIRECTION, ACCEPTED, REJECTED, SUPERSEDED, OPEN.
