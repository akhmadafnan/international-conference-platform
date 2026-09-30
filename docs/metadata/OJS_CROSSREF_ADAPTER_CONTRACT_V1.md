# OJS & Crossref Adapter Contract v1

**Status:** AUTHORITATIVE ADAPTER BOUNDARY  
**Updated:** 2026-09-30

## 1. Principle

Adapters transform canonical ICHES metadata into destination-specific payloads.

ICHES CANONICAL METADATA
→ PUBLICATION SNAPSHOT
→ ADAPTER PROFILE
→ OJS / Crossref.

Adapters must not write destination schema assumptions back into canonical fields.

## 2. OJS target

V1 goal: reduce or eliminate manual re-entry of publication metadata in OJS.

Expected handoff content where supported by the target OJS profile:
- submission language;
- title/subtitle;
- abstract;
- keywords;
- contributors in order;
- contributor email where OJS workflow requires it;
- affiliation text;
- ROR identifier where supported/profiled;
- ORCID;
- corresponding-author/contact relationship where supported;
- references;
- final manuscript;
- section/genre mapping supplied by destination profile;
- conference provenance/reference where useful.

## 3. OJS version/profile rule

OJS Native XML/import behavior is version/context specific.

Therefore the application must not promise one universal XML file for every OJS installation.

An OJS destination profile may define:
- OJS major/minor family;
- journal/context identifier;
- section mapping;
- author/contributor user-group mapping if required;
- file genre/component mapping;
- supported locales;
- target import format/version;
- optional endpoint/integration configuration.

V1 may provide manual/assisted export/handoff.

A future API/direct-send integration must use the same canonical publication snapshot, not a second metadata store.

## 4. OJS export integrity

Before export:
- publication snapshot exists;
- final manuscript exists;
- canonical author order is frozen;
- target-required mappings are resolved;
- no private/confidential review data is included;
- no reviewer identity is leaked;
- target payload validates against its adapter profile.

OJS IDs returned after handoff are recorded as external identifiers/references and never become internal primary keys.

## 5. Crossref proceedings target

Edition/proceedings context should be able to provide:
- conference/event name;
- acronym optional;
- theme optional;
- sponsor optional;
- location optional;
- conference dates optional/recommended;
- proceedings title;
- publisher;
- publication date/year;
- series title/ISSN when the proceedings are part of a series;
- ISBN where applicable.

Conference paper publication snapshot should provide:
- contributors in order;
- title;
- DOI when assigned for deposit;
- DOI resource URL;
- publication date when available;
- page range/article number when available;
- references/citation list when available;
- ORCID/affiliation/ROR where supported by the active Crossref schema/profile.

## 6. Crossref readiness levels

General READY_FOR_PRODUCTION does not automatically mean CROSSREF_DEPOSIT_READY.

Crossref deposit readiness additionally requires target/depositor publication facts such as:
- DOI value;
- resource URL;
- required proceedings-level metadata;
- required contributor/title data;
- valid adapter payload.

This distinction allows production work before DOI assignment/deposit.

## 7. Contributor mapping

Canonical contributor order maps to destination contributor sequence.

Canonical ORCID:
- exported only if syntactically valid;
- verification state retained internally;
- adapter must not label an unverified ORCID as authenticated.

Canonical mononym:
- preserved internally;
- adapter may map the full mononym to a required surname field when a target schema requires surname.

## 8. Affiliation mapping

Canonical affiliation stores both human-readable institution snapshot and ROR when known.

Adapter output may use affiliation text plus structured institution/identifier elements where supported.

Missing ROR does not invalidate a truthful manual affiliation.

## 9. References

Canonical raw citation remains source evidence.

Adapters may emit:
- raw/unstructured citation;
- DOI;
- enriched structured citation elements when available.

The absence of structured parsing must not cause data loss.

## 10. Privacy

Crossref/public publication export must not include contributor email merely because ICHES stores it.

OJS export may include email only where required/appropriate for the destination editorial workflow.

Payment, phone, internal notes, private review comments, and audit data are never scholarly export metadata.

## 11. Adapter outputs

Accelerated V1 may support:
- downloadable canonical JSON package for diagnostics/archive;
- OJS-compatible export profile;
- Crossref-ready preview/validation payload;
- human-readable metadata report.

Automatic remote submission is out of accelerated V1 scope.

## 12. Future compatibility

Future adapters may target JATS XML, DataCite, institutional repositories, and other discovery/indexing systems.

They must consume the same canonical metadata/publication snapshot.
