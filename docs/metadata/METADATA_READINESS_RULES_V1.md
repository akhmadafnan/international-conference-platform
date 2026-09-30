# Metadata Readiness Rules v1

**Status:** AUTHORITATIVE V1 READINESS CONTRACT  
**Updated:** 2026-09-30

## 1. Readiness vocabulary

Only three top-level outcomes:
- READY
- WARNING
- BLOCKED

Do not use an arbitrary numeric metadata quality score.

## 2. Submission readiness

Before official abstract submission, BLOCKED when:
- primary scholarly locale missing;
- required title missing;
- required abstract missing;
- keyword policy not satisfied;
- Track missing when Edition requires it;
- no contributor;
- contributor order invalid;
- corresponding author is not exactly one;
- required contributor email missing/invalid;
- supplied ORCID is invalid;
- required affiliation/explicit independent status missing;
- required abstract file missing when Edition policy requires it.

WARNING examples:
- ORCID absent;
- ROR absent but affiliation is valid manual text;
- optional translations absent;
- reference DOI absent.

## 3. Scheduling readiness

BLOCKED when:
- abstract not accepted;
- Full Article missing;
- actual presenter not confirmed;
- presenter is not one of the Submission contributors.

Other scheduling conflicts may be WARNING when override is permitted.

## 4. Final academic/publication readiness

Before Finalize for Production, BLOCKED when:
- PRESENTED requirement not satisfied without authorized exception;
- required assessment incomplete;
- required revision incomplete;
- Final ACC missing;
- final manuscript missing;
- publication destination missing;
- final title missing;
- contributor order invalid;
- corresponding author invalid;
- required affiliation state invalid;
- supplied ORCID invalid;
- required references missing according to Edition/destination policy;
- unresolved protected authorship correction exists.

WARNING examples:
- contributor ORCID absent;
- ORCID is valid but not authenticated;
- affiliation has no ROR;
- references are only raw/unstructured;
- reference DOI absent;
- optional scholarly translation absent;
- page range/article number not yet assigned.

## 5. Publication snapshot gate

Publication snapshot may be finalized only when general publication readiness is READY or when explicitly allowed WARNING conditions remain.

BLOCKED conditions cannot be overridden silently.

If an authorized domain-specific override exists, record original blocker, authority, reason, time, and resulting state.

## 6. OJS handoff readiness

BLOCKED when:
- publication snapshot missing;
- final manuscript missing;
- target OJS profile missing;
- required section/genre/context mapping unresolved;
- required target locale unsupported;
- adapter payload fails target-profile validation.

WARNING:
- ROR unsupported by target profile;
- some structured reference enrichment unavailable.

## 7. Crossref deposit readiness

BLOCKED when:
- publication snapshot missing;
- contributor/title data required for the conference paper missing;
- DOI missing;
- DOI resource URL missing;
- proceedings title missing;
- publisher missing;
- publication year/date missing;
- required series metadata missing when using a proceedings-series deposit;
- adapter/schema validation fails.

WARNING:
- ORCID absent;
- affiliation/ROR incomplete;
- citation list absent;
- event optional/recommended metadata incomplete;
- page range/article number absent where not required.

## 8. UI behavior

User-facing wording explains the actual issue.

Do not display:
METADATA_BLOCKER_004

Prefer:
Publication is blocked because the final manuscript has not been uploaded.

Admin screens may show machine codes only as secondary detail.
