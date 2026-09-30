# ICHES Conference & Event Experience Platform

This repository is the working product-definition repository for a reusable international academic conference platform, currently shaped around the ICHES series and its recurring editions.

## Current state

**PRE-DEVELOPMENT PRODUCT DISCOVERY — substantially complete, not yet frozen**

No application implementation is authorized yet.

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

## Product DNA

The platform must remain:

- operationally simple for a conference of roughly 100 participants;
- multi-edition and reusable;
- participant-friendly and mobile-first;
- multilingual: Indonesian, English, Arabic with first-class RTL;
- CRUD-first where CRUD is enough;
- state-aware rather than menu-heavy;
- auditable for consequential actions;
- publication-ready without becoming an OJS replacement;
- standards-aware through canonical scholarly metadata and export/adapters.

## Canonical reading order

1. AGENTS.md
2. docs/product-discovery/PRODUCT_DNA.md
3. docs/ai-context/CURRENT_STATE.md
4. PRD.md
5. docs/product-discovery/PRE_PRESENTATION_PRODUCT_AUDIT.md
6. docs/product-discovery/WAREK_I_PRODUCT_DECISION_SHEET.md
7. docs/governance/DECISION_REGISTER.md
8. docs/ai-context/INTERNATIONAL_CONFERENCE_PROJECT_CANONICAL_CONTEXT.md

Older requirement and NFR documents remain valuable historical evidence. Where they conflict with the current Product DNA, they are not authoritative until the formal Corrective Phase 0 Re-baseline reconciles them.

## Branch model

- main: stable/release baseline
- develop: integration baseline
- scoped branches: all work

Routine work must not be performed directly on main.
