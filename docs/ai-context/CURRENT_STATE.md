# Current Project State

**State ID:** ICHES-STATE-20260930-BLUEPRINT-V1  
**Status:** PRE-DEVELOPMENT — PRODUCT BLUEPRINT v1 COMPLETE  
**Implementation authorization:** NOT GRANTED  
**Repository:** akhmadafnan/international-conference-platform  
**Integration branch:** develop

## Current position

Product Discovery and Pre-Presentation Product Audit are complete.

For accelerated planning, the Product Owner instructed the project to assume the rational/simple Warek-domain choices that preserve a two-week V1 target. This is an explicit planning assumption and does not claim a real Warek signature.

Completed:
- Product Discovery ✓
- Pre-Presentation Product Audit ✓
- Warek I Decision Sheet ✓
- Accelerated domain assumption baseline ✓
- Product Blueprint v1 ✓
- Frontend Product Specification ✓

Current sequence:

PRODUCT BLUEPRINT v1 ✓
→ CORRECTIVE PHASE 0 RE-BASELINE ← CURRENT
→ SUBMISSION & SCHOLARLY METADATA CONTRACT
→ STACK + ERD FREEZE
→ TWO-WEEK DEVELOPMENT PLAN
→ CODING

## Accelerated V1 assumptions

- payment is event participation fee;
- rejected abstract remains participant, no automatic academic-rejection refund;
- fixed edition-configurable participation packages;
- package + optional simple participant category pricing;
- single-anonymous abstract review;
- one reviewer default;
- one normal abstract revision cycle;
- slides optional/configurable;
- offline-first accelerated edition;
- actual presenter confirmed after Full Article;
- Proceedings default publication destination;
- Selected Journal is an authorized override;
- award decision remains Committee-authoritative;
- Presenter may receive Participant + Presenter certificates;
- Best Article certificates go individually to all listed authors;
- Committee certificates supported;
- separated Finance / Academic / Event / Award / Publication / Certificate authorities.

## Frontend status

Public + participant/presenter/reviewer frontend specification is ready for parallel prototyping.

Technical direction remains candidate until Stack Freeze:
- Laravel 13 official Vue Starter Kit
- Vue 3
- TypeScript
- Inertia
- Tailwind
- shadcn-vue
- Vite
- Vue I18n
- Lucide Vue
- optional Inertia SSR for public pages

Admin/backoffice visual direction is shadcn-admin-like in interaction language, implemented using shadcn-vue and ICHES domain components. Detailed admin behavior waits for backend/ERD contracts.

## Next exact action

Execute **Corrective Phase 0 Re-baseline**.

The re-baseline must explicitly reconcile old payment/refund, lifecycle, publication-review, permission/authority, and requirement documents against Product Blueprint v1.

No coding yet.
