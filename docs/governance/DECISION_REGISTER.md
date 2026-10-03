# Product Decision Register — Current Product Baseline

**ID:** ICHES-DECISION-REGISTER  
**Status:** ACTIVE — PRODUCT BLUEPRINT v1  
**Updated:** 2026-10-02

This register records current Product Discovery and accelerated V1 decisions.

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
| PD-017 | Administrative screening precedes abstract review | ACCEPTED |
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
| PD-062 | Abstract reviewer count defaults to 1 and remains configurable | ASSUMED_V1 |
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
