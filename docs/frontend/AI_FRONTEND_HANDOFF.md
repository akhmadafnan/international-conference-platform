# AI Frontend Handoff Prompt — ICHES

You are responsible only for the **public and authenticated user-facing frontend experience** of the ICHES Conference & Event Experience Platform.

Read first:
1. docs/frontend/FRONTEND_PRODUCT_SPEC_V1.md
2. docs/product-discovery/PRODUCT_DNA.md
3. docs/ai-context/CURRENT_STATE.md
4. PRD.md

## Your mission

Build a high-quality frontend prototype/shell that can be integrated later with the Laravel backend without changing product rules.

## Hard boundaries

Do not:
- change Product DNA;
- invent business states;
- modify payment/review/publication/certificate rules;
- freeze database schema;
- build admin backoffice;
- add backend APIs;
- claim unfinished mock behavior is production behavior.

Use typed mock data and switchable mock scenarios.

## Scope

Build:
- public website;
- multilingual id/en/ar shell;
- Arabic RTL;
- participant dashboard;
- payment UI;
- Event Pass;
- abstract submission wizard;
- submission status;
- LoA;
- Full Article;
- presenter confirmation;
- schedule;
- presentation/revision;
- publication status;
- documents;
- notifications/help/profile;
- reviewer-facing assignment workspace.

## UX principle

The participant should always understand:
1. current status;
2. next action;
3. available documents/information.

## Technical direction

If implementation is requested, align with the project candidate stack:
- Vue 3
- TypeScript
- Inertia-compatible page/component structure
- Tailwind CSS
- shadcn-vue

Do not introduce another UI framework without explicit approval.

## Delivery style

Work in a scoped frontend branch from develop.

Prefer reusable components, responsive design, and mock state fixtures.

Before handoff, demonstrate every required mock scenario in FRONTEND_PRODUCT_SPEC_V1.md.
