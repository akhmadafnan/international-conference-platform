# Product Decision Register — Current Product Baseline

**ID:** ICHES-DECISION-REGISTER  
**Status:** ACTIVE — PRODUCT BLUEPRINT v1  
**Updated:** 2026-10-23

This register records current Product Discovery and accelerated V1 decisions.

> **ICHES 2027 amendment:** [Operational Rebaseline, 2026-10-23](ICHES_2027_OPERATIONAL_REBASELINE_2026_10_23.md) is authoritative for its stated Edition scope. PD-085–PD-096 are accepted product decisions, not implementation completion or automatic coding/merge authorization. Earlier IDs and rationale remain traceable; PD-017 mandatory screening is superseded for the 2027 ordinary flow, and PD-062 configurable reviewer count is replaced by exclusive one-active-reviewer responsibility per paper/stage for ICHES 2027.

Statuses:
- ACCEPTED — concept accepted by Product Owner;
- ASSUMED_V1 — Product Owner authorized the rational/simple default for accelerated planning; does not claim external Warek signature;
- CANDIDATE — intentionally not yet frozen.

| ID | Decision | Status |
|---|---|---|
| PD-001 | Product is a reusable multi-edition Conference & Event Experience Platform | ACCEPTED |
| PD-002 | Core UX is simple CRUD + smart personal/state-aware experience | ACCEPTED |
| PD-003 | Public UI locales are id/en/ar; Arabic RTL is first-class | ACCEPTED |
| PD-004 | Conference Series and Edition are separate; host is edition-configurable | ACCEPTED |
| PD-005 | Participation is selected through edition-configurable package presets | ACCEPTED |
| PD-006 | Presenter/author path does not pay before abstract review; ACCEPT creates the payment obligation. Participant-only users pay through the participant registration path | ACCEPTED |
| PD-007 | Manual bank transfer + proof + Finance verification is V1 payment model | ACCEPTED |
| PD-008 | Finance verification confirms registration and unlocks Event Pass/QR; it does not create academic acceptance or LoA | ACCEPTED |
| PD-009 | QR is participant lookup identity; scan alone is not attendance | ACCEPTED |
| PD-010 | Presenter is not chosen as registration role; academic path begins through abstract submission | ACCEPTED |
| PD-011 | Abstract submission UX is OJS-inspired but simpler | ACCEPTED |
| PD-012 | Submission language is independent from UI language | ACCEPTED |
| PD-013 | ORCID is optional | ACCEPTED |
| PD-014 | Affiliation is ROR-first with manual fallback | ACCEPTED |
| PD-015 | Contributors may exist without application accounts | ACCEPTED |
| PD-016 | References are ordered canonical scholarly metadata with raw citation preserved | ACCEPTED |
| PD-017 | Administrative screening precedes abstract review | ACCEPTED historically; SUPERSEDED FOR THE 2027 OPERATIONAL MODEL by PD-089 |
| PD-018 | Abstract review defaults to single-anonymous | ACCEPTED |
| PD-019 | Reviewer recommendation is advisory; authorized Academic authority decides | ACCEPTED |
| PD-020 | Presentation LoA is issued after abstract acceptance | ACCEPTED |
| PD-021 | LoA means accepted for presentation, not room assignment or publication | ACCEPTED |
| PD-022 | Full Article upload opens after acceptance/LoA | ACCEPTED |
| PD-023 | Only Full Article submitted papers enter Scheduling Pool | ACCEPTED |
| PD-024 | Actual presenter is explicitly confirmed from contributors | ACCEPTED |
| PD-025 | Scheduling supports bulk assignment of Session/Room/Reviewer/Moderator/Slot | ACCEPTED |
| PD-026 | Draft schedule is internal; participant sees published schedule only | ACCEPTED |
| PD-027 | Reviewer sees only assigned sessions/papers | ACCEPTED |
| PD-028 | Moderator/Event Operations records PRESENTED/NO_SHOW | ACCEPTED |
| PD-029 | Reviewer records academic presentation assessment | ACCEPTED |
| PD-030 | Attendance and PRESENTED are separate facts | ACCEPTED |
| PD-031 | No Revision does not create an extra manuscript upload | ACCEPTED |
| PD-032 | Revision Required creates a new versioned Revised Article upload | ACCEPTED |
| PD-033 | Best Article and Best Presenter are native awards | ACCEPTED |
| PD-034 | System scores/candidates are evidence; Committee owns final award decision | ACCEPTED |
| PD-035 | Committee may disregard or replace system candidates | ACCEPTED |
| PD-036 | Best Presenter is Overall edition award for accelerated V1 | ASSUMED_V1 |
| PD-037 | Best Article certificate is issued individually to all listed authors | ACCEPTED |
| PD-038 | Presenter may receive both Participant and Presenter certificates | ACCEPTED |
| PD-039 | Committee/Appreciation certificates are supported | ACCEPTED |
| PD-040 | Certificate Eligibility != Generated != Issued | ACCEPTED |
| PD-041 | Certificates use unique verification identity, QR, revoke/reissue, immutable snapshot | ACCEPTED |
| PD-042 | Dashboard is source of truth; email is personalized notification | ACCEPTED |
| PD-043 | Document Center consolidates personal and edition documents | ACCEPTED |
| PD-044 | Schedule is database data, not merely a PDF | ACCEPTED |
| PD-045 | Presentation slides are edition-configurable | ACCEPTED |
| PD-046 | Publication destination is Proceedings or Selected Journal | ACCEPTED |
| PD-047 | Author does not freely choose publication destination | ACCEPTED |
| PD-048 | Selected for Journal does not mean Accepted by Journal | ACCEPTED |
| PD-049 | Conference platform stops at publication-ready/handoff rather than becoming OJS | ACCEPTED |
| PD-050 | Canonical scholarly metadata feeds OJS-oriented export/handoff. Direct Crossref deposit and DOI registration are downstream publishing responsibilities in accelerated V1 | ACCEPTED |
| PD-051 | Finalize for Production creates protected publication snapshot | ACCEPTED |
| PD-052 | Metadata readiness uses READY/WARNING/BLOCKED, not synthetic score | ACCEPTED |
| PD-053 | Public frontend and authenticated dashboard are one coherent product journey | ACCEPTED |
| PD-054 | Dashboard centers on Next Action and hides irrelevant menus | ACCEPTED |
| PD-055 | One account can hold multiple scoped functions without account switching | ACCEPTED |
| PD-056 | Community Service remains lightweight CRUD, not mini-KKN | ACCEPTED |
| PD-057 | Evening/MoU remains lightweight CRUD, not contract management | ACCEPTED |
| PD-058 | Laravel 13 official Vue Starter Kit + Vue 3 + TypeScript + Inertia 3 + Tailwind CSS 4 + shadcn-vue is the frozen frontend stack | ACCEPTED |
| PD-059 | Rejected abstract creates no presenter payment obligation. The user may continue as participant-only and pay through the participant path when permitted | ACCEPTED |
| PD-060 | Participation packages are fixed choices but edition-configurable | ASSUMED_V1 |
| PD-061 | Fee model is Package + optional simple Participant Category | ASSUMED_V1 |
| PD-062 | Abstract reviewer count defaults to 1 and remains configurable | ASSUMED_V1 outside designated 2027 scope; superseded for ICHES 2027 by PD-090 |
| PD-063 | Abstract review uses one normal revision cycle with authorized exception | ASSUMED_V1 |
| PD-064 | Slides are optional/configurable and not a V1 blocker | ASSUMED_V1 |
| PD-065 | Accelerated first V1 is offline-first; edition mode remains conceptually configurable | ASSUMED_V1 |
| PD-066 | Proceedings is default publication destination; Selected Journal is an authorized override | ASSUMED_V1 |
| PD-067 | Finance, Academic, Event, Award, Publication and Certificate authorities are separated | ASSUMED_V1 |
| PD-068 | Proceedings may use Publication Acceptance; Selected Journal uses Selection/Handoff until journal acceptance | ASSUMED_V1 |
| PD-069 | Admin/backoffice adopts shadcn-admin-like interaction language implemented with shadcn-vue; not the React template architecture | ACCEPTED |
| PD-070 | Public/participant frontend may be developed in parallel using typed mock scenarios without changing domain rules | ACCEPTED |
| PD-071 | LeConfe is a mature-platform benchmark only; no fork, runtime integration, or roadmap replacement is authorized | ACCEPTED |
| PD-072 | Payment destinations/bank accounts are Edition-configurable; package may use the Edition default or an explicit destination | ACCEPTED |
| PD-073 | Payment obligation snapshots amount, currency, package context, and participant-visible destination details | ACCEPTED |
| PD-074 | Account number on participant payment instruction supports one-action copy UX | ACCEPTED |
| PD-075 | Operational workflow windows/deadlines are configurable separately from public Important Dates | ACCEPTED |
| PD-076 | V1 CMS supports bounded localized custom pages and configurable navigation without a generic plugin/page-builder engine | ACCEPTED |
| PD-077 | Final Approved Manuscript is the publication-authoritative file after revision/Final ACC; newest upload alone is never sufficient | ACCEPTED |
| PD-078 | ICHES supports single and bulk OJS production bundles derived from immutable publication snapshots and readiness guards | ACCEPTED |
| PD-079 | OJS/publisher is the system of record for copyediting, final publication, DOI and Crossref operations; ICHES may record downstream identifiers/results | ACCEPTED |
| PD-080 | Public SEO/sitemap/discoverability is an ICHES responsibility, but scholarly Google Scholar publication indexing remains a downstream publication concern when OJS hosts the final article | ACCEPTED |
| PD-081 | Participation Package billing mode is explicit FREE or PAID; FREE creates no Payment row, proof-upload requirement, or Finance verification requirement | ACCEPTED |
| PD-082 | A normally PAID Registration may receive an explicit audited fee exemption/complimentary decision from an authorized actor | ACCEPTED |
| PD-083 | Free/waived financial resolution is a business fact distinct from VERIFIED payment; configuration changes never silently rewrite resolved financial history | ACCEPTED |
| PD-084 | Workflow-window enforcement uses Edition-scoped `edition_workflow_windows`: missing or inactive rows impose no restriction; null bounds are open-ended; active bounds are inclusive; active `opens_at > closes_at` is invalid and must fail explicitly. `REGISTRATION` gates Registration creation. `ACCEPTED_AUTHOR_PAYMENT` is reserved for the accepted-author/presenter path and is not used for participant-only payment | ACCEPTED |
| PD-085 | Official Edition identity is The 6th International Conference on Humanity, Education, and Society (ICHES 2027); academic theme is Islamic Values in a Changing World: Education, Accountability, and Social Resilience for Inclusive Development; promotional theme is Islamic Values in a Changing World. Series/Edition remain separate and 2027 specifics are not universal hardcoded obligations | ACCEPTED |
| PD-086 | Official 2027 timeline: abstracts 1–20 December 2026; acceptance announcement 22 December; Full Papers 23–28 December; Registration/Presenter Payment 24–28 December; event 5 January 2027; proceedings publication target 28 January 2027. Journal editorial timelines are independent; internal recommendations are not new official dates | ACCEPTED |
| PD-087 | ICHES 2027 uses the eight T01–T08 Topics exactly as listed in the operational rebaseline; one primary Topic is required on abstract submission, Edition-configurable. Topic interest/competency is separate from authorization; no per-Topic Spatie Reviewer roles | ACCEPTED |
| PD-088 | One abstract per submitting owner per Edition across all states. Owned DRAFT without academic evidence may be edited/deleted; official submissions cannot be freely edited/deleted or followed by a second submission. Corrections, Full Paper, revisions and later stages retain the same Submission ID and immutable version/history. Co-authorship is unaffected | ACCEPTED |
| PD-089 | Technically valid SUBMITTED abstracts automatically enter the Topic Review Pool. Mandatory per-paper Administrative Screening PASS and reviewer-email assignment are replaced for the ordinary 2027 workflow. Technical/scope exceptions remain reasoned and auditable without a routine Admin gate; supersedes PD-017 for this scope | ACCEPTED |
| PD-090 | Relevant reviewers declare COI and atomically Self-Claim an unclaimed abstract. Exactly one exclusive active reviewer per paper per academic stage; concurrent claims cannot create duplicate owners. Only authorized Academic Committee releases/reassigns abstracts with audit. Preserve single-anonymous, scoped private access, final report locks and advisory recommendations; supersedes PD-062 configurable count for ICHES 2027 | ACCEPTED |
| PD-091 | Abstract reviews run progressively during the submission window; internal decisions may be prepared early. Author-facing official results and Presentation LoA follow the 22 December 2026 announcement/release policy; no premature exposure of unreleased decisions or LoA. Academic Decision Authority retains final acceptance | ACCEPTED |
| PD-092 | SC approves Room/Topic/Session/Presenter/Paper/Reviewer/schedule mapping; authorized Academic/Event operators enter/import it with bulk preview-before-apply. My Rooms lists assigned papers in schedule order. Preserve abstract-reviewer continuity unless SC reassigns the Full Paper/presentation stage; partition multi-reviewer room papers. Audit actor/time/old-new reviewer/paper-stage/SC reason-source; preserve prior evidence. No special SC role solely for policy-making | ACCEPTED |
| PD-093 | Accepted Author uploads private versioned Full Paper on the existing Submission ID during 23–28 December; presenter payment follows ACCEPT in its separate 24–28 December window. The later payment opening must not block Full Paper upload on 23 December. Academic, Finance and presentation entitlements remain separate | ACCEPTED |
| PD-094 | Deep Full Paper review occurs after presentation, not as a prerequisite to the 5 January presentation. Attendance, PRESENTED/NO_SHOW, review and final decision remain distinct. Advisory outcomes: NO_REVISION_REQUIRED, MINOR_REVISION_REQUIRED, MAJOR_REVISION_REQUIRED, NOT_RECOMMENDED. No revision requires no extra upload; required revision is versioned and Academic/Publication Authority completes Final ACC/approved-manuscript validation | ACCEPTED |
| PD-095 | Revision outcome and publication destination are separate. Reviewer may recommend a destination/candidate journal; SC / authorized Academic or Publication Authority controls PROCEEDINGS default or SELECTED_JOURNAL override. Final ACC/readiness precede handoff; LoA is not publication acceptance, Selected Journal is not Journal Accepted, READY_FOR_PRODUCTION is not PUBLISHED. Journal/OJS owns downstream acceptance, DOI and publication; prevent misleading claims and duplicate publication | ACCEPTED |
| PD-096 | The ten proposed journal candidates and SINTA tiers in operational rebaseline section 12 are recorded as Product Owner-supplied, not independently verified. Identity/title/ISSN/accreditation validity/OJS URL/partnership confirmation/slots remain pending for every entry; no fabricated identifiers or confirmed acceptance agreements | ACCEPTED |

### ICHES 2027 supersession rationale — accepted 2026-10-23

PD-017 recorded the earlier routine screening sequence; PD-062 recorded a configurable reviewer-count default. The Product Owner replaces those ordinary 2027 gates with technically valid Topic Pool routing, COI-aware exclusive Self-Claim and stage-specific SC conference mapping. Administrative exceptions and authorized reassignment remain auditable. Earlier single-anonymous, human final authority, immutable evidence and payment-after-ACCEPT principles remain binding. These changes are Edition-scoped and preserve the historical rationale outside the superseded scope. See the [operational document](ICHES_2027_OPERATIONAL_REBASELINE_2026_10_23.md) and [bounded roadmap](../project-management/ICHES_2027_EXECUTION_ROADMAP.md).

## META-001 — Scholarly Metadata Decisions

| ID | Decision | Status |
|---|---|---|
| META-001 | Canonical internal metadata is independent from OJS/Crossref target schemas | ACCEPTED |
| META-002 | One Submission identity persists from abstract through publication | ACCEPTED |
| META-003 | UI locale and scholarly content locale are independent | ACCEPTED |
| META-004 | Primary scholarly locale required; other scholarly translations optional | ACCEPTED |
| META-005 | Keywords are structured individual ordered values | ACCEPTED |
| META-006 | Contributors need not have User accounts | ACCEPTED |
| META-007 | Exactly one corresponding author is required before final submission | ACCEPTED |
| META-008 | Canonical model supports legitimate single-name authors without inventing surname | ACCEPTED |
| META-009 | ORCID is optional; missing ORCID is not a blocker | ACCEPTED |
| META-010 | ORCID verification state distinguishes typed/unverified from authenticated | ACCEPTED |
| META-011 | Contributors may have multiple affiliations | ACCEPTED |
| META-012 | ROR-first with truthful manual fallback | ACCEPTED |
| META-013 | Department/faculty is separate from the ROR organization identity | ACCEPTED |
| META-014 | Raw citation is always preserved; structured reference enrichment is optional/non-destructive | ACCEPTED |
| META-015 | Manuscript files are immutable/versioned; revisions never overwrite prior versions | ACCEPTED |
| META-016 | Finalize for Production creates an immutable publication snapshot | ACCEPTED |
| META-017 | Post-finalization correction creates a superseding snapshot/version with audit | ACCEPTED |
| META-018 | External identifiers use generic scheme/value relationships and never become internal primary keys | ACCEPTED |
| META-019 | OJS adapter is destination-profile/version aware, not one universal XML assumption | ACCEPTED |
| META-020 | Direct Crossref proceedings deposit is not an accelerated-V1 ICHES responsibility; OJS/publisher owns DOI/Crossref publication operations | ACCEPTED |
| META-021 | General production readiness and OJS handoff readiness are explicit gates; downstream publisher-specific deposit readiness is outside accelerated V1 | ACCEPTED |
| META-022 | Metadata readiness vocabulary is READY / WARNING / BLOCKED | ACCEPTED |
| META-023 | ROR-backed affiliation uses the institution/organization as canonical affiliation identity; faculty/department/study program is optional subdivision metadata only | ACCEPTED |
| META-024 | Final Approved Manuscript must be explicitly identified by the finalized publication snapshot after Final ACC | ACCEPTED |
| META-025 | OJS production export is generated from canonical immutable metadata; target transformation never rewrites canonical data | ACCEPTED |
| META-026 | Production bundle may include metadata, final manuscript, approved supplementary files and manifest, but excludes confidential review/internal notes | ACCEPTED |
| META-027 | Downstream DOI/OJS publication IDs/URLs may be recorded as external identifiers after handoff/publication | ACCEPTED |
| META-028 | For ICHES 2027, the primary scholarly locale requires at least 3 and at most 5 ordered keywords; other Editions may configure different minimum/maximum values | ACCEPTED |
| META-029 | For ICHES 2027, a valid active same-Edition Track is required when that Edition uses Tracks; Track requirements are configurable per Edition | ACCEPTED |
| META-030 | For ICHES 2027, the abstract manuscript attachment is optional; PDF/DOCX may be uploaded through private immutable versioning; other Editions may require an abstract file | ACCEPTED |
| META-031 | In initial V1, contributors must provide at least one valid affiliation; the independent/no-institution exception is deferred until explicitly modeled and separately authorized, rather than inferred from an empty affiliation list | ACCEPTED |
| META-032 | Abstract submission readiness policies belong to the Conference Edition's `ConferenceEdition.settings_json` and are Edition-configurable, never hardcoded universally; proposed JSON key names, defaults and provisioning are subject to the separate P03-G technical plan | ACCEPTED |
| META-033 | Presenter/author abstract DRAFT and official submission require a valid Edition participation intent and selected package, not advance payment or Registration Confirmed; payment follows authorized academic ACCEPT when applicable | ACCEPTED |
| META-034 | Abstract metadata readiness uses READY / WARNING / BLOCKED without a numerical score and does not itself create a submission, change lifecycle state, or create a protected snapshot | ACCEPTED |

## ARCH-001 — Stack & ERD Decisions

| ID | Decision | Status |
|---|---|---|
| ARCH-001 | V1 is one Laravel 13 modular monolith | ACCEPTED |
| ARCH-002 | PHP 8.4 is the target runtime | ACCEPTED |
| ARCH-003 | MySQL 9.7 LTS is the frozen relational database baseline for V1 | ACCEPTED |
| ARCH-004 | UUIDv7 CHAR(36) is the internal first-class domain ID strategy; human codes are separate | ACCEPTED |
| ARCH-005 | Normal first-party UI uses Laravel routes/controllers + Inertia, not a separate internal REST SPA | ACCEPTED |
| ARCH-006 | Official Laravel Vue Starter Kit is the application skeleton | ACCEPTED |
| ARCH-007 | Frontend stack is Vue 3 + TypeScript + Inertia 3 + Tailwind CSS 4 + shadcn-vue + Vite + Vue I18n + Lucide Vue | ACCEPTED |
| ARCH-008 | Spatie Permission Teams uses conference_edition_id as edition-scoped authority context, with Policies/Gates for resource rules | ACCEPTED |
| ARCH-009 | Database queue/cache/session is the V1 baseline; Redis is not required | ACCEPTED |
| ARCH-010 | Protected files are private-by-default through Laravel Filesystem; S3 remains future-compatible | ACCEPTED |
| ARCH-011 | Spatie Laravel PDF abstraction with Browsershot/Chromium is the default official-document renderer | ACCEPTED |
| ARCH-012 | QR carries verification/lookup URL or opaque token, not unrestricted PII | ACCEPTED |
| ARCH-013 | No external search engine is required for V1 | ACCEPTED |
| ARCH-014 | Relational core is normalized; JSON is reserved for snapshots/evidence/configuration/localized CMS edges | ACCEPTED |
| ARCH-015 | No generic EAV model and no event-sourcing/CQRS architecture | ACCEPTED |
| ARCH-016 | ERD v1 relationship/cardinality contract is frozen | ACCEPTED |
| ARCH-017 | Published schedule, certificates, documents and exports are representations of database state, not independent truths | ACCEPTED |
| ARCH-018 | Authentication V1 includes registration, password authentication/reset/confirmation and required email verification; 2FA and passkeys are excluded from V1 | ACCEPTED |
| ARCH-019 | User, Role and Permission security identities use UUIDv7-compatible identifiers; Activity Log is audit evidence and not a replacement for explicit business-history records | ACCEPTED |
| ARCH-020 | Superadmin is a global application authority across all Conference Editions and application abilities. It bypasses ordinary role/permission/policy authorization but never bypasses validation, business invariants, state-transition rules, database constraints, immutable/versioned history, transaction safety, or mandatory audit logging | ACCEPTED |

## DEV-START — Implementation Authorization

| ID | Decision | Status |
|---|---|---|
| DEV-START-001 | Product Owner authorized V1 implementation under the frozen 10-working-day plan; no new V1 scope is added, frontend parallel work uses frozen contracts, and Days 9–10 remain stabilization/UAT | ACCEPTED |
| DEV-START-002 | Product Owner explicitly authorized PHASE 02 — Registration + Payment on 2026-10-03 against the accepted Pre-Phase 02 rebaseline, including FREE/PAID/complimentary participation | ACCEPTED |
| DEV-START-003 | Product Owner explicitly authorized PHASE 03 — Submission + Scholarly Metadata on 2026-10-06. Abstract submission follows the accepted presenter/author lifecycle: participation intent + selected package before abstract submission; presenter payment/Registration Confirmed occurs only after authorized ACCEPT when required | ACCEPTED |
