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
10. `docs/metadata/SUBMISSION_SCHOLARLY_METADATA_CONTRACT_V1.md`
11. `docs/metadata/METADATA_FIELD_DICTIONARY_V1.md`
12. `docs/metadata/OJS_CROSSREF_ADAPTER_CONTRACT_V1.md`
13. `docs/metadata/METADATA_READINESS_RULES_V1.md`
14. `docs/architecture/TECH_STACK_FREEZE_V1.md`
15. `docs/architecture/ERD_V1.md`
16. `docs/architecture/ARCHITECTURE_DECISIONS_V1.md`
17. `docs/architecture/IMPLEMENTATION_CONVENTIONS_V1.md`
18. `docs/governance/DECISION_REGISTER.md`
19. current relevant NFR documents
20. active issue/ticket

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

## Current gate

The live implementation gate is determined by `docs/ai-context/CURRENT_STATE.md` plus current Git/GitHub evidence.

Phase 02 — Registration + Payment is currently AUTHORIZED / IN PROGRESS. The Two-Week Development Plan, stack, and ERD baseline are already approved/frozen for the current V1 direction.

Do not start Phase 03 merely because Phase 02 implementation appears complete. A new phase requires the previous phase exit gate to be CLOSED_GREEN, integration into `develop`, and a new explicit Product Owner GO.

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

For ordinary scoped work, branch from synchronized `develop`.

For a bounded issue inside an active phase, use the nested delivery rule:

```text
active phase branch
→ agent/<issue>-<scope> or another bounded child branch
→ bounded implementation + real scope-appropriate gates
→ Draft PR back to the active phase branch
→ human review/acceptance
→ merge to the active phase branch
→ only the phase PR integrates the completed phase into develop
```

Agent/AI worker guardrails:
- keep task contracts tiny and exact;
- read only the context needed to edit safely;
- AI execution success is not implementation GREEN—run syntax/tests/static analysis appropriate to the change;
- do not repeatedly retry the same failing AI strategy; use a deterministic bounded correction when appropriate;
- prepare least-privilege Git commit capability before execution rather than applying ad-hoc broad permission changes;
- never auto-merge a phase PR, merge to `develop`/`main`, force-push, change secrets, or deploy production.


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