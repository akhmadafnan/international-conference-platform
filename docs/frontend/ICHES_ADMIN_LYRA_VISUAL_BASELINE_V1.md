# ICHES Admin — Lyra Visual Baseline V1 (FROZEN)

**Status:** VISUAL_APPROVED_BY_PO — 2026-10-19 (Asia/Jakarta).
**Scope:** ICHES administrative workspace and all subsequent admin features.
**Source:** `design/136-lyra-admin-pilot` based on V03B-C `7dccdf7`.
**Decision record:** issue #136; original pilot audit: `LYRA_ADMIN_PILOT_AUDIT_AND_HANDOFF.md`.

## Product Owner acceptance

On 2026-10-19, the PO supplied six real-browser screenshots and instructed: **lock this UI style and keep future UI consistent with it.** The accepted visual reference covers the admin Dashboard, Create Conference Series, Conference Editions index, Create Conference Edition, plus light and dark settings screenshots. It is approval of the **visual direction**, not evidence that every responsive / portal / RTL test was performed.

The selected shadcn-vue Create preset `a33J5lFw` is binding for the admin UI:

| Axis | Frozen choice |
| --- | --- |
| Base / style | Reka UI / **Lyra** |
| Neutrals / theme / chart | **Neutral / Emerald / Emerald** |
| Body / headings | **JetBrains Mono / Geist Mono** |
| Arabic text fallback | **Noto Sans Arabic** |
| Icons | **Phosphor** (`@phosphor-icons/vue`, pinned); existing Lucide components elsewhere are preserved |
| Radius / menu | Default preset radius (Lyra sharp control geometry); solid default menu; subtle accent |
| Density | Compact controls, restrained 1px borders, square cards/buttons/inputs, minimal shadows |

## Visual anatomy for future admin features

1. Keep the left collapsible sidebar with ICHES branding, distinct selected-nav highlight, and only real authorized routes. Put user menu in the sidebar footer; sidebar changes side in RTL.
2. Keep the quiet top header with sidebar toggle, breadcrumbs, appearance toggle, and `id/en/ar` language selector. Use generous overall whitespace but compact individual components.
3. Use a clean white/light neutral background, understated separators, emerald for *primary* action/state, not large saturated background panels. Dark appearance uses the corresponding neutral/emerald semantic tokens.
4. Typography is intentionally small and monospaced within the admin workspace, with readable Arabic glyph fallback. Maintain clear hierarchy; avoid reducing long form text below accessible, legible sizing.
5. For data screens use the same bordered content boxes, compact rows/tables, subtle status badges, readable form labels and guidance, normal pagination, and consistent create/cancel/delete affordances.
6. All admin pages—existing Series/Edition and future modules such as submission, registration, review, payments, certificates and reports—must **reuse** these UI primitives/tokens/layout patterns, not create a different style per module.
7. Preserve accurate backend values; do not paste demonstration content or invented revenue/visitors/charts from dashboard-01.
8. Keep destructive confirmation protection, validation states, focus navigation, full form operation, and existing authorization. Visual refresh must not alter business logic.

## Implementation and change control

- Primary scope: `resources/css/admin-lyra.css` + `.workspace-shell.admin-workspace.admin-lyra`, associated scoped portal styles, admin shell/navigation/Dashboard components.
- Do **not** run global `shadcn-vue init` or `apply`. Do not replace `components.json` for public/auth/settings/participant.
- Do not globally rewrite `:root`, `body`, shared UI primitives, route policy or server data contracts; preserve the existing app's non-admin visual baseline.
- Settings/profile screens shown alongside the PO screenshots **still use the existing non-admin layout**. They are not evidence of a full Lyra conversion of settings; any future alignment requires a separately scoped approved change.
- Translated labels must be provided in Indonesian, English and Arabic. RTL, dialogs/dropdowns/tooltips/toaster mounted via portal, 390px/768px/desktop layouts, and 200% zoom must remain usable.
- Future visual changes require an explicit amendment to this document and PO acceptance; routine new modules must simply follow this baseline.

## Verification and delivery boundary

At the initial visual acceptance checkpoint, the assistant independently reported TypeScript, scoped formatter/lint, Vite build, and **104 focused Pest tests (1,098 assertions)** passing using isolated SQLite. The selected font files were present in built assets. Functional delete UAT V03B-C was already accepted and should not be repeated merely because of this styling.

**Visual status:** Approved by PO for the supplied screenshots. **Not separately evidenced:** Arabic RTL browser interactions, small-screen behavior, or dark-mode admin modal rendering; these are targeted integration checks, not a reason to reopen accepted design or redo destructive UAT.

Pilot preview uses the ignored local SQLite `database/lyra-pilot.sqlite` and ignored `.env`. **Never commit local database, account credentials, or private materials.** The main MySQL data, original V03B feature branch, Draft PR #138 and P03-I remain untouched.

**Integration policy:** Keep this visual work stacked on `feat/137-v03b-edition-management` in a **Draft PR** until approved for integration. Do not merge into PR #138 or `phase/03-submission-metadata` automatically.
