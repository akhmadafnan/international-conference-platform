# ICHES Conference & Event Experience Platform

This repository is the canonical product, architecture, and implementation repository for a reusable multi-edition academic conference platform.

## Current state

**PRE-DEVELOPMENT — ARCH-001 GREEN / TWO-WEEK DEVELOPMENT PLAN CURRENT**

Feature coding is not yet authorized.

Current sequence:

PRODUCT DISCOVERY ✓
→ PRODUCT BLUEPRINT v1 ✓
→ CORRECTIVE PHASE 0 RE-BASELINE ✓
→ SUBMISSION & SCHOLARLY METADATA CONTRACT ✓
→ STACK + ERD FREEZE ✓
→ TWO-WEEK DEVELOPMENT PLAN ← CURRENT
→ IMPLEMENTATION START GATE
→ CODING

## Frozen stack

- PHP 8.4
- Laravel 13 modular monolith
- Official Laravel Vue Starter Kit
- Inertia 3
- Vue 3 + TypeScript
- Tailwind CSS 4
- shadcn-vue
- Vite
- Vue I18n
- Lucide Vue
- MySQL 8.4 LTS
- database queue/cache/session
- private-by-default Laravel Filesystem
- Spatie Permission with edition/team scope + Laravel Policies
- PDF driver abstraction with Browsershot default

## Product DNA

The platform remains:
- operationally simple for a conference of roughly 100 participants;
- multi-edition and reusable;
- participant-friendly and mobile-first;
- multilingual: Indonesian, English, Arabic with first-class RTL;
- CRUD-first where CRUD is enough;
- state-aware rather than menu-heavy;
- auditable for consequential actions;
- publication-ready without becoming an OJS replacement;
- standards-aware through canonical scholarly metadata and adapters.

## Canonical reading order

1. AGENTS.md
2. docs/product/PRODUCT_BLUEPRINT_V1.md
3. docs/ai-context/CURRENT_STATE.md
4. docs/requirements/v1/
5. docs/metadata/
6. docs/architecture/TECH_STACK_FREEZE_V1.md
7. docs/architecture/ERD_V1.md
8. docs/architecture/ARCHITECTURE_DECISIONS_V1.md
9. docs/architecture/IMPLEMENTATION_CONVENTIONS_V1.md
10. docs/governance/DECISION_REGISTER.md
11. current relevant NFR documents

Historical Phase 0 requirement documents remain provenance only where superseded.

## Branch model

- main: stable/release baseline
- develop: integration baseline
- scoped branches: all work

Routine work must not be performed directly on main.
