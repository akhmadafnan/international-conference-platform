# PF-01A — Frontend Prototype Bootstrap Plan

**Status:** DEFERRED — WAITING FOR DAY 1 FOUNDATION MERGE  
**Branch:** `proto/public-frontend-v1`  
**Parent baseline:** PF-00 CLOSED_GREEN

## Why this is deferred

While PF-01A was being prepared, `develop` advanced and froze the implementation architecture plus the Two-Week Development Plan.

The newer canonical plan requires the parallel frontend workstream to begin **after Day 1 foundation is merged**, so frontend work uses the real Laravel 13 + official Vue Starter Kit + Inertia skeleton rather than a temporary standalone Vite harness.

Therefore the temporary standalone bootstrap was removed from the branch HEAD without rewriting history.

## Frozen frontend stack

The current canonical stack is:

- Laravel 13 official Vue Starter Kit
- Vue 3
- TypeScript
- Inertia 3
- Tailwind CSS 4
- shadcn-vue
- Vite
- Vue I18n
- Lucide Vue
- id/en/ar
- Arabic RTL first-class

## Work allowed before Day 1 foundation merge

Continue documentation/design preparation only:

- public information architecture;
- visual direction;
- design tokens;
- reference register;
- component inventory;
- mock scenario design;
- homepage content hierarchy;
- visual acceptance criteria.

Do not create a competing frontend application skeleton.

## Resume condition

PF-01 implementation resumes after:

1. Product Owner GO is granted for V1 implementation;
2. `phase/01-foundation` completes its gate;
3. Day 1 foundation is merged into `develop`;
4. this frontend worktree synchronizes from that new `develop` baseline.

## First implementation after resume

Use the actual Laravel/Inertia/Vue application skeleton and implement the bounded public homepage design proof directly inside its `resources/js` structure.

No migration from a separate SPA should be necessary.
