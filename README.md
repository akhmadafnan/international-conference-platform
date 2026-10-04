# ICHES Conference & Event Experience Platform

This repository is the canonical product, architecture, and implementation repository for a reusable multi-edition academic conference platform.

## Current state

**PHASE 02 — REGISTRATION + PAYMENT — IN PROGRESS**

Phase 01 Foundation is `CLOSED_GREEN` and merged into `develop` through PR #57.

The Product Owner explicitly authorized Phase 02 on 2026-10-03. The active implementation branch is `phase/02-registration-payment`, with Draft phase PR #61 targeting `develop`.

The bounded security regression for cross-payment proof isolation was accepted and merged through PR #77 into the active Phase 02 branch. Phase 02 is **not** yet CLOSED_GREEN: Event Pass, the participant-facing browser flow, remaining roadmap disposition, and final exit evidence are still required.

Current sequence:

PRODUCT / REQUIREMENTS / METADATA / ARCHITECTURE ✓
→ TWO-WEEK DEVELOPMENT PLAN ✓
→ IMPLEMENTATION START GATE ✓
→ PHASE 01 FOUNDATION ✓ CLOSED_GREEN
→ PR #57 / MERGE TO develop ✓
→ META-AUDIT-001 ✓ CLOSED_GREEN
→ PRE-PHASE02 REBASELINE ✓ CLOSED_GREEN
→ PRODUCT OWNER PHASE 02 GO ✓
→ PHASE 02 — REGISTRATION + PAYMENT ← CURRENT
→ PHASE 02 EXIT / PR #61 HUMAN REVIEW
→ next phase only after explicit new Product Owner GO

## Frozen stack

- PHP 8.4 target runtime
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

Historical Phase 01 implementation branch:
- `phase/01-foundation`

Active Phase 02 implementation branch:
- `phase/02-registration-payment`
- Draft phase PR: #61 → `develop`

META-AUDIT work follows the normal scoped-branch → PR → `develop` integration workflow.

## Canonical reading order

Start every substantial development session with:

1. `docs/ai-context/CURRENT_STATE.md`
2. `docs/governance/WORKING_PROTOCOL.md`
3. `docs/ai-context/INTERNATIONAL_CONFERENCE_PROJECT_CANONICAL_CONTEXT.md`

Then load only the additional authoritative product, architecture, testing, or domain documents required by the current gate.

Historical/provenance documents do not override later accepted canonical decisions.

## Branch model

- main: stable/release baseline
- develop: integration baseline
- scoped branches: all work

Routine work must not be performed directly on main.
