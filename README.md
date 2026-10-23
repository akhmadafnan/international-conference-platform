# ICHES Conference & Event Experience Platform

This repository is the canonical product, architecture, and implementation repository for a reusable multi-edition academic conference platform.

## Current state

Read [CURRENT_STATE](docs/ai-context/CURRENT_STATE.md) for the 2026-10-23 local checkpoint and the [ICHES 2027 Operational Rebaseline](docs/governance/ICHES_2027_OPERATIONAL_REBASELINE_2026_10_23.md) for approved product behavior. Phase03 browser UAT PASS; Phase04 Academic Core exists locally with full end-to-end UAT incomplete. The approved Self-Claim/Room model is not implemented. See the [bounded execution roadmap](docs/project-management/ICHES_2027_EXECUTION_ROADMAP.md); no automatic coding, merge or push authorization is implied.

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

Historical Phase 02 implementation branch:
- `phase/02-registration-payment`
- Draft phase PR: #61 → `develop`

META-AUDIT work follows the normal scoped-branch → PR → `develop` integration workflow.

Current local work uses `agent/phase04-academic-core` on Laptop 2; the existing uncommitted working tree must be preserved. Use CURRENT_STATE and the operational roadmap for today's gate rather than the historical plan/branches above.

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
