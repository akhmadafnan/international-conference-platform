# P03-I — ROR-first Affiliations + Isolated Local UAT Fixtures

Status: code gates GREEN; integrated browser UAT still required.

## Existing canonical decisions
- `PRD.md` §7, `docs/architecture/ARCHITECTURE_DECISIONS_V1.md` ADR-013, and `docs/metadata/SUBMISSION_SCHOLARLY_METADATA_CONTRACT_V1.md`: **ROR-first with truthful manual fallback**; lookup uses the whole institution, not faculty/department. Optional subdivisions are separate fields.
- `institutions` already stores `ror_uri`, while contributor affiliations store `institution_id` and immutable `ror_uri_snapshot`; no database migration.
- External ROR v2 `?query=` is used for name autocomplete, *not* `?affiliation=` automatic matching; the author must explicitly select a result.
- Official ROR record is re-fetched on the server before inserting a canonical `Institution` (prevents client-supplied fake names). REST URL is a constant; ROR ID undergoes strict validation; API responses are timeout-bound, validated, and cached. Outages never block a truthful manual affiliation.
- UI uses existing shadcn-vue Input/Button and the frozen Lyra tokens in id/en/ar, with keyboard-focusable suggested results; manual input accepted. Browser accessibility/RTL UAT remains necessary.

## Local-only opt-in demo
The `LocalConferenceUatSeeder` **is not** registered in `DatabaseSeeder`. It refuses production and requires explicit `ICHES_DEMO_ROLE_SEED_ENABLED` and a strong supplied `ICHES_DEMO_ROLE_PASSWORD`. Its scope is a separate `ICHES-UAT` Conference Series and `ICHES-UAT-2027` Edition, visibly marked DEMO. It never modifies the true `ICHES-2027` edition.

The UAT Edition has the isolated abstract readiness policy, sample free/paid packages, registration and abstract windows, and 18 clearly marked persona accounts. Only the DEMO author gets a sample registration intent through the genuine Phase02 domain action. The author creates the abstract themselves through the wizard; no fake real-world academic/paper/finance decisions are generated. Running the seeder again must be idempotent.

No demo passwords are committed. For the separately authorized Laptop 2 seed, the strong password should be randomly generated and saved using Windows DPAPI for the currently authenticated Windows user, outside Git. The user can retrieve it locally when running browser UAT.

## Smoke routes
- `/dashboard/submissions` — author-owned submission list and wizard
- `/dashboard/institutions/search?q=University` — authenticated JSON ROR/local institution suggestion
- `/dashboard/institutions/resolve` — authenticated POST-only canonical ROR selection
- `/admin/editions/{edition}/academic` — explicit abstract policy; official ICHES-2027 remains untouched
- Use ordinary demo author login for end-to-end mutations. Superadmin switching to Author is only a *view mode*, not impersonation.

## Follow-up gates
Full Pest, PHPStan, Pint, Vue typecheck/build, Lyra contract; real browser id/en/ar RTL, reduced-motion, 390px and 200% zoom, accessibility, ROR selected/manual, and snapshot after SUBMITTED. Draft PR remains unmerged until integration review.
