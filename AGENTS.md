# ICHES Platform — Agent Instructions

This repository is documentation-governed. AI agents and developers must not invent product policy or treat exploratory discussion as implementation authorization.

## Mandatory first read

Before proposing implementation, read in order:

1. docs/product-discovery/PRODUCT_DNA.md
2. docs/ai-context/CURRENT_STATE.md
3. PRD.md
4. docs/product-discovery/PRE_PRESENTATION_PRODUCT_AUDIT.md
5. docs/product-discovery/WAREK_I_PRODUCT_DECISION_SHEET.md
6. docs/governance/DECISION_REGISTER.md
7. docs/ai-context/INTERNATIONAL_CONFERENCE_PROJECT_CANONICAL_CONTEXT.md
8. relevant requirement/NFR documents
9. active issue/ticket

## Source-of-truth order during current discovery stage

When sources conflict:

1. current Product DNA and explicitly accepted Product Discovery decisions;
2. CURRENT_STATE;
3. current PRD;
4. current Decision Register;
5. accepted NFR/security/accessibility/localization baselines that do not conflict;
6. older Phase 0 requirement documents;
7. GitHub issue/ticket;
8. chat context;
9. AI assumptions.

Do not silently resolve contradictions. Record them for Corrective Phase 0 Re-baseline.

## Current hard gate

Implementation is NOT authorized.

Do not:
- generate application code;
- freeze ERD;
- freeze stack;
- treat old Phase 0 lifecycle/payment/publication assumptions as current when they conflict with Product DNA;
- perform corrective re-baseline before Warek I validation.

## Product principles

- Simple CRUD where CRUD is enough.
- Smart/personal UX with a strong Next Action model.
- Dashboard is the participant source of truth.
- Email is notification, not business truth.
- Public UI supports id/en/ar; Arabic RTL is first-class.
- Participant account role is not a substitute for lifecycle facts.
- Payment, academic acceptance, presentation, publication, awards, and certificates are separate facts.
- OJS is downstream; this platform must not become OJS 2.0.
- Canonical scholarly metadata feeds OJS/Crossref adapters.
- Committee decisions remain human-authoritative; the system supplies evidence and records the final decision.

## Working workflow

DISCUSS
→ AUDIT / ANALYZE
→ DOCUMENT
→ PLAN
→ READY GATE
→ IMPLEMENT
→ REVIEW
→ REGRESSION
→ UAT
→ CLOSEOUT

Do not continue through a failed gate.

## Git rules

- Do not work directly on main.
- Use scoped branches from develop.
- Preserve history.
- Prefer PR to develop.
- Exact-scope commits only.
- Documentation that changes product truth must update CURRENT_STATE and canonical context.

## Implementation handoff

When implementation eventually begins, report:
- ticket;
- branch;
- scope;
- files changed;
- behavior delivered;
- migrations;
- tests;
- regression;
- UAT;
- docs updated;
- known limitations;
- commit/PR.
