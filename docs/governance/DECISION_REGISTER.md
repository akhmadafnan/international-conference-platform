# Product Decision Register — Current Product Baseline

**ID:** ICHES-DECISION-REGISTER  
**Status:** ACTIVE — PRODUCT BLUEPRINT v1  
**Updated:** 2026-09-30

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
| PD-006 | All participants pay at the beginning as commitment gate | ACCEPTED |
| PD-007 | Manual bank transfer + proof + Finance verification is V1 payment model | ACCEPTED |
| PD-008 | Payment verification confirms registration and unlocks Event Pass/QR | ACCEPTED |
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
| PD-050 | Canonical scholarly metadata feeds OJS/Crossref adapters | ACCEPTED |
| PD-051 | Finalize for Production creates protected publication snapshot | ACCEPTED |
| PD-052 | Metadata readiness uses READY/WARNING/BLOCKED, not synthetic score | ACCEPTED |
| PD-053 | Public frontend and authenticated dashboard are one coherent product journey | ACCEPTED |
| PD-054 | Dashboard centers on Next Action and hides irrelevant menus | ACCEPTED |
| PD-055 | One account can hold multiple scoped functions without account switching | ACCEPTED |
| PD-056 | Community Service remains lightweight CRUD, not mini-KKN | ACCEPTED |
| PD-057 | Evening/MoU remains lightweight CRUD, not contract management | ACCEPTED |
| PD-058 | Laravel 13 official Vue Starter Kit + Vue 3 + TypeScript + Inertia + Tailwind + shadcn-vue is the preferred stack direction pending Stack Freeze | CANDIDATE |
| PD-059 | Paid participant with rejected abstract remains participant; no automatic academic-rejection refund | ASSUMED_V1 |
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
