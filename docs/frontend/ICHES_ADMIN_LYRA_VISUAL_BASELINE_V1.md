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

## Amendment V1.1 — Palette & Component Contract (PO directive, 2026-10-19)

**Status:** APPROVED DIRECTION; implementation subject to the usual build and scoped browser verification. This amendment supplements V1 without changing the accepted admin appearance.

### Normative design authority

- The **approved Lyra Dashboard** is the visible color/spacing reference; the official preset `a33J5lFw` is the component/style authority. Do not revert to legacy green classes or to generic shadcn defaults when building another admin screen.
- Preset identity remains **Reka UI + Lyra + Neutral / Emerald + JetBrains Mono / Geist Mono + Noto Sans Arabic + Phosphor + compact square geometry**.
- The canonical admin design-token values live in `resources/css/admin-lyra.css`, under `.workspace-shell.admin-workspace.admin-lyra` and its explicitly scoped Reka portalled surfaces. Use semantic Tailwind tokens (`bg-primary`, `text-primary-foreground`, `bg-card`, `bg-muted`, `border-border`, `text-muted-foreground`, `text-destructive`, etc.), never ad hoc `bg-green-700`, `text-green-*`, hex, or one-off palette copies inside admin pages.
- **Emerald** is the primary/active emphasis. Neutral controls, borders, surfaces, and backgrounds stay restrained. **Destructive red** is reserved for destructive operations and validation errors, not primary navigation. Appearance switching must use the same semantic contract in dark mode; Arabic RTL must remain supported.
- In admin pages use the existing shadcn-vue **Button, Card, Badge, Input, Label, Select, Textarea, Dialog** primitives and Reka patterns where applicable. Components installed globally must **not** be reinitialized or regenerated; the preset is adapted through admin-only styling and composition, leaving public/auth/settings/participant workspaces intact.
- User-facing admin action icons should use **Phosphor**. Existing third-party/internal primitive icons outside explicit admin action surfaces may remain until scoped equivalents are implemented; do not globally change shared primitives merely to swap their internal icons.
- Data tables share `.admin-data-table` and semantic header/border/hover states; paginated active items use `bg-primary` / `text-primary-foreground`. Admin heading and page padding match the UAT-approved full-width Dashboard/Series/Edition index (`w-full`, `p-4 md:p-8`), except narrow form layouts where deliberate.
- Form submit buttons use the semantic default `Button` variant, cancellation uses `outline`, and deletion uses `destructive` with the existing explicit typed-confirmation requirement. Never modify the deletion action or its authorization as part of a theme update.
- New admin modules must reuse these primitives and semantic tokens; no new isolated dashboard theme, manual green CTA style, duplicate component family, custom chart/statistics or generic SaaS demo content. Changes to the frozen palette/components require explicit Product Owner review and amendment.

### Automated verification

- Run `npm run check:admin-lyra` when creating or changing admin screens; it checks hardcoded palette utilities, legacy Lucide admin-action imports, and native select/textarea usage in admin pages.
- Run TypeScript, scoped format check, production build, and focused regressions. Visual UI acceptance alone does not establish proof of dark-mode portalled select/dialogs, Arabic RTL, mobile widths or zoom behavior.
- `components.json` remains the global non-admin registry configuration; it **must not** override this admin-only Lyra contract. The admin pilot PR stays stacked/draft until a separate explicit merge decision.
