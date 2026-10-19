# ICHES Lyra admin pilot — audit and implementation handoff

Date: 2026-10-19 (Asia/Jakarta)
Related issue: #136. Functional baseline: V03B / V03B-C, Draft PR #138.
Status: audit and isolated Git setup complete; application implementation has not started.

## Accepted Product Owner direction

Laptop 2, DESKTOP-PE5D86C, is now the primary workstation at
`D:\PINJAM-AFNAN\Herd\international-conference-platform`.
Laptop 1 is the backup. This explicitly supersedes the older workstation
paragraphs in AGENTS.md and CURRENT_STATE.md for this workstream.
The Product Owner reports the databases have been synchronized; this audit
does not inspect, copy, migrate, seed, or alter either database.

V03A is GREEN and merged. V03B-C functional UAT stages 1–3 are GREEN and
accepted. Do not repeat their UAT or reopen accepted functionality.
Dashboard visual acceptance remains outstanding.
Real records reported by the Product Owner: Series ICHES; Edition ICHES-2027,
DRAFT. ICHES-2026 was intentionally deleted in accepted UAT. Preserve these facts.

The exact selected preset is
https://www.shadcn-vue.com/create?preset=a33J5lFw&item=preview.
It supersedes earlier Inter/Sora/Lucide and Nexus visual preferences within
the admin pilot. Do not substitute another preset.

```json
{
  "base": "reka",
  "style": "lyra",
  "baseColor": "neutral",
  "theme": "emerald",
  "chartColor": "emerald",
  "font": "jetbrains-mono",
  "fontHeading": "geist-mono",
  "iconLibrary": "phosphor",
  "radius": "default",
  "menuColor": "default",
  "menuAccent": "subtle"
}
```

The configuration was independently decoded using the Vue upstream field order
from https://raw.githubusercontent.com/unovue/shadcn-vue/dev/packages/cli/src/preset/preset.ts.
The Create page's server-rendered HTML showed a different default preset before
client hydration; that default is not evidence for the requested preset.
Use the exact selected code and configuration above.

Dashboard layout reference: https://www.shadcn-vue.com/view/dashboard-01.
The preset defines the visual system; dashboard-01 defines layout patterns.
Its sample revenue, visitors, charts, rows, and navigation are demonstration
content and must not become ICHES data or features.

## Verified Git and GitHub checkpoint

Read-only local Git, remote refs, GitHub PR metadata, issue comments, and Actions
were inspected. No fetch/reset/rebase/merge/push or application checks were run.

| Surface | Verified state |
| --- | --- |
| Main Laptop 2 worktree | Clean, feat/137-v03b-edition-management at 7dccdf7b180dd7227d948777e22eb3594635df5b |
| Remote V03B head | Same 7dccdf7 SHA |
| PR #135 | Merged into Phase03 at c6779fb68b97a6d9f6be2d489b65e2e948075fbb |
| PR #138 | Open Draft; head V03B; base phase/03-submission-metadata; not merged |
| Phase03 local/remote | c6779fb68b97a6d9f6be2d489b65e2e948075fbb |
| PR #101 | Open Draft to develop; not merged |
| P03-I local/remote | feat/126-p03i-author-wizard at 2ee6f91c09712781932c412e1680511fec28998b |
| Actions at 7dccdf7 | Backend Quality run 39206205439 completed successfully |

The combined-status endpoint returned no statuses; the Actions workflow endpoint
provided the success evidence above. Do not interpret the empty status response
as a fresh full-project CI certification.

Issue #139's final checkpoint records 151 PASS / 1,434 assertions and successful
PHPStan, Pint, Vue typecheck, frontend checks, and build at 7dccdf7. The later
Product Owner comment accepts delete UAT. Reuse this evidence; it was not rerun.
PR #138's opening body and older issue text contain outdated UAT/data statements;
their later comments and the Product Owner's latest instructions supersede them.

## Isolated pilot setup

- Branch: `design/136-lyra-admin-pilot`.
- Worktree: `D:\PINJAM-AFNAN\Herd\international-conference-platform-lyra-pilot`.
- Starting commit: exact V03B-C checkpoint `7dccdf7`.
- Main application worktree remains on the V03B branch at the same checkpoint.
- No .env, dependencies, database, or generated build assets have been copied.
- No preview server/domain has been configured yet.

This is a dependent design experiment on top of unmerged V03B, not a new phase
delivery or a merge into Phase03. It starts from V03B because the pilot must use
the real accepted Series/Edition/delete functionality. If a PR is later opened
before V03B merges, its base should be the V03B feature branch so its diff contains
only the pilot. Keep PR #138 and PR #101 Draft. After acceptance, coordinate the
visual changes into V03B or rebase onto its reviewed phase merge; never merge the
experiment automatically. Do not touch the P03-I branch or its files.

## Impact audit

Current components.json is `new-york-v4`, Neutral, Lucide. Reka UI is already
installed; Laravel 13, Inertia 3, Vue 3, Tailwind 4 and shadcn-vue are retained.
vite.config.ts currently loads Instrument Sans, Inter and Sora through Bunny.
Root tokens, workspace overrides, and shared UI components affect multiple
surfaces. `.workspace-shell` also appears outside admin, and workspace.css
contains shared auth selectors. A global replacement would exceed this scope.

The CLI's documented init changes configuration, CSS and dependencies, and its
apply command changes an existing project's preset. Do not run either command
in either application worktree for this pilot. A changed style string alone
does not migrate already-copied component classes.
Reference: https://www.shadcn-vue.com/docs/cli.

Pilot integration must handle:

1. Admin-only semantic Emerald tokens, Lyra shapes, control sizing, borders and
   spacing. Derive actual values from the upstream Lyra registry; do not assume
   that radius=default means the current project's 0.75rem.
2. JetBrains Mono body and Geist Mono headings loaded through an audited font
   source. Keep existing public/auth/participant fonts. Verify actual downloaded
   font faces and define an explicit readable Arabic glyph fallback.
3. Phosphor icons for the pilot admin shell/dashboard. Audit and pin its Vue
   package and update only the relevant dependency/lockfile; retain Lucide where
   existing shared components still use it. No application-wide icon replacement.
4. Sidebar, header, responsive content, real statistics, recent editions, and
   working quick actions following dashboard-01 composition.
5. Portal content: mobile sidebar, dropdowns, dialogs, tooltips, and toaster may
   render outside the admin root. Explicitly carry admin scope and RTL to these
   surfaces; do not solve this with unscoped body or :root overrides.
6. Existing authorization, URLs, Inertia forms, validation, pagination, destructive
   confirmations, and backend props remain authoritative.

## Bounded code-executor contract

Write application code only in the pilot worktree. No Git, tests, formatter,
build, mass CLI, migrations, seeders, database operations, commits or pushes.
ChatGPT owns the independent verification and integration steps afterwards.

First visual slice: admin shell and Dashboard only. Expose only Dashboard,
Conference Series and Conference Edition navigation with existing capabilities.
Show real seriesCount, editionCount, and recentEditions from the existing
controller. Keep truthful empty states and working create/view links.
Keep tables/forms/delete behavior available; styling inherited from the shell
must not break their layout, input readability, confirmation or focus handling.
Do not import the full dashboard-01 demo or add charts and invented metrics.

Expected source boundaries, to be narrowed after checking official assets:

- New `resources/css/admin-lyra.css`, imported from app.css with admin-only scope.
- vite.config.ts for the two selected fonts after font-source verification.
- AppShell.vue and layouts/app/AppSidebarLayout.vue for admin-only scope/layout.
- AppSidebar.vue, AppSidebarHeader.vue and scoped admin components as necessary.
- pages/admin/Dashboard.vue and affected id/en/ar labels only.
- Admin wrappers/adapters for portal/icon needs; shared UI primitives should keep
  non-admin rendering intact.
- package.json and the existing lockfile only for the audited Phosphor dependency.

Do not edit backend actions/controllers/requests/routes, authentication,
submission/P03-I, payment, migrations, or existing tests for a visual-only slice.
Keep components.json unchanged during the isolated scoped pilot; record the
candidate configuration here. A later global generator-config adoption requires
its own reviewed impact decision, rather than making future generated components
implicitly change style in public/auth/participant views.

## Verification and new visual UAT

Before preview: configure a separate pilot runtime and asset output with ChatGPT,
without replacing the main Herd site's public/hot or build assets and without
copying production/local secrets into Git. Confirm runtime connection settings
before starting; do not run migrations or seed the user's MySQL. An isolated Git
worktree by itself is not a configured runnable Laravel environment.

After source changes, ChatGPT runs scoped frontend checking, Vue TypeScript,
build, diff review and appropriate related regression against an isolated testing
environment. Do not rerun completed delete UAT or claim it becomes visually GREEN
from automated tests. Backend checks need expansion only if the diff reveals a
backend change or a relevant regression.

The proposed pilot URL is `http://127.0.0.1:8016`; it is a planned address, not
an active server. Publish the actual working address when runtime setup is done.
Visual UAT then proceeds as follows:

1. Open `<pilot-origin>/admin/dashboard` using the existing Superadmin account.
   Expect a Lyra admin shell, Neutral/Emerald palette, selected fonts and Phosphor
   shell icons. Counts and the ICHES-2027 DRAFT row must match real backend data.
2. Click Conference Series, then Conference Edition. Expect their existing real
   lists and correct active navigation. Click Create Edition; verify the ACTIVE
   ICHES series remains selectable and the form remains readable. Cancel without
   entering or saving data. Do not submit delete/create forms in this visual UAT.
3. Switch to English, Indonesian, then Arabic. Expect translated labels, correct
   sidebar direction, readable Arabic fallback, and intact mixed-direction codes.
4. Toggle dark mode, open the account menu and mobile sidebar, and inspect a delete
   dialog without submitting. Expect consistent Emerald/Neutral surfaces, readable
   text, focus visibility and translated confirmation copy. Cancel the dialog.
5. Repeat at approximately 390px, 768px and desktop widths, then 200% zoom. Use Tab,
   Enter and Escape to inspect navigation and modal focus. Expect no clipped primary
   controls or accidental destructive action.
6. Open the pilot login/public/participant views and compare against the accepted
   baseline. Expect the existing non-admin typography, palette and behavior.

No real data needs to be created or deleted to approve this visual slice. Evaluate
zero-data rendering through isolated test fixtures, not by clearing MySQL.
The Product Owner's GREEN accepts the visual pilot; do not request the same UAT
again without a specific subsequent change that invalidates it.

## Exit and integration

Deliver an exact file diff, font/icon provenance and version choices, scoped QA
evidence, and screenshots for the new visual review. Until then the status is
pilot preparation, not Dashboard visual GREEN. No commits, pushes, issue comments,
PR changes or merges were made during this audit/setup task.
