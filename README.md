# ICHES Conference & Event Experience Platform

This repository is the canonical product, architecture, and implementation repository for a reusable multi-edition academic conference platform.

## Current state

**PRE-DEVELOPMENT — TWO-WEEK PLAN COMPLETE / IMPLEMENTATION START GATE READY**

Coding begins only after explicit Product Owner GO.

Current sequence:

PRODUCT DISCOVERY ✓
→ PRODUCT BLUEPRINT v1 ✓
→ CORRECTIVE PHASE 0 RE-BASELINE ✓
→ SUBMISSION & SCHOLARLY METADATA CONTRACT ✓
→ STACK + ERD FREEZE ✓
→ TWO-WEEK DEVELOPMENT PLAN ✓
→ IMPLEMENTATION START GATE ← CURRENT
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
- MySQL 9.7 LTS
- database queue/cache/session
- private-by-default Laravel Filesystem
- edition-scoped Spatie Permission Teams + Laravel Policies
- PDF driver abstraction with Browsershot default

## Development plan

See:
- docs/project-management/TWO_WEEK_DEVELOPMENT_PLAN_V1.md
- docs/project-management/IMPLEMENTATION_START_GATE_V1.md

First coding branch after GO:
- phase/01-foundation

## Canonical reading order

1. AGENTS.md
2. docs/product/PRODUCT_BLUEPRINT_V1.md
3. docs/ai-context/CURRENT_STATE.md
4. docs/requirements/v1/
5. docs/metadata/
6. docs/architecture/
7. docs/project-management/TWO_WEEK_DEVELOPMENT_PLAN_V1.md
8. docs/governance/DECISION_REGISTER.md
9. current relevant NFR documents

## Branch model

- main: stable/release baseline
- develop: integration baseline
- scoped branches: all work

Routine work must not be performed directly on main.