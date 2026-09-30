# AI Frontend Handoff Prompt — ICHES

You are responsible only for the **public and authenticated user-facing frontend experience** of the ICHES Conference & Event Experience Platform.

## Mandatory first read

Read in this order:

1. docs/product/PRODUCT_BLUEPRINT_V1.md
2. docs/product-discovery/PRODUCT_DNA.md
3. docs/ai-context/CURRENT_STATE.md
4. docs/frontend/FRONTEND_PRODUCT_SPEC_V1.md
5. docs/frontend/PUBLIC_FRONTEND_DESIGN_DIRECTION.md
6. docs/frontend/PUBLIC_INFORMATION_ARCHITECTURE.md
7. docs/frontend/PUBLIC_DESIGN_SYSTEM.md
8. docs/frontend/PUBLIC_COMPONENT_MOCK_CONTRACT.md
9. docs/frontend/FRONTEND_REFERENCE_REGISTER.md
10. PRD.md

Also obey root `AGENTS.md` and relevant V1/NFR documents.

## Current frontend workstream

Branch:

```text
proto/public-frontend-v1
```

Current frontend phase:

```text
PF-00 — PUBLIC FRONTEND BASELINE FREEZE
→ PF-01 — HOMEPAGE DESIGN PROOF
```

This branch is a **parallel frontend prototype workstream**. It does not authorize general product/backend implementation.

## Mission

Build a high-quality frontend prototype/shell that can be integrated later with the Laravel backend without changing product rules.

The public experience must:

- preserve the information maturity expected from an international academic conference;
- follow the original ICHES design direction;
- use AICIS+ only as a product/information-architecture reference;
- use ForumX only as a visual/composition reference;
- never visually clone either reference.

## Hard boundaries

Do not:

- change Product DNA;
- invent business states;
- modify payment/review/publication/certificate rules;
- freeze database schema;
- build admin backoffice;
- add backend APIs;
- claim unfinished mock behavior is production behavior;
- introduce Vue Router as the application router;
- introduce a second frontend framework;
- copy reference-site branding, copy, imagery, or source implementation.

Use typed mock data and switchable mock scenarios.

## PF-01 bounded scope

The next implementation phase is limited to:

- public header;
- announcement bar;
- mobile navigation;
- language switcher;
- public homepage;
- public footer;
- id/en/ar shell;
- Arabic RTL proof;
- responsive behavior;
- typed mock fixtures;
- reusable public components.

PF-01 does **not** include:

- participant dashboard;
- payment UI;
- Event Pass;
- submission wizard;
- authenticated workflow;
- reviewer workspace;
- backend integration;
- authentication;
- real uploads/documents;
- business-state implementation.

Those remain within the larger Frontend Product Specification but are not authorized in PF-01.

## UX principle

The participant should always understand:

1. current status;
2. next action;
3. available documents/information.

For the public site, visitors should immediately understand:

1. what ICHES is;
2. when and where the edition takes place;
3. what the conference theme/tracks are;
4. what action they should take next.

## Technical direction

Keep implementation compatible with:

- Vue 3
- TypeScript
- Inertia-compatible page/component structure
- Tailwind CSS
- shadcn-vue
- Vite
- Vue I18n
- Lucide Vue

Do not introduce another UI framework without explicit approval.

Do not create a separate REST API.

Prefer Inertia-compatible links, page props, and form architecture.

## Delivery style

Work only on a scoped frontend branch/worktree from `develop`.

Prefer reusable components, responsive design, centralized typed mock fixtures, and clean component contracts.

Before handoff:

- demonstrate desktop/mobile;
- demonstrate id/en/ar;
- demonstrate Arabic RTL;
- document known visual limitations;
- report exact files changed;
- run applicable frontend checks;
- complete visual UAT before expanding scope.
