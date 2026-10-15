# ICHES — P0–P03 As-Built Backend View & Browser UAT Audit

**ID:** ICHES-UI-AUDIT-20261015-01  
**Status:** AUDITED / corrective implementation pending PO browser UAT  
**Date:** 2026-10-15  
**Scope:** Existing application through Phase 03, plus uncommitted local P03-I Batch01 observed on Laptop 1  
**Authority:** Product Owner direction; `docs/governance/DECISION_REGISTER.md`; canonical product, architecture, permission and frontend specifications  
**Source snapshot:** `phase/03-submission-metadata @ cd61437addf061fdb56d23a20cccfd4b2226e518`, not `develop`. Aggregate PR #101 remains OPEN/DRAFT.  
**Operational host:** Laptop 1 `D:\PROJEKKU\international-conference-platform`, VSCode-first. Laptop 2 is backup; **local DB content and credentials intentionally differ**.

## 1. Executive finding

**Technical completion and browser product completion must be tracked separately.** Phase 01 foundation and Phase 02 registration/payment have verified domain and HTTP tests. Phase 03 P03-A–H scholarly domain actions, snapshot, checksum and MySQL concurrency have passed technical gates. However, public-to-participant-to-author and privileged operational **end-to-end browser journeys are NOT fully available**.

The P03-I Batch01 local submission Index screenshot shows no eligible registration and no abstracts. This is a truthful empty state for that account, **not proof of a broken submission action**. The application lacks browser UI to configure the requisite conference series, edition, package, relevant windows, and institutional operators. On the server only `/dashboard` is the Phase02 **participant** dashboard; a superadmin logging in sees participant content because no dedicated admin dashboard exists.

**Major domain/UX gate:** the approved author path is `Participation intent → Abstract → Screening → Review → ACCEPT → Payment obligation`; yet the current `StoreParticipantRegistrationController` invokes `ResolveRegistrationFeeRequirementAction` immediately after creating a PAID registration. This is appropriate for **participant-only PAID** registration but potentially violates the **author/presenter** path when no participation-purpose distinction is passed through. Treat as a policy-versus-code discrepancy requiring bounded technical decision and tests **before claiming end-to-end author-flow green**. Do not retroactively modify finance records, quietly skip financial guards or invent a role from a package name.

## 2. Repository inventory, as checked

- **482** tracked files in the Phase03 source snapshot, **10** migrations (3 Laravel foundational plus permissions, activitylog, superadmin, Phase02 and Phase03).
- **13** tracked Vue page components: Welcome, Dashboard; auth Login/Register/VerifyEmail/Forgot/Reset/Confirm; settings Profile/Security/Appearance; registration/Show; documents/EventPass.
- Tracked application routes in `routes/web.php`: Home; login/auth via Fortify; `GET /dashboard`; edition-specific registration GET/POST; payment proof/correction/verify POST; EventPass show/QR; settings; locale switch. **No admin configuration GET/POST UI; no tracked P03 author routes before Batch01.**
- Local Laptop1 `php artisan route:list --json` on dirty P03-I branch reported **40 routes** including devtools/settings/auth and **4 uncommitted author submission routes**, `/dashboard/submissions` GET/POST and details GET/PUT. The 40 route count is **local dirty state, not integrated source**.
- Local dirty P03-I branch is `feat/126-p03i-author-wizard @ cd61437` with modified `routes/web.php` and `AppSidebar.vue`, plus new Submission controller/pages/i18n helper/HTTP test. **Not committed, pushed or merged. Do not reset/stash/apply again without a careful preservation plan.**
- User-reported first Batch01 Pest run failed 3/7 because new Vue entrypoints were missing from the then-current Vite manifest. After frontend build, independently rerun scoped Pest **7 passed / 53 assertions**. Local Vue typecheck initially flagged `form.errors.details` not declared; a suggested correction exists, but no verified later typecheck result is recorded. **Batch01 is NOT GREEN.**

## 3. Phase-by-phase delivered domain vs actual view

| Phase / area | Server/domain/persistence as built | Existing HTTP + Vue | Missing to complete browser user journey |
|---|---|---|---|
| **P0 Product Discovery/Blueprint** | Product rules, permissions, multilingual contract, ERD/stack freeze, lifecycle and NFR decisions | No P0 runtime app by design | No "P0 dashboard" should be invented. Convert decisions into information architecture and UAT checklist |
| **P01 Authentication** | Laravel Fortify, verified email, hashed passwords, reset, session; user models | Login/Register/VerifyEmail/Reset/Forgot/Confirm Vue | Neutral login visual; honest local mail verification UX; native id/en/ar copy; password optional policy not implemented; Google login not implemented |
| **P01 Security/Access** | UUIDv7 identities, Spatie Permission Teams per Edition, global SuperAdmin, server-side authorizer, auditlog | Profile/Security/Appearance pages; generic participant Dashboard | Separate permission-aware privileged workspace; active Edition switcher; admin-only entry/guard; no false superadmin dashboard |
| **P01 Localization & theme** | Vue i18n messages id/en/ar, RTL shell, light/dark toggle, `tokens.css` | Sidebar and auth shell | Shared global `tokens.css` is green-tinted; current `.workspace-shell` override covers sidebar workspace ONLY; auth, global statuses and other layouts still leak green |
| **P02 Conference Configuration** | ConferenceSeries/ConferenceEdition/Track/ImportantDate/Activity/EditionWorkflowWindow etc. Eloquent models/migrations; package/payment destination configure actions with scoped authorization | **No Series/Edition/Track/window admin UI** | Create/manage Series & Edition; select active Edition; configure packages, windows, tracks, registration availability and destinations using real, authorized controllers/actions |
| **P02 Participant Registration** | Edition membership, registration intent, package/fee snapshots, FREE/PAID/complimentary and workflow guard | `registration/Show.vue` GET/POST, Dashboard summary | Public / dashboard path for choosing Edition when account has no membership; clear participant-only versus author intent; avoid misleading "pay first" author UX |
| **P02 Payment/Finance** | Protected proof, correction, Finance verification, audited versions, EventPass issuance | Proof upload/form in Registration view; POST Finance verify/correction HTTP; EventPass page | **No finance queue/detail browser workspace**; no admin editable payment-destination browser view; negative/role tests must be reflected in browser UAT |
| **P02 Event Pass** | Issued document/opaque QR lookup, privacy guards | EventPass.vue and QR route | UAT from actual eligible participant through payment/FREE confirmation to EventPass in browser, including mobile and Arabic |
| **P03-A/B** | Submission metadata models; `CreateDraftSubmissionAction` with Edition Paper ID | No integrated tracked author Vue | My Submissions listing, create action, ownership-based routes; local Batch01 WIP only |
| **P03-C** | Draft Details mutator with scholarly locales/Track/title/abstract/keywords | No integrated tracked Details Vue | Step1 Details form, validation, draft persistence, language distinct from UI; local Batch01 WIP |
| **P03-D** | Contributors, ordering, correspondence, affiliation, optional ORCID action | None | Step2 Contributors / Affiliations GUI + persistence/guard UAT |
| **P03-E** | Ordered reference mutation, raw citation preservation, DOI/URL optional | None | Step3 References GUI and reorder/mutation UAT |
| **P03-F** | Private versioned PDF/DOCX abstract uploads, integrity guards | None | Step4 Files GUI, file validation, version history, private access |
| **P03-G** | Server read-only READY/WARNING/BLOCKED readiness evaluator | None | Step5 actionable readiness issues, categorized by wizard step |
| **P03-H** | Transactional official DRAFT→SUBMITTED, one INITIAL_SUBMISSION SHA snapshot & audit; genuine MySQL concurrency proof | None | Review/Confirm/Submit UI + receipt/read-only status; preserve server authority |

**Do not claim** "Phase02 browser end-to-end" merely because its `ParticipantBrowserFlowTest` passes via HTTP simulation. That test covers real controllers but is not equivalent to a PO-led visual, real-user navigation/UAT. The `FRONTEND_PRODUCT_SPEC_V1.md` route map is expressly **provisional**, and its frontend-prototype exclusions for admin backoffice do not mean the actual backoffice has been implemented.

## 4. Product decision invariants — do not change through UI

- One account is reused across Conference Editions; Edition-specific membership and permission scope remain separate.
- Presenter is **not** a registration role selected upfront. Author academic route begins with abstract submission.
- Payment:
  - participant-only PAID registration may create a payment obligation before any abstract;
  - corresponding author/presenter: participation intent → submit abstract → academic ACCEPT (later phase) → payment obligation only if PAID;
  - FREE and approved complimentary must not require a synthetic Payment record;
  - no presenter payment requirement for P03 abstract submission; rejection creates no presenter payment.
- Finance verifies a payment, not academic acceptance. Superadmin authorization bypass is **not** domain-invariant bypass, and does not automatically authorize a Finance/Academic business decision.
- Anonymous reviewer/confidential academic data never exposed in participant UI.
- Language of UI is id/en/ar with RTL; `primary_locale` on Submission is an independent scholarly field.
- Only activate menu items backed by real routes and protected controllers; no fake counts, invalid state changes or future-phase LoA/reviewer links.
- Official submitted snapshot is immutable; no second submission, no data loss from editing.
- `MAIL_MAILER=log` is currently in Laptop1 local `.env`; verification messages are logged locally, not sent to Gmail. Configure safe local mail viewing (e.g. Mailpit or documented log viewer), not auto-mark accounts verified for production.

## 5. Neutral visual foundation — definitive PO directive

**Default look:** official Laravel Vue Starter Kit, monochrome neutral `zinc/slate/white` semantics. Light mode white/off-white; dark mode charcoal/black and white/gray text. Sidebar, forms, links, cards, backgrounds, input focus, primary and secondary buttons are grayscale by default. Keep meaningful red errors and controlled semantic state indication when actually needed; do not make green a compulsory palette. Minimal card/border/shadow styles; standard shadcn-vue primitives.

**Root cause audit:**
- `resources/css/tokens.css` defines globally green-tinted `:root` and `.dark` tokens including `--background`, `--foreground`, `--primary`, `--accent`, `--border`, `--ring`, `--sidebar-background`.
- `resources/css/workspace.css` neutralizes only `.workspace-shell` and `.dark .workspace-shell`. It does **not** cover `AuthSimpleLayout.vue`; that layout is actually selected by `AuthLayout.vue`.
- `Login.vue` and `VerifyEmail.vue` contain explicit `text-green-600` successful-status copy, a second independent source of green.
- The underlying public `Welcome.vue` is separate from authenticated shell; neutral auth should be scoped to auth roots or require explicit public visual regression approval before rewriting global brand tokens.

**First bounded corrective (P01-UI-01):** share existing neutral workspace semantic CSS overrides with a new `.auth-shell` root on **active** AuthSimpleLayout, and replace nonessential explicit green auth copy with semantic neutral text. Do not overwrite public homepage styles; do not touch domain/session/business state. Verify light/dark and id/en/ar/RTL.

## 6. Sequential view rebuild — dependency order, not random feature order

| Gate | Small deliverable and real GUI outcome | Prerequisites | Key UAT acceptance |
|---|---|---|---|
| **V00 Audit/contract** | This as-built map + role journey + fixture policy; no fake P0 runtime view | Audited source | PO accepts sequence |
| **V01 P01 Auth/Theme** | All Login/Register/Verify/Reset/Security and existing workspace neutral light/dark | Scoped CSS + existing Fortify | Browser login/register, email verification, forgot/reset, profile, dark/light + RTL; no green tint |
| **V02 P01 Privileged Workspace** | Superadmin/Conference Admin authorized landing; transparent role/Edition context | Protected routes/props; security capability checks | Privileged account no longer sees participant-only overview; ordinary user cannot open admin |
| **V03 P02 Edition Setup** | Series list/create, Edition list/create/edit, active Edition selector; honest empty states | Models existing, new guarded CRUD/action validation required | Operator configures Edition entirely in browser without Artisan/manual SQL |
| **V04 P02 Edition Configuration** | Packages, fee/destination, tracks, workflow windows and deadlines; actual domain actions reused | V03 | FREE and PAID correctly configured; permission and Edition cross-scope denies |
| **V05 P02 Participation Journeys** | Edition discovery → participation intent; participant-only vs author distinction; registration + Next Action | V03–04 + P02/P03 payment lifecycle gate decision | No early author obligation; participant-only paid/FREE correct; no fake payment step |
| **V06 P02 Finance/EventPass** | Finance queue and verification/correction + participant proofs; EventPass and QR | V05 + permissions/test actors | Full paid/free paths through browser, role denied checks, QR privacy |
| **V07 P03 Author Index/Details** | Preserve/correct current local Batch01 My Submissions/Create Draft/Step1 | V05; no cherry-pick/overwrite WIP until reviewed | User makes draft from a real Edition; save/reopen; foreign access denied |
| **V08 P03 Wizard Steps 2–4** | Contributors → references → private files with real persistence | V07 | Author edits and resumes; metadata, order, ORCID, private file states |
| **V09 P03 Review/Submit** | Readiness summary, confirmation, official submit, immutable read-only receipt | V08 | READY/WARNING/BLOCKED, duplicate-submit and window guards; one snapshot/audit |
| **V10 P03 Exit** | End-to-end participant + author browser UAT, regression and privacy gates | V01–09 | PO signs actual browser flow, then Phase03 integration decision; no Phase04 |

**Sequence notes**
- Phase0 is decision/infrastructure planning; do not fabricate an admin page named "P0".
- Existing `registration/Show.vue`, `Dashboard.vue`, `EventPass.vue` remain usable; refactor only where a feature requires it. Their design can be aligned later with the shell.
- Phase04 reviewer assignment, screening, ACCEPT/LoA remain outside authorized implementation. Phase03 exit proves official abstract SUBMITTED, not academic ACCEPT/LoA.

## 7. Browser UAT traceability — required evidence each gate

| Scenario | Actor + setup | Browser action | Pass condition | Negative check |
|---|---|---|---|---|
| UAT-01 | New local user | Register → verification → login | Verified account reaches the proper workspace | Unverified user stays protected; rate limit/recovery safe |
| UAT-02 | Any authenticated account | Switch light/dark + id/en/ar | No green default; Arabic root+sidebar RTL; keyboard/mobile usable | Hidden menu does not substitute authorization |
| UAT-03 | Global superadmin | Create Series and Edition | Edition persisted; visible selector; audit | Ordinary participant gets 403/404 |
| UAT-04 | Edition admin/capability | Configure FREE/PAID, dates/windows/Track, destination | Validated server configuration and localized display | Cross-Edition change denied |
| UAT-05 | Participant-only FREE | Join/register FREE | Confirmed; no Payment row; eligible EventPass | No bank/proof demanded |
| UAT-06 | Participant-only PAID + Finance | Join/register → proof → correction if needed → verify | One valid payment path; CONFIRMED/EventPass | Finance self-case/cross-Edition authorization boundaries |
| UAT-07 | Author, PAID package | Participation intent → Create Draft | No presenter payment obligation before ACCEPT; DRAFT can be edited | Attempt early pay is not required or mislabeled |
| UAT-08 | Author with valid Registration | Details → Contributors → References → File | Data saved across refresh/logout/login | Other user/cross-Edition cannot access |
| UAT-09 | Author, readiness configured | Review/Submit | Single INITIAL_SUBMISSION; SUBMITTED receipt and immutable state | Missing/closed-window/inactive edition and repeated submit denied |
| UAT-10 | Admin and participant | Navigate normal dashboards | All visible links work, no placeholder screens/states | Internal queue inaccessible to ordinary accounts |

**UAT evidence**: date, branch/commit, target DB, actor-role-fixture, action, expected vs observed, request/status or screenshot, locale+breakpoint, PASS/FAIL, defect issue/PR. Use disposable testing DBs or controlled dedicated local fixtures; never run `migrate:fresh` on the main working DB just to make test data. No automatic account impersonation, manual `email_verified_at` bypass, or cloning credentials between laptops.

**Quality gates before merging each scoped PR:** narrowly scoped tests + related domain regression, PHP Pint/PHPStan where relevant, Vue TS check/build and existing lint, `git diff --check`, browser/UAT for actual view interaction and Arabic RTL, privacy/authorization, clean tracked diff, PO approval. Route tests with `withoutVite` should not require prior Vite build. CI job names are not proof they exercise all affected features.

## 8. Operational safety and continuation

**Current Laptop1 local work is dirty by design.** `feat/126-p03i-author-wizard @ cd61437` has uncommitted Step1 source changes. This audit is on a **separate GitHub docs branch**, with no change to Laptop1 working directory, MariaDB/MySQL schema, user accounts or `.env`. Do not reset, remove files, re-run the original P03-I ZIP `--apply`, or merge PR #101 to `develop`. To start V01 safely: first preserve the Step1 work in a scoped WIP commit/branch or an additional Git worktree by explicit PO-assisted VSCode procedure; only then checkout a neutral-auth change for browser evaluation.

Document priority: Product Owner latest direction + source code verified at named ref supersede stale `CURRENT_STATE.md` paragraphs that still call P03-G next or Laptop2 primary. Do not reinterpret unmerged Draft PR #124 6-character password policy as live. Google login remains deferred.

## 9. Audited code / canonical documents

- `docs/project-management/PHASE_0_BACKLOG.md`, `DELIVERY_LIFECYCLE.md`, `TWO_WEEK_DEVELOPMENT_PLAN_V1.md` (Day2/Day3 explicit exit gates)
- `docs/governance/DECISION_REGISTER.md` (PD-002, 003, 006–013, 042, 053–055, 058, 069, 081–084), `PRE_PHASE02_PRODUCT_REBASELINE.md`
- `docs/requirements/AUTHENTICATION_BASELINE.md`, `PERMISSION_FINAL_MATRIX.md`, `ACTOR_CATALOG.md`
- `docs/frontend/FRONTEND_PRODUCT_SPEC_V1.md`, `AUTHENTICATED_WORKSPACE_VIEW_BASELINE_V1.md`
- `routes/web.php`, `routes/settings.php`, all 13 tracked Vue pages and active AuthSimpleLayout
- `resources/css/tokens.css`, `workspace.css`, `app.css`, auth Login/VerifyEmail
- `app/Http/Controllers/Registration/StoreParticipantRegistrationController.php`
- `app/Actions/Registration/CreateRegistrationIntentAction.php`, `ResolveRegistrationFeeRequirementAction.php`
- `app/Support/Registration/ParticipantRegistrationView.php`
- `app/Actions/Payment/*`, `app/Actions/Submission/*` P03-A–H
- `tests/Feature/Auth/*`, `Foundation/*`, `Phase02/*`, `Phase03/*`
- GitHub Issue #129 (this rebaseline), #126 (paused local P03-I); Draft PR #101; auth password Draft PR #124.

**Audit verdict:** Domain foundations **P0/P01/P02 and P03-A–H exist** at their recorded scopes. Product-level browser acceptance **P01–P03 is NOT YET proven**. Start V01 and proceed by actual dependencies, retaining existing GREEN business logic.
