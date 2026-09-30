# Current Project State

**State ID:** ICHES-STATE-20260930-PD-AUDIT  
**Status:** PRE-DEVELOPMENT PRODUCT DISCOVERY — PRE-PRESENTATION AUDIT COMPLETE  
**Implementation authorization:** NOT GRANTED  
**Repository:** akhmadafnan/international-conference-platform  
**Integration branch:** develop

## Current position

The initial Phase 0 documentation completed substantial lifecycle, permission, and NFR work. Subsequent direct Product Discovery with the Product Owner materially refined the product toward a simpler conference experience.

Current sequence:

PRODUCT DISCOVERY
→ PRE-PRESENTATION PRODUCT AUDIT
→ WAREK I PRODUCT DECISION SHEET
→ WAREK I VALIDATION
→ PRODUCT BLUEPRINT v1
→ CORRECTIVE PHASE 0 RE-BASELINE
→ SUBMISSION & SCHOLARLY METADATA CONTRACT
→ STACK + ERD FREEZE
→ DEVELOPMENT PLAN
→ CODING

## Product Discovery status

Sufficiently mature:
- Registration & participation packages
- Payment at beginning
- Event Pass / QR
- Abstract submission
- Contributors / ROR / optional ORCID
- References
- Administrative screening
- Single-anonymous abstract review
- Presentation LoA
- Full Article after acceptance
- Presenter confirmation
- Scheduling Pool and bulk scheduling
- Reviewer presentation workspace
- PRESENTED / NO_SHOW split
- Presentation assessment
- Revision / No Revision
- Best Article / Best Presenter
- Committee-finalized award model
- Document & Communication model
- Certificate eligibility/generation/issuance
- Publication Destination & Production Handoff
- Canonical scholarly metadata direction
- Public Website IA
- Participant/Presenter dashboard experience
- Multi-edition Series vs Edition model

## Critical current rules

- All participants pay before entering the academic submission path.
- Payment verification confirms registration.
- Presenter is not chosen as a registration role; submitting an abstract starts the author/presenter lifecycle.
- LoA means accepted for presentation.
- Full Article upload opens after acceptance/LoA.
- Only Full Article submissions enter Scheduling Pool.
- Actual presenter is explicitly confirmed from contributors.
- Room/session are assigned after Full Article submission.
- Reviewer access is assignment-scoped.
- Moderator/Event Operations records PRESENTED/NO_SHOW.
- Reviewer records academic assessment.
- No Revision means no extra manuscript upload.
- Revision Required opens a new Revised Article version.
- Awards are Committee decisions recorded by the system; ranking/candidates are evidence only.
- Best Article certificates go to all listed authors individually.
- Best Presenter is currently designed as an overall edition award.
- Presenter may receive both Participant and Presenter certificates.
- Committee certificates are supported.
- Publication destination is Proceedings or Selected Journal.
- Selected for Journal does not equal Accepted by Journal.
- Canonical scholarly metadata feeds OJS/Crossref adapters.
- Public UI is id/en/ar with Arabic RTL.
- Dashboard is state-aware and centered on Next Action.

## Open domain decisions for Warek I

Primary unresolved business/policy questions include:
- treatment of paid participant when abstract is rejected;
- fixed package vs package customization;
- final fee matrix / participant categories;
- default reviewer count;
- abstract revision cycle policy;
- required presentation slides;
- hybrid/offline mode per edition;
- publication default destination;
- exact application authority holders;
- formal certificate/award wording and publication-acceptance use.

## Legacy Phase 0 status

Previous detailed permission and NFR work remains valuable.

However, old lifecycle/payment/refund/publication documents may conflict with the current Product DNA. They are retained for evidence and must be reconciled during Corrective Phase 0 Re-baseline.

Do not silently treat old conflicting decisions as current.

## Candidate stack

NOT FROZEN:
- Laravel 13
- Vue 3
- TypeScript
- Inertia
- Tailwind
- shadcn-vue

## Next exact action

Prepare and validate the Warek I Product Decision Sheet.

No coding, ERD freeze, or implementation planning before domain validation.
