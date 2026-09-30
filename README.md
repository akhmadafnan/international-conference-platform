# ICHES Conference & Event Experience Platform

This repository is the canonical pre-development and implementation repository for a reusable multi-edition academic conference platform.

## Current state

**PRE-DEVELOPMENT — META-001 GREEN / STACK + ERD FREEZE CURRENT**

Application coding is not yet authorized.

Current sequence:

PRODUCT DISCOVERY ✓
→ PRODUCT BLUEPRINT v1 ✓
→ CORRECTIVE PHASE 0 RE-BASELINE ✓
→ SUBMISSION & SCHOLARLY METADATA CONTRACT ✓
→ STACK + ERD FREEZE ← CURRENT
→ TWO-WEEK DEVELOPMENT PLAN
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
2. docs/product/PRODUCT_BLUEPRINT_V1.md
3. docs/ai-context/CURRENT_STATE.md
4. docs/governance/CORRECTIVE_PHASE0_REBASELINE.md
5. docs/requirements/v1/
6. docs/metadata/SUBMISSION_SCHOLARLY_METADATA_CONTRACT_V1.md
7. docs/metadata/METADATA_FIELD_DICTIONARY_V1.md
8. docs/metadata/OJS_CROSSREF_ADAPTER_CONTRACT_V1.md
9. docs/metadata/METADATA_READINESS_RULES_V1.md
10. docs/governance/DECISION_REGISTER.md
11. current relevant NFR documents

Historical Phase 0 requirement documents remain valuable provenance but are not current authority where they conflict with the V1 baselines.

## Branch model

- main: stable/release baseline
- develop: integration baseline
- scoped branches: all work

Routine work must not be performed directly on main.
