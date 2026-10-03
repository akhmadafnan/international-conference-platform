# ICHES Conference & Event Experience Platform

This repository is the canonical product, architecture, and implementation repository for a reusable multi-edition academic conference platform.

## Current state

**META-AUDIT-001 CLOSED_GREEN — PRODUCT OWNER DISCUSSION HOLD / PHASE 02 LOCKED**

Phase 01 Foundation is `CLOSED_GREEN` and merged into `develop` through PR #57 at `e8141e9`.

The documentation/workflow meta-audit is complete. Application development remains paused for Product Owner discussion/review before any Phase 02 authorization.

Current sequence:

PRODUCT / REQUIREMENTS / METADATA / ARCHITECTURE ✓
→ TWO-WEEK DEVELOPMENT PLAN ✓
→ IMPLEMENTATION START GATE ✓
→ PHASE 01 FOUNDATION ✓ CLOSED_GREEN
→ PR #57 / MERGE TO develop ✓
→ META-AUDIT-001 ✓ CLOSED_GREEN
→ PRODUCT OWNER DISCUSSION / REVIEW ← CURRENT
→ PHASE 02 only after explicit new GO
→ V1 RELEASE CANDIDATE

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
