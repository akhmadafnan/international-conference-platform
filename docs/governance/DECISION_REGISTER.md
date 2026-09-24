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
| DEC-113 | Super Administrator is global platform governance, not an automatic business-domain decision authority | ACCEPTED |
| DEC-114 | Technical Administrator is a technical-operations role with minimum-necessary access to business data | ACCEPTED |
| DEC-115 | Conference Administrator is edition-scoped and may configure workflows without automatically executing authoritative domain decisions | ACCEPTED |
| DEC-116 | Ordinary edition administration cannot assign global protected roles | ACCEPTED |
| DEC-117 | Protected roles cannot be self-assigned through normal role-management capability | ACCEPTED |
| DEC-118 | Protected-role assignment/revocation must be auditable | ACCEPTED |
| DEC-119 | Controlled break-glass access may be used for serious technical/security incidents and must be reasoned, temporary, scoped, and audited | ACCEPTED |
| DEC-120 | Break-glass access does not rewrite business-domain authority/history | ACCEPTED |
| DEC-121 | Participant access is limited to own account/membership unless another scoped resource relationship grants more | ACCEPTED |
| DEC-122 | Corresponding Author is the primary submission manager; other contributors do not automatically inherit management authority | ACCEPTED |
| DEC-123 | Co-author/contributor may exist without an account and therefore without workspace access | ACCEPTED |
| DEC-124 | Submission collaboration/delegation is modeled as a submission-scoped relationship, not a broad global role | ACCEPTED |
| DEC-125 | Author-facing review access excludes anonymous reviewer identity, confidential comments, COI notes, and internal deliberation | ACCEPTED |
| DEC-126 | Presenter cannot self-set authoritative PRESENTED status | ACCEPTED |
| DEC-127 | Exceptional non-author Presenter receives limited presentation access rather than Author permissions | ACCEPTED |
| DEC-128 | Invited Speaker/Keynote is edition-scoped and does not imply administrative/academic/reviewer authority | ACCEPTED |
| DEC-129 | Current-profile updates do not rewrite historical conference snapshots | ACCEPTED |
| DEC-130 | Official submissions are not freely hard-deleted; withdrawal is a formal workflow | ACCEPTED |
| DEC-131 | Contributor changes after official submission are controlled corrections | ACCEPTED |
| DEC-132 | Finance is edition-scoped and authoritative for payment/refund execution | ACCEPTED |
| DEC-133 | Raw bank mutation/reconciliation data is Finance-restricted; other domains consume derived status | ACCEPTED |
| DEC-134 | Payment proof is related-Author + Finance visible by default; unrelated-role visibility is denied | ACCEPTED |
| DEC-135 | Finance authority does not confer academic decision authority | ACCEPTED |
| DEC-136 | Refund eligibility is policy/business-event driven while Finance executes eligible refunds | ACCEPTED |
| DEC-137 | Refund recipient/bank data is Finance-restricted | ACCEPTED |
| DEC-138 | Financial corrections are controlled and audited rather than silently overwritten | ACCEPTED |
| DEC-139 | Payment/refund operational records are not freely hard-deleted | ACCEPTED |
| DEC-140 | Overpayment/partial mismatch does not automatically become normal PAID | ACCEPTED |
| DEC-141 | Finance may not normally verify/process its own payment or refund case | ACCEPTED |
| DEC-142 | V1 may use one Finance role while remaining ready for future Payment Verifier/Refund Processor/Finance Approver separation | ACCEPTED |
| DEC-143 | Academic Committee manages academic review operations but is not automatically the final decision authority | ACCEPTED |
| DEC-144 | Reviewer permission is review-assignment/round/version scoped | ACCEPTED |
| DEC-145 | Reviewer access requires COI declaration/screening and COI restriction overrides role permission | ACCEPTED |
| DEC-146 | Review anonymity is enforced per assignment, including mixed anonymity on one submission | ACCEPTED |
| DEC-147 | Double-anonymous access excludes identity-bearing author metadata/files and technical identity leakage | ACCEPTED |
| DEC-148 | Reviewers cannot view other reviewers' reports by default | ACCEPTED |
| DEC-149 | Reviewer recommendation is advisory; no automatic majority decision | ACCEPTED |
| DEC-150 | Academic Decision Authority records final academic decision and may differ by review stage | ACCEPTED |
| DEC-151 | Divergent/exceptional academic decisions require auditable rationale according to policy | ACCEPTED |
| DEC-152 | Submitted review reports are locked and only reopened through controlled audited workflow | ACCEPTED |
| DEC-153 | Reviewer assignment history is preserved rather than silently deleted | ACCEPTED |
| DEC-154 | Confidential editor comments never become author-facing output | ACCEPTED |
| DEC-155 | Decision Authority/Reviewer conflict on own paper blocks review/decision authority | ACCEPTED |
| DEC-156 | Academic decision corrections use controlled supersession rather than silent overwrite | ACCEPTED |
| DEC-157 | Event Operations is edition-scoped event authority while Session Chair/Moderator are session-scoped | ACCEPTED |
| DEC-158 | Event roles receive operationally necessary/derived data rather than raw Finance or confidential academic data by default | ACCEPTED |
| DEC-159 | Published schedule changes are controlled and auditable | ACCEPTED |
| DEC-160 | Attendance/check-in and presentation status are separate | ACCEPTED |
| DEC-161 | Self/QR check-in never creates authoritative PRESENTED status | ACCEPTED |
| DEC-162 | Event Operations and Session Chair may verify PRESENTED/NO_SHOW within scope; Moderator verification is configurable | ACCEPTED |
| DEC-163 | Presentation verification is auditable and separate from exception/makeup approval | ACCEPTED |
| DEC-164 | Presenter substitutions and post-event presenter corrections are traceable/controlled | ACCEPTED |
| DEC-165 | Event actors cannot normally verify their own presentation | ACCEPTED |
| DEC-166 | Event status authority does not imply Academic/Publication/Certificate issuance authority | ACCEPTED |
| DEC-167 | Publication Team/Proceeding Editor is edition-scoped publication-operations authority | ACCEPTED |
| DEC-168 | PUBLICATION_APPROVED, PUBLICATION_ELIGIBLE, and PUBLISHED remain distinct states/authorities | ACCEPTED |
| DEC-169 | Publication Team does not automatically receive Academic Decision Authority and cannot bypass review/eligibility gates | ACCEPTED |
| DEC-170 | Publication Team processes only submissions that have satisfied required publication eligibility | ACCEPTED |
| DEC-171 | Publication Team consumes derived Finance/eligibility state rather than raw Finance evidence by default | ACCEPTED |
| DEC-172 | Final publication metadata is snapshotted before/at handoff | ACCEPTED |
| DEC-173 | Substantive authorship/manuscript changes after protected stages require controlled correction/authority | ACCEPTED |
| DEC-174 | V1 OJS/proceedings handoff is manual/assisted and operated by Publication Team | ACCEPTED |
| DEC-175 | External OJS/DOI/URL/ISBN/ISSN references never become internal primary keys | ACCEPTED |
| DEC-176 | Publication transfer/status changes are auditable and must reflect known downstream fact | ACCEPTED |
| DEC-177 | Downstream publication failure/withdrawal preserves legitimate conference/presentation/certificate history | ACCEPTED |
| DEC-178 | Publication Team cannot bypass academic/publication gates on its own paper | ACCEPTED |
| DEC-179 | Publication records are corrected through controlled audited history, not free hard delete | ACCEPTED |
| DEC-180 | V1 may combine Publication Team/Proceeding Editor while remaining ready for future publication sub-roles | ACCEPTED |
| DEC-181 | Certificate functions are capability-based and are not automatically inherited from a broad administrator role | ACCEPTED |
| DEC-182 | Rule-based and manual individual/bulk certificate issuance are both supported | ACCEPTED |
| DEC-183 | Certificate display date is activity/event date while technical creation/generation timestamps remain truthful internal audit data | ACCEPTED |
| DEC-184 | Each certificate receives an individual record/identifier/token/link, including bulk issuance | ACCEPTED |
| DEC-185 | Public verification and QR expose/resolve only minimum credential information | ACCEPTED |
| DEC-186 | Issued certificates use controlled revoke/supersede/reissue rather than free edit/hard delete | ACCEPTED |
| DEC-187 | Manual certificate issuance never silently rewrites event/academic/publication state | ACCEPTED |
| DEC-188 | Privileged manual certificate self-issuance is denied by default | ACCEPTED |
| DEC-189 | Edition closeout is checklist-driven and may coexist with continuing downstream publication work | ACCEPTED |
| DEC-190 | ARCHIVED editions are read-only by default | ACCEPTED |
| DEC-191 | Narrow post-archive certificate/publication-reference/historical corrections may occur without reopening the whole edition | ACCEPTED |
| DEC-192 | Historical correction is a dedicated audited capability rather than unrestricted archive editing | ACCEPTED |
| DEC-193 | Unarchive is an exceptional privileged audited operation | ACCEPTED |
| DEC-194 | Archive preserves lifecycle records and is never equivalent to deletion | ACCEPTED |
| DEC-195 | Role/capability assignments are scoped auditable records distinct from resource assignments | ACCEPTED |
| DEC-196 | Temporary/expiring authority is supported while historical attribution is preserved | ACCEPTED |
| DEC-197 | Revocation removes future authority without deleting or reassigning historical attribution | ACCEPTED |
| DEC-198 | Protected roles/capabilities cannot be self-assigned through normal administration | ACCEPTED |
| DEC-199 | V1 protected assignment uses authorized assigner + anti-self-assignment + audit; universal dual approval is not required | ACCEPTED |
| DEC-200 | COI is a generic cross-domain restriction layer and explicit restriction wins over role allow | ACCEPTED |
| DEC-201 | Direct self-conflict blocks Reviewer, Academic Decision, Finance, Event verification, and privileged manual certificate actions | ACCEPTED |
| DEC-202 | Overrides are domain-specific; there is no universal override-everything authority | ACCEPTED |
| DEC-203 | Overrides preserve before/after state, reason, actor, timestamp, and original factual history | ACCEPTED |
| DEC-204 | Break-glass is separate from business override and reserved for serious technical/security incidents | ACCEPTED |
| DEC-205 | Break-glass is temporary, scoped, reasoned, fully auditable, and never permanent | ACCEPTED |
| DEC-206 | Silent impersonation/account takeover is denied; V1 does not require impersonation | ACCEPTED |
| DEC-207 | Delegation revocation preserves historical attribution while removing future permission | ACCEPTED |
| DEC-208 | Role-holder replacement never rewrites prior actor attribution | ACCEPTED |
| DEC-209 | Sensitive-role revocation must cease new authorization immediately at product-policy level | ACCEPTED |
| DEC-210 | Active dependency warnings support revocation handoff but do not block emergency revocation | ACCEPTED |
| DEC-211 | Assignment, revocation, expiry, extension, scope changes, and reassignment are first-class audit events | ACCEPTED |
| DEC-212 | Front Office is edition-scoped support: safe-status visibility with no automatic business-domain authority | ACCEPTED |
| DEC-213 | Front Office has no default raw Finance/confidential-review access and no impersonation/account-takeover authority | ACCEPTED |
| DEC-214 | Administrative screening is a distinct submission.admin_screen capability and is not academic judgment | ACCEPTED |
| DEC-215 | Official-submission withdrawal separates request from authorized approval and preserves history | ACCEPTED |
| DEC-216 | Post-submission contributor/authorship correction requires stage-aware protected approval and before/after audit | ACCEPTED |
| DEC-217 | Publication eligibility normally derives from authoritative facts + edition policy, not discretionary operator mutation | ACCEPTED |
| DEC-218 | Publication Team may remediate its own-domain blockers but cannot rewrite Finance/Academic/Event source facts | ACCEPTED |
| DEC-219 | publication.eligibility.override is protected/domain-specific and preserves the original blocking facts | ACCEPTED |
| DEC-220 | Final permission matrix is canonical for Phase 0 permission requirements | ACCEPTED |
| DEC-221 | Full permission consistency audit is GREEN and REQ-PERM-001 is complete at product-requirement level | ACCEPTED |

Statuses: PROPOSED, ANALYZED, ACCEPTED DIRECTION, ACCEPTED, REJECTED, SUPERSEDED, OPEN.


| DEC-222 | Production requires HTTPS/secure transport and server-side authorization on every protected request | ACCEPTED |
| DEC-223 | MFA is mandatory for high-risk internal authorities; Participant MFA is not mandatory in V1 and Reviewer MFA is configurable | ACCEPTED |
| DEC-224 | Sensitive authority/security actions may require re-authentication/step-up verification | ACCEPTED |
| DEC-225 | Revoked authority must cease authorizing protected actions immediately at the next server authorization check | ACCEPTED |
| DEC-226 | Authentication/verification/recovery endpoints require abuse controls and must avoid unnecessary account enumeration | ACCEPTED |
| DEC-227 | Magic-link/OTP tokens are unpredictable, short-lived, single-use, bound to intended action/account, and excluded from plaintext logs/analytics | ACCEPTED |
| DEC-228 | Uploaded files are untrusted; secrets never belong in repo/client bundles/plaintext logs; production errors must not expose internals | ACCEPTED |
| DEC-229 | Security-sensitive account/access/override/break-glass events are auditable | ACCEPTED |
| DEC-230 | Silent impersonation/account takeover remains prohibited and is not required for V1 | ACCEPTED |
| DEC-231 | Phase 0 locks security behavior, not specific authentication/MFA/session libraries or providers | ACCEPTED |

| DEC-232 | Privacy treatment is sensitivity/purpose-based; the product collects only data needed for defined conference purposes | ACCEPTED |
| DEC-233 | Current-profile changes never silently rewrite protected historical snapshots | ACCEPTED |
| DEC-234 | Unpublished manuscripts, financial evidence, reviewer data, and audit/security data are private by default | ACCEPTED |
| DEC-235 | Reviewer anonymity/confidentiality applies across all technical and communication surfaces, not only UI | ACCEPTED |
| DEC-236 | Finance-restricted data remains isolated from unrelated operational/support/academic/event/publication surfaces | ACCEPTED |
| DEC-237 | Notifications and third-party integrations use minimum necessary data; authenticated workspace is preferred for sensitive detail | ACCEPTED |
| DEC-238 | Public URLs/logs must not expose unnecessary PII, secrets, authentication tokens, or raw restricted content | ACCEPTED |
| DEC-239 | Export/API access cannot bypass UI authorization boundaries and sensitive bulk export may be audited | ACCEPTED |
| DEC-240 | Data retention is purpose/data-class specific; exact retention periods remain deferred until policy/legal basis is available | ACCEPTED |
| DEC-241 | Account closure is distinct from erasure of historical scholarly/financial/certificate/publication/audit facts | ACCEPTED |
| DEC-242 | Test/staging/demo/backup handling remains subject to production-equivalent privacy boundaries; synthetic/redacted test data is preferred | ACCEPTED |

| DEC-243 | V1 production availability objective is ≥99.5% monthly outside announced scheduled maintenance | ACCEPTED |
| DEC-244 | Planned maintenance is avoided during conference critical windows and deadlines | ACCEPTED |
| DEC-245 | External-service outages must not cause authoritative core-data loss or rollback of established business truth | ACCEPTED |
| DEC-246 | Critical state transitions are consistency-safe and sensitive operations are duplicate-safe/idempotent | ACCEPTED |
| DEC-247 | Backup/recovery scope includes database plus required private files/generated artifacts and recovery-critical state | ACCEPTED |
| DEC-248 | Normal RPO is ≤4h and critical-window target RPO is ≤1h where reasonably supported | ACCEPTED |
| DEC-249 | Normal RTO is ≤4h and critical-window target RTO is ≤2h | ACCEPTED |
| DEC-250 | Production backups are automated, monitored, failure-alerted, and restore-tested | ACCEPTED |
| DEC-251 | Restore verification occurs before first production launch, before each major edition, and periodically; quarterly is the active-production target | ACCEPTED |
| DEC-252 | Backup storage must be failure-domain separated and retain source-data privacy/security classification | ACCEPTED |
| DEC-253 | Database↔file/artifact integrity must be verifiable | ACCEPTED |
| DEC-254 | Integration/notification retries are safe; delivery failure does not change authoritative business state | ACCEPTED |
| DEC-255 | The platform should degrade gracefully and support controlled maintenance instead of cascading failure | ACCEPTED |
| DEC-256 | Conference-day continuity pack and auditable post-outage reconciliation are required operational fallback capabilities | ACCEPTED |
| DEC-257 | Data integrity/correctness has priority over accepting unsafe transactions during degraded conditions | ACCEPTED |

| DEC-258 | Common interactive production requests target ≤2s with a normal upper expectation of ≤3s | ACCEPTED |
| DEC-259 | V1 performance baseline supports at least 50 concurrent active users on common workflows | ACCEPTED |
| DEC-260 | Deadline bursts must remain duplicate-safe and data-integrity safe | ACCEPTED |
| DEC-261 | Large lists are bounded/paginated and operational queries are edition-scoped | ACCEPTED |
| DEC-262 | Search/filter is server-side and index-ready; dedicated search infrastructure is not required for V1 | ACCEPTED |
| DEC-263 | Heavy operations are asynchronous-capable and bulk workflows are batch/job-ready | ACCEPTED |
| DEC-264 | Upload handling exposes progress/failure/retry, uses configurable limits, and avoids unbounded application-memory use | ACCEPTED |
| DEC-265 | Cache is optimization only and never the authoritative business source of truth | ACCEPTED |
| DEC-266 | Microservices are not required for V1; architecture may remain a modular monolith/single application if approved NFRs are met | ACCEPTED |
| DEC-267 | Scaling is incremental/evidence-driven rather than premature distributed-system complexity | ACCEPTED |
| DEC-268 | Performance observability and pre-production/major-release regression sanity checks are required | ACCEPTED |
| DEC-269 | Slow external delivery must not unnecessarily block authoritative business transactions | ACCEPTED |

| DEC-270 | Business audit trail and technical application logs are separate concerns | ACCEPTED |
| DEC-271 | Business audit is append-only/immutable in normal workflow; corrections create new events rather than rewriting audit history | ACCEPTED |
| DEC-272 | Sensitive business/security/permission/override/archive/certificate actions and exceptional restricted-data access are auditable | ACCEPTED |
| DEC-273 | Technical logging is structured, correlation-ID capable, severity-aware, and privacy-redacted | ACCEPTED |
| DEC-274 | Application/database/storage/critical-job health plus latency/error/job/queue/database/integration signals are observable | ACCEPTED |
| DEC-275 | Backup freshness/failure belongs to operational monitoring | ACCEPTED |
| DEC-276 | Alerts are actionable/severity-based and may be more sensitive during edition critical windows | ACCEPTED |
| DEC-277 | Authorized audit views are searchable; sensitive audit exports are restricted and may themselves be audited | ACCEPTED |
| DEC-278 | Business-audit retention and technical-log retention are separate; exact periods remain deferred | ACCEPTED |
| DEC-279 | Technical logs use bounded retention/rotation to avoid exhausting storage | ACCEPTED |
| DEC-280 | Internal timestamps are consistent/unambiguous; display timezone may be converted for user/edition context | ACCEPTED |
| DEC-281 | User-facing error references correlate to internal diagnostics without exposing technical internals | ACCEPTED |
| DEC-282 | Production deployments are traceable and incident timelines should be reconstructable across requests/jobs/integrations | ACCEPTED |
| DEC-283 | Monitoring is an observability layer, not business source of truth or a required dependency for core transaction success | ACCEPTED |
| DEC-284 | Audit/observability behavior must be verified before production through representative failure and protected-action scenarios | ACCEPTED |

| DEC-285 | Authoritative file replacement creates a new version/evidence record instead of silently overwriting prior evidence | ACCEPTED |
| DEC-286 | File versions already used for review/decision/publication or other authoritative workflows are immutable in normal workflow | ACCEPTED |
| DEC-287 | Internal file identity/storage keys are opaque and separate from original filenames; filenames/paths are application-controlled and sanitized | ACCEPTED |
| DEC-288 | File validation must consider content/type policy rather than trusting extension alone, and upload architecture remains scanner/quarantine-ready | ACCEPTED |
| DEC-289 | Protected files require authorized access and double-anonymous reviewer packets are isolated from identity-bearing originals | ACCEPTED |
| DEC-290 | Important stored files support integrity fingerprint/checksum verification | ACCEPTED |
| DEC-291 | Database↔storage partial-failure states must be detectable/reconcilable and orphan cleanup must be reference-safe | ACCEPTED |
| DEC-292 | Temporary artifacts are distinguished from authoritative artifacts and may have shorter retention | ACCEPTED |
| DEC-293 | Certificates, LoA, decision letters, and other important generated documents preserve provenance plus relevant template/version/snapshot context | ACCEPTED |
| DEC-294 | Current-template changes do not silently rewrite historical generated documents | ACCEPTED |
| DEC-295 | File metadata preserves enough identity/version/uploader/time/integrity/validation context for traceability | ACCEPTED |
| DEC-296 | File retention follows resource purpose/history/privacy policy and authoritative evidence files are not freely hard-deleted | ACCEPTED |
| DEC-297 | Storage failures fail safely and never report successful persistence before durable storage confirmation | ACCEPTED |
| DEC-298 | Backup/restore supports post-recovery file-integrity verification | ACCEPTED |
| DEC-299 | Exceptional restricted-file access is auditable where applicable and exports/publication packages bind to authoritative approved versions | ACCEPTED |

| DEC-300 | WCAG 2.2 Level AA is the V1 accessibility target | ACCEPTED |
| DEC-301 | Participant/Author experience is mobile-first; ordinary critical participant workflows must not be desktop-only | ACCEPTED |
| DEC-302 | Back-office workspaces are desktop-first but responsive, while Event Operations must be usable on tablet/mobile | ACCEPTED |
| DEC-303 | Core workflows are keyboard-operable with visible focus, no keyboard traps, and semantic accessible component behavior | ACCEPTED |
| DEC-304 | Status/progress/errors are not color-only and must be exposed accessibly | ACCEPTED |
| DEC-305 | Forms use persistent labels, field-specific errors, and preserve entered data after validation failure | ACCEPTED |
| DEC-306 | Long-form work uses draft/autosave/recovery safeguards appropriate to the feature | ACCEPTED |
| DEC-307 | High-impact actions use proportional confirmation that explains consequences | ACCEPTED |
| DEC-308 | User-facing workflow status prioritizes understandable current-state and next-action guidance over raw internal enums | ACCEPTED |
| DEC-309 | Deadline/schedule display uses absolute date/time and timezone where ambiguity is possible | ACCEPTED |
| DEC-310 | Authoritative success is shown only after server-confirmed completion; loading state should prevent accidental duplicate actions where necessary | ACCEPTED |
| DEC-311 | Responsive design supports reflow/zoom, avoids unnecessary horizontal scrolling, and respects reduced-motion preference | ACCEPTED |
| DEC-312 | Accessibility/usability quality applies equally across id/en/ar including Arabic RTL | ACCEPTED |
| DEC-313 | Accessibility UAT covers representative devices, keyboard, zoom/reflow, semantic/screen-reader sanity, and RTL | ACCEPTED |

| DEC-314 | V1 UI locales are id/en/ar and Arabic is a first-class RTL experience | ACCEPTED |
| DEC-315 | Translatable UI copy comes from localization resources rather than scattered hardcoded strings | ACCEPTED |
| DEC-316 | Locale fallback is requested locale → English → controlled missing-translation handling | ACCEPTED |
| DEC-317 | Critical id/en/ar translation completeness is a release/UAT gate and missing keys are observable | ACCEPTED |
| DEC-318 | RTL is full layout direction, not merely text-align:right, while directional/non-directional icon behavior is semantic | ACCEPTED |
| DEC-319 | Mixed Arabic/Latin content requires explicit BiDi-safe handling for email/URL/DOI/ORCID/codes/identifiers | ACCEPTED |
| DEC-320 | UI locale is separate from scholarly-content language; interface changes never silently translate/mutate paper metadata | ACCEPTED |
| DEC-321 | Multilingual scholarly metadata is supported/configurable but not globally mandatory for every V1 submission | ACCEPTED |
| DEC-322 | CMS/public edition content is locale-aware/translatable with explicit translation availability/fallback | ACCEPTED |
| DEC-323 | Date/time/number formatting is locale-aware, while currency follows edition policy and timezone follows edition/user context | ACCEPTED |
| DEC-324 | Critical validation/status/confirmation/error text and notifications are locale-aware with deterministic fallback | ACCEPTED |
| DEC-325 | Generated-document language is explicit and independent of current UI locale; provenance records language/template/version context | ACCEPTED |
| DEC-326 | Text storage/search is full-Unicode safe; naming models do not force a Western first/middle/last-only structure | ACCEPTED |
| DEC-327 | The system does not silently auto-transliterate personal/scholarly identity | ACCEPTED |
| DEC-328 | Locale switching preserves current resource context and does not mutate authoritative data | ACCEPTED |
| DEC-329 | Authenticated user locale preference is supported while public language switching remains explicit | ACCEPTED |
| DEC-330 | Arabic RTL receives dedicated UAT and must be tested together with accessibility requirements | ACCEPTED |