# ICHES Platform — Agent Instructions

This repository is documentation-governed. AI agents and developers must not invent product policy.

## Mandatory first read

Before proposing implementation, read in order:

1. `docs/product/PRODUCT_BLUEPRINT_V1.md`
2. `docs/governance/ACCELERATED_V1_DOMAIN_DECISIONS.md`
3. `docs/ai-context/CURRENT_STATE.md`
4. `docs/governance/CORRECTIVE_PHASE0_REBASELINE.md`
5. `docs/requirements/v1/V1_LIFECYCLE_BASELINE.md`
6. `docs/requirements/v1/V1_PAYMENT_REFUND_BASELINE.md`
7. `docs/requirements/v1/V1_ACADEMIC_REVIEW_BASELINE.md`
8. `docs/requirements/v1/V1_PUBLICATION_BASELINE.md`
9. `docs/requirements/v1/V1_AUTHORITY_BASELINE.md`
10. `docs/governance/DECISION_REGISTER.md`
11. current relevant NFR documents
12. active issue/ticket

Frontend-specific work must also read:
- `docs/frontend/FRONTEND_PRODUCT_SPEC_V1.md`
- `docs/frontend/AI_FRONTEND_HANDOFF.md`

## Source-of-truth order

When sources conflict:

1. Product Blueprint v1;
2. authoritative V1 requirement baselines in `docs/requirements/v1/`;
3. Accelerated V1 Domain Decisions;
4. Current State;
5. current Decision Register;
6. non-conflicting NFR/security/privacy/accessibility/localization baselines;
7. historical Phase 0 documents;
8. GitHub issue/ticket;
9. chat context;
10. AI assumptions.

Do not silently resolve contradictions.

## Current hard gate

Implementation is still NOT authorized until:
- Submission & Scholarly Metadata Contract is complete;
- Stack + ERD are frozen;
- Two-Week Development Plan is approved.

Do not:
- code from historical superseded lifecycle rules;
- restore automatic academic-rejection refund;
- create abstract drafts before registration/payment confirmation;
- build a separate publication peer-review engine for accelerated V1;
- invent admin authorities;
- mix unrelated frontend architectures.

## Product principles

- Simple CRUD where CRUD is enough.
- Smart/personal UX with a strong Next Action model.
- Dashboard is participant source of truth.
- Email is notification, not business truth.
- id/en/ar; Arabic RTL first-class.
- Payment, academic acceptance, presentation, publication, awards, and certificates are separate facts.
- OJS is downstream.
- Canonical scholarly metadata feeds adapters.
- Committee decisions remain human-authoritative.

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
- Update canonical docs when accepted behavior changes.

## Implementation handoff

Report ticket, branch, scope, files changed, behavior delivered, migrations, tests, regression, UAT, docs updated, known limitations, and commit/PR.
