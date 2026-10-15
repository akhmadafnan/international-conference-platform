# ICHES Authenticated Workspace — Views & User Interaction Baseline V1

**ID:** ICHES-UX-WS-001  
**Decision date:** 2026-10-15  
**Status:** Product Owner-approved direction; implementation staged / not all views built  
**Scope:** Authenticated workspace (participant/presenter/reviewer and later operational authorities). Public landing redesign is out of scope.  
**Workstation:** Laptop 1, D:\PROJEKKU\international-conference-platform (VSCode-first)  
**Integration:** Phase 03 child PRs target phase/03-submission-metadata; Phase 03 aggregate PR #101 remains Draft, not merged into develop.

## 1. Product Owner direction and supersession

**Restart the authenticated Views & User Interaction design approach, NOT the backend architecture.**
Do not recreate the Laravel app, MySQL models, migrations, verified domain actions, payment rules, or P03-A–P03-H tests. Adopt the official Laravel Vue Starter Kit's existing components and layouts with minimum styling. Keep authenticated surfaces neutral: white/gray in light appearance, neutral dark appearance, readable borders, simple navigation. **No dominant green sidebar or compulsory green UI buttons** in the authenticated workspace. Public conference branding remains separately scoped and must not regress because of global token changes.

VSCode on Laptop 1 is the main editor, browser/UAT surface and source-development environment. AI helps with design, bounded code and review; Remote Desktop is optional and never compulsory. GitHub Issues/child PRs carry durable checkpoints. Laptop 2 is backup; both MySQL databases have identical structure but independent local data and credentials.

This amendment narrows the earlier frontend prototype spec's "polished" requirement for AUTHENTICATED BACKEND WORKSPACES ONLY. The existing frontend product specification remains authoritative for page content, multilingual support, valid lifecycle and the five-step abstract wizard. Its conceptual route map and typed mock scenarios are **not proof that corresponding Laravel routes or backend actions already exist**.

## 2. Real as-built inventory at P03-H merge

**Integrated source:** phase/03-submission-metadata @ 472ddf6641714801b44e3e22dc22e74bef627824, merging child PR #121. Parent PR #101 remains Draft.

| Surface | Actual page/route as of this revision | Readiness | Intended treatment |
|---|---|---|---|
| Public home | Welcome, GET / | LIVE / existing | Retain; public design separate |
| Login/registration/recovery | resources/js/pages/auth/* | LIVE / existing | Keep existing Laravel Fortify flows; Google OAuth is separate, not implemented |
| Participant overview | Dashboard.vue, GET /dashboard (ParticipantDashboardController) | LIVE / Phase02 | Preserve Next Action and no fake metrics |
| Edition package and registration/payment | registration/Show.vue, GET/POST /editions/{edition}/registration | LIVE / Phase02 | Preserve FREE/PAID/complimentary and accepted-author billing rules |
| Event Pass | documents/EventPass.vue and existing document routes | LIVE / Phase02 | Keep mobile QR and guarded access |
| Profile/security/appearance | settings/* via routes/settings.php | LIVE | Keep starter-kit defaults and auth controls |
| Abstract submission listing and wizard | NO routes/controllers/Vue pages yet | P03-I BUILD | New integrated user-facing vertical slice |
| Official abstract submit | SubmitDraftAbstractAction, integrated P03-H | BACKEND ONLY | Call through P03-I authorized controller; UI never changes academic state directly |
| Superadmin operational dashboard | NO dedicated admin route/page yet | NOT IMPLEMENTED | Do not present participant dashboard as complete superadmin console |
| Reviewer queue / academic screening / LoA | NO operational routes/pages yet | DEFERRED / Phase04 | No fake buttons or data |
| Full Article, scheduling, event day, publication, awards, certificates | No integrated operating UI | LATER PHASES | Design index only, not authorized to implement now |

The currently shared resources/css/tokens.css sets primary/sidebar colors to ICHES green; resources/js/components/AppSidebar.vue currently renders only Dashboard and inherited Laravel documentation/repository links. Changing global brand tokens without scope control can regress public pages. Do not invent data by using placeholder analytics.

## 3. Information architecture

One application with distinct user **contexts**, not a pile of independent admin template apps.

### Participant / author workspace (progressive by state)

- Overview: truthful current edition, registration/payment state, urgent Next Action; empty state when no participation.
- My Participations / Registration: active edition and chosen package; existing Phase02 view.
- My Submissions: list/details and five-step wizard, **only when real Phase03 page/routes exist**.
- Event Pass: only if issued/entitled by existing service.
- Profile, Security, Appearance and supported Help: show only routes actually implemented.
- Reviewer assignments: only for users with genuine assigned reviewer context and Phase04 implementation, not for every logged-in user.

### Internal / privileged workspace (authority-aware)

- Global superadmin accesses all editions and authorized modules, but cannot bypass validation, state transitions or immutable history.
- Conference Admin, Finance, Academic, Event, Reviewer, Publication, Award, Certificate authorities stay separated; use server-side edition-scoped policy/Gate checks. Menu visibility is presentation, never the security boundary.
- Provide navigation entries only when corresponding authorized routes and working features exist. No fake finance/academic queues, no unapproved actions.
- Treat the current participant Dashboard.vue as participant-specific content; future privileged overview gets its own controller/page when implemented, not simply different navigation labels.

### Edition context and state

- One account across editions; edition-scoped memberships and role authority remain separate.
- UI must show which edition the user is acting on when applicable and avoid silently changing edition.
- Sidebar is role/state-aware; active edition and authorized routes are resolved from backend props/capabilities, never inferred from client role-name strings alone.

## 4. UI implementation contract

**Reuse** Tailwind CSS 4, Vue 3 + TypeScript, Inertia, shadcn-vue, Vue I18n and Lucide Vue already installed. No React admin theme, additional CSS framework, global layout rewrite or custom visual system.

- Workspace theme: semantic CSS variables scoped to an authenticated root such as data-workspace or a workspace CSS layer. Light: neutral off-white/white backgrounds, slate/gray text, subtle borders, near-black primary, neutral sidebar. Dark: neutral charcoal surfaces and sufficient contrast. Do NOT overwrite public/root branding tokens globally.
- Fonts: reuse installed defaults; no mandatory custom fonts or decoration in backend.
- Reusable components only where repeated behavior is demonstrated: WorkspacePageHeader, NextAction, StatusText/Badge, EmptyState, FormSection, FieldErrorSummary, ConfirmationDialog, MobileList/Table; first look for existing ui/* primitives.
- Clear pages before decoration: one main heading, breadcrumb when meaningful, human-readable statuses, labels, validation summaries, inline errors, save/cancel/submit with pending/disabled states.
- No metrics without real queries; no fake activity, reviewer assignment, payment receipt or certificate.
- Responsive at mobile/tablet/desktop; avoid giant tables on mobile; ensure focus and skip navigation; honor prefers-reduced-motion.
- i18n: id/en/ar, Arabic layout RTL including sidebar direction, and separate scholarly content locale (primary_locale is NOT UI locale).
- Inertia forms: use useForm/Form with server validations and status redirects; never write DB directly from Vue or trust button hiding as authorization.
- Form UX should allow intentional draft saving, validation error recovery and safe navigation. Explicit confirmation before irreversible official submission. No silent step skipping.

### Proposed file boundaries (names provisional until implementation review)

- resources/js/components/workspace/* for small shared workspace atoms.
- resources/js/pages/submissions/Index.vue, Show.vue and wizard/* for author-facing Phase03.
- resources/js/layouts/app/* and AppSidebar.vue for retained starter-kit shell.
- app/Http/Controllers/Submission/* for small Inertia GET/POST endpoints.
- app/Http/Requests/Submission/* for exact route FormRequests.
- Frontend props are typed, adapted server-side from existing models/actions. Avoid exposing private storage paths, full audit internals, role secrets or contributor PII outside authorized context.

Do not place auth-specific token overrides in global :root/.dark. Do not modify the complete public site for the workspace restyling.

## 5. Phase 03 P03-I: first full vertical feature

Build P03-I as a single separately reviewed feature, using already-integrated domain actions instead of rewriting them.

| Step | User-facing fields/interactions | Authoritative server behavior |
|---|---|---|
| 1 — Details | primary scholarly language, optional Track according to Edition rule, title/subtitle/abstract, 3–5 primary-locale keywords for ICHES 2027 | Existing draft creation/details mutation and Edition policy |
| 2 — Contributors | ordered author list, single/given/family naming, country, email, institution + optional subdivision, ORCID optional, exactly one corresponding author before submit | Existing contributor mutation; rule validated again by readiness |
| 3 — References | ordered raw citations; optional normalized DOI/URL; add/remove/reorder | Existing ordered-reference mutation |
| 4 — Files | optional PDF/DOCX upload for ICHES 2027, progress/validation/history of private versioned file; required only if Edition policy demands | Existing private immutable abstract-file ingestion |
| 5 — Review & Submit | truthful READY/WARNING/BLOCKED summary, missing fields, confirmation, explicit submit; prevent rapid repeat clicks | Existing read-only readiness action; authoritative P03-H transaction and snapshot |

**Lifecycle constraint:** participation intent + chosen package → abstract DRAFT/official submit; there is NO presenter's payment or Registration CONFIRMED requirement before abstract. Presenter payment obligation follows authorized ACCEPT in later phase. Participant-only paid workflow remains independent.

Workflow examples:
- No Edition registration yet → select valid edition/package through existing route.
- Draft exists → reopen editing only while still DRAFT and authorized.
- Missing required metadata → BLOCKED with field-level, actionable correction.
- WARNING → review issues but permit official submit under policy.
- SUBMITTED → status screen with submission identity/confirmation, no writable draft controls.
- Authorization failures, expired/inactive submission window, other edition or cancelled registration → no snapshot or transition.
- Reload, double-click or concurrent requests → exactly one immutable INITIAL_SUBMISSION snapshot/audit. Verified on MySQL at P03-H checkpoint.

## 6. Delivery sequence after this baseline

1. **UI-00** authenticated neutral shell and honest role-aware navigation; do not build fake admin pages. PR linked to Issue #125.
2. **P03-I** submission list/detail and five-step wizard end-to-end to integrated P03-A–H backend. Separate PR linked to P03-I issue.
3. **Phase03 exit** participant browser UAT, locale/RTL/accessibility, security/authorization, transaction/error recovery, full related integration; only after PO acceptance does aggregate PR #101 target develop integration.
4. Later authorized phases: reviewer/screening/decision/LoA; Full Article/schedule; event-day and attendance; revision/publication; certificate/awards; operational admin views as their domain implementations mature. UI shell reuses UI-00 components but does not activate unfinished features.

Do not migrate or reset the main local database merely to check visual states. Use fixtures/seeds in disposable testing DB when needed.

## 7. Vertical-feature definition of DONE

For each implemented view or action:
- Route exists and is guarded by auth, verified email where relevant, authorization/Edition context.
- Backend props are bounded and typed; fields/validation follow existing domain contract.
- Rendered page has clear Next Action, meaningful empty/pending/error/success states and visible navigation.
- Browser user can complete the real server action without a terminal-only special case.
- Server prevents unauthorized cross-user/Edition access and no fake available feature is shown.
- Locale id/en/ar works, including RTL and error labels.
- Responsive + keyboard/a11y smoke checks pass.
- Scoped formatter, TS typecheck/build (if Vue), focused Laravel Pest and related regressions pass.
- Git diff limited to issue scope, clean commit, Draft child PR to Phase03, actual PO browser/UAT acceptance before merge.

## 8. VSCode-first working loop — canonical Laptop 1

Repo: D:\PROJEKKU\international-conference-platform. **Do not copy .env or MySQL content from Laptop 2.**

Before new UI branch work in a fresh terminal:
1. Ensure VSCode working tree is clean; if not, pause and review local changes, never reset them.
2. Fetch GitHub refs; switch to phase/03-submission-metadata; fast-forward only to latest integrated P03-H; open a new scoped child branch.
3. Edit and run the application from VSCode; use AI as reviewer/copilot, not as a required autonomous executor.
4. Test with local PHP/npm and browse application; submit screenshot/UX issues as needed.
5. Commit exact files, push, create Draft PR to phase/03-submission-metadata, then review.
6. Never merge phase aggregate PR #101 to develop or start Phase04 without explicit PO authorization.

## 9. Known follow-ups, explicitly not implemented here

- A genuine privileged superadmin dashboard separate from participant Dashboard.vue.
- Google sign-in and simpler password policy (Issue #122, Draft PR #124), not merged here.
- Internal authority-based operational routes and review queues for later phases.
- Snapshot immutability across hypothetical future administrative write paths.
- A stable project-wide rewrite of outdated CURRENT_STATE/AGENTS frontend checkpoint narratives should be a bounded documentation reconciliation, not an excuse to change old domain rules.

## References / source authority

- docs/frontend/FRONTEND_PRODUCT_SPEC_V1.md §4, §7.1–7.6, §11–17
- docs/governance/DECISION_REGISTER.md: PD-002, PD-003, PD-006, PD-010–011, PD-042, PD-053–054, PD-058, PD-069; META-028–034
- docs/requirements/v1/V1_AUTHORITY_BASELINE.md
- docs/ai-context/INTERNATIONAL_CONFERENCE_PROJECT_CANONICAL_CONTEXT.md
- docs/governance/WORKING_PROTOCOL.md and ENGINEERING_WORKFLOW.md
- PR #121 (P03-H integrated), aggregate PR #101 (Draft), Issue #125 (UI-00), P03-I implementation issue.
