# PF-00 — Public Frontend Baseline Freeze

**Status:** CLOSED_GREEN  
**Branch:** `proto/public-frontend-v1`  
**Baseline source:** `develop@dbde5bf`  
**Scope:** Documentation only; no application code implemented.

## Objective

Freeze the public frontend direction before visual implementation so the frontend prototype can proceed independently without changing product/domain policy.

## Completed

- Public frontend design direction defined.
- AICIS+ role frozen as information architecture/product reference.
- ForumX role frozen as visual/composition reference.
- Original ICHES identity requirement recorded.
- Public navigation and homepage section architecture defined.
- Provisional design tokens and visual principles defined.
- Vue/TypeScript/Inertia-compatible destination structure defined.
- Component boundary defined.
- Typed mock-data boundary defined.
- id/en/ar + Arabic RTL requirements reinforced.
- AI frontend handoff narrowed to PF-01.
- PF-00 decisions recorded in Decision Register.

## Canonical PF-00 documents

- `docs/frontend/FRONTEND_PRODUCT_SPEC_V1.md`
- `docs/frontend/PUBLIC_FRONTEND_DESIGN_DIRECTION.md`
- `docs/frontend/PUBLIC_INFORMATION_ARCHITECTURE.md`
- `docs/frontend/PUBLIC_DESIGN_SYSTEM.md`
- `docs/frontend/PUBLIC_COMPONENT_MOCK_CONTRACT.md`
- `docs/frontend/FRONTEND_REFERENCE_REGISTER.md`
- `docs/frontend/AI_FRONTEND_HANDOFF.md`

## No-code guarantee

PF-00 introduced no:

- Laravel code;
- Vue page/component code;
- backend API;
- migration/schema;
- authentication changes;
- business workflow;
- production behavior.

## Next phase

```text
PF-01 — PUBLIC HOMEPAGE DESIGN PROOF
```

PF-01 authorized scope:

- project frontend prototype baseline as needed;
- design tokens;
- public header;
- announcement bar;
- mobile navigation;
- language switcher;
- public homepage;
- public footer;
- id/en/ar shell;
- Arabic RTL proof;
- typed mock data;
- responsive design.

Not authorized:

- dashboard;
- payment;
- submission workflow;
- reviewer workspace;
- backend/domain integration;
- database/schema changes.

## PF-01 exit gate

PF-01 cannot close until:

- desktop visual UAT passes;
- mobile visual UAT passes;
- RTL structural UAT passes;
- site does not resemble generic SaaS/shadcn demo;
- site remains recognizably an international academic conference;
- mock data is replaceable by typed page props;
- no product policy is invented;
- applicable frontend quality checks are green.

After PF-01 visual UAT, correct failures before expanding to other public pages.
