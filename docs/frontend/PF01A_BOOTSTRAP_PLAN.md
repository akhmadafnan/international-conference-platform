# PF-01A — Frontend Prototype Bootstrap Plan

**Status:** READY FOR EXECUTION  
**Branch:** `proto/public-frontend-v1`  
**Parent baseline:** PF-00 CLOSED_GREEN

## Goal

Create the minimum Vue/TypeScript/Vite frontend harness required to begin the ICHES public homepage design proof without coupling the prototype to unfinished Laravel/backend implementation.

## Technical baseline

- Vue 3
- TypeScript
- Vite
- Tailwind CSS v4 via `@tailwindcss/vite`
- shadcn-vue-compatible structure
- Vue I18n
- Lucide Vue
- Reka UI direction provider for RTL-aware primitives

No Vue Router.
No REST API.
No Laravel/Inertia runtime wiring yet.
No authenticated workflow.

## Destination-oriented source root

```text
resources/js/
├── components/
│   ├── ui/
│   ├── shared/
│   └── public/
├── layouts/
├── pages/
│   └── public/
├── composables/
├── i18n/
├── mocks/
├── types/
├── lib/
├── styles/
├── App.vue
└── main.ts
```

The `@` alias points to `resources/js`.

## PF-01A deliverables

- `package.json`
- Vite config
- TypeScript configs
- `index.html`
- shadcn-vue `components.json`
- Tailwind v4 global stylesheet
- `cn()` utility
- Vue I18n bootstrap
- id/en/ar locale proof
- runtime LTR/RTL document direction proof
- minimal public prototype status page

## Quality gate

Before PF-01A closes:

1. `npm install` completes.
2. `npm run typecheck` passes.
3. `npm run build` passes.
4. language switching works for id/en/ar.
5. Arabic switches the document to RTL.
6. working tree diff is reviewed.
7. package lock is committed.
8. no backend/domain behavior is introduced.

## Next

PF-01B — Design Tokens + Public Shell.
