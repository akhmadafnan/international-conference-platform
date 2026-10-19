# P03-I Author Submission Workspace — HTTP/UI Integration

**Status:** code/test GREEN in isolated Laptop 2 SQLite; PO browser UAT and live MySQL integration **PENDING**. Builds upon PR #145; admin Lyra V1.1 frozen.

## Implemented
- Author's own eligible registration and drafts at `/dashboard/submissions`; creates DRAFT without advance presenter payment. Metadata, tracks and keywords in id/en/ar.
- Owner-scoped staged edits: contributors (multiple authors, affiliations, corresponding author), references, PDF/DOCX uploads to protected private storage, read-only readiness findings.
- Submission POST delegates exclusively to `SubmitDraftAbstractAction`: authoritative window, policy and readiness checks; immutable initial snapshot, audit, locked SUBMITTED; no duplicate official submission.
- Admin Edition Academic Setup adds audited explicit abstract policy: min/max keywords, track required, manuscript file required. Preserves unrelated Edition settings. Absent policy stays BLOCKED until explicitly configured.
- Existing V04-B active `ABSTRACT_SUBMISSION` window remains mandatory. The policy screen does not silently open a window.
- Inertia UI uses shadcn-vue primitives; no broad Lyra CSS modifications. No new schema/migrations/seeds and no MySQL real-data mutation.
- Author/registration ownership and file validation are backend-enforced; Superadmin's visual role switch remains **not impersonation** (it will show only own registrations).

## Isolated verification
- Focused test coverage includes auth/ownership, unpaid presenter DRAFT, metadata save, contributor and reference replacement, valid private PDF upload and forged PDF rejection, readiness BLOCKED/WARNING, official DRAFT→SUBMITTED, snapshot exactly-once, immutable post-submit, abstract-policy role control/audit.
- Full Pest, PHPStan, Pint, Vue typecheck, Vite production build and Lyra contract gates required.
- Before user UAT, ensure test Edition has explicit abstract policy, active submission window and an actual registration for the logged-in user; make no fabricated registrations in the real database.
- Browser UAT remains necessary for forms, upload, all three languages, RTL, mobile, accessibility and review of final status.

## Not in this phase
- Review assignments/anonymous review, screening, academic acceptance, full paper, finance settlement, LoA, presenter scheduling/check-in, certificates and OJS handoff. The full conference platform remains **NOT release-complete**.
