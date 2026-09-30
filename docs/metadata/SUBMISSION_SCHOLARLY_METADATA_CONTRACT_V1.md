# Submission & Scholarly Metadata Contract v1

**Document ID:** ICHES-META-001  
**Status:** AUTHORITATIVE V1 CONTRACT  
**Updated:** 2026-09-30  
**Gate:** META-001 — GREEN

## 1. Purpose

This contract defines the canonical scholarly metadata model from abstract submission through publication handoff.

AUTHOR INPUT
→ CANONICAL SCHOLARLY METADATA
→ LIFECYCLE ENRICHMENT
→ FINAL PUBLICATION SNAPSHOT
→ TARGET ADAPTERS: OJS / Crossref / future JATS and other targets.

The internal model must never become a copy of an OJS XML schema or Crossref deposit schema.

## 2. Core principles

1. One Submission/Paper identity persists from abstract through publication.
2. Abstract, Full Article, Revised Article, and publication file are versions/artifacts of the same Submission.
3. UI locale is independent from scholarly content locale.
4. Primary scholarly locale is required; additional translations are optional.
5. Contributor records do not require application accounts.
6. ORCID is optional.
7. ROR is preferred for organizations but manual affiliation remains valid.
8. Raw references are preserved even when structured enrichment is available.
9. Files are versioned; manuscript files are never silently overwritten.
10. External identifiers are references, never internal primary keys.
11. Finalize for Production creates an immutable scholarly metadata snapshot.
12. Later profile edits must not rewrite historical publication metadata.
13. Adapter-specific transformations must not mutate canonical metadata.
14. Metadata readiness uses READY / WARNING / BLOCKED.
15. Personal data is exported only when the target workflow actually requires it.

## 3. Submission identity

Each Submission has:
- immutable internal UUID;
- human-readable Paper ID/code unique within an Edition;
- Conference Edition;
- Track when the Edition uses tracks;
- primary scholarly locale;
- workflow status independent from metadata status;
- created/submitted timestamps;
- owner/corresponding workflow relationship.

Example display ID: ICHES27-P-0042.

The display code is not the database primary key.

## 4. Scholarly text

Canonical translatable scholarly fields:
- Title — required in primary locale;
- Subtitle — optional;
- Abstract — required in primary locale;
- Keywords — structured list in primary locale;
- additional title/subtitle/abstract/keywords translations — optional.

Supported initial locales:
- id
- en
- ar

Do not require all three translations merely because the application UI supports three languages.

Additional translations identify their own locale and never overwrite the primary-language value.

## 5. Keywords

Keywords are individual ordered values, not a single comma-separated database string.

Conceptual fields:
- locale;
- value;
- sequence/order.

Minimum/maximum keyword count is Edition policy, not hardcoded globally.

## 6. Contributors

A contributor is scholarly metadata and may exist without a User account.

Canonical contributor fields:
- contributor UUID;
- full/public display name;
- given name;
- family/surname, nullable for legitimate single-name authors;
- single-name indicator where applicable;
- email;
- country code;
- optional ORCID;
- ORCID verification state;
- corresponding-author flag;
- author sequence/order;
- contributor type/role, V1 default author;
- optional linked User account.

V1 rules:
- contributor order is explicit and unique;
- exactly one corresponding author is required before final submission;
- contributor email is private by default;
- actual presenter is a lifecycle relationship to one contributor, not a contributor-name field;
- author identity changes after protected stages require controlled correction.

## 7. Single-name authors

The canonical model preserves the author's true single name without inventing a false family name.

Adapters may transform a single name only when a destination schema requires a surname-like element.

Example:
- canonical: full_name = "Sukarno", family_name = null, single_name = true;
- Crossref adapter: map Sukarno to the required surname field and omit given name.

The adapter transformation must not rewrite canonical metadata.

## 8. ORCID

ORCID is optional.

If present:
- normalize to canonical ORCID URI form;
- validate syntax/checksum;
- store whether it is self-entered/unverified or authenticated/verified;
- never claim authentication when only typed manually.

Missing ORCID is a WARNING/recommendation, not a publication blocker.

Invalid ORCID supplied by the user is a validation error until corrected or removed.

## 9. Affiliations

A contributor may have multiple affiliations.

Canonical affiliation relationship contains:
- contributor;
- affiliation sequence;
- institution reference when matched internally;
- institution-name snapshot;
- optional ROR ID;
- optional department/faculty/subdivision text;
- country code;
- optional city/location text where operationally useful.

ROR identifies the organization; department/faculty remains separate local text.

ROR is preferred but not mandatory.

A contributor must either:
- have at least one affiliation; or
- explicitly be marked as independent/no institutional affiliation when policy allows.

Manual affiliation remains valid when the organization is not in ROR, ROR is unavailable, or a confident match cannot be made.

## 10. Institution canonical record

Reusable institution data may contain:
- internal UUID;
- preferred name;
- ROR URI;
- country code;
- website optional;
- aliases optional;
- source/provenance.

Do not treat department/faculty as a ROR organization unless it actually has its own valid ROR record.

## 11. References

References are ordered scholarly metadata.

Author-facing V1 input remains simple:
- raw citation — primary field;
- DOI — optional;
- URL — optional.

Optional structured enrichment may later add:
- title;
- authors;
- source/container title;
- year;
- volume;
- issue;
- first page;
- last page;
- article number;
- publisher.

Rules:
- always preserve the original raw citation;
- structured enrichment must not destroy raw text;
- reference order is explicit;
- reference DOI is validated/normalized when supplied;
- absence of structured parsing is not itself a blocker.

Reference requirements may differ between abstract submission and publication readiness.

## 12. Submission files and versions

Conceptual file roles:
- ABSTRACT_FILE — if Edition requires it;
- FULL_ARTICLE;
- REVISED_ARTICLE;
- PRESENTATION_SLIDES — optional/configurable;
- PUBLICATION_FINAL_FILE;
- supplementary file — future/optional.

Each stored file/version preserves:
- immutable file UUID;
- Submission;
- file role;
- manuscript version number where relevant;
- original filename;
- stored filename/path;
- MIME type;
- size;
- checksum;
- uploader;
- uploaded timestamp;
- visibility/access classification;
- supersession relationship where applicable.

Rules:
- Full Article v1 is never overwritten by Revised Article v2;
- replacement/correction creates traceable history;
- unpublished manuscripts remain private;
- public publication files are separate from private working files.

## 13. Metadata snapshots

There are two distinct concepts.

### 13.1 Submission/version evidence
The system preserves what was officially submitted/revised at protected lifecycle points.

### 13.2 Publication snapshot
Finalize for Production creates an immutable scholarly publication snapshot containing at least:
- primary locale;
- final title/subtitle;
- final abstract;
- final keywords;
- final contributor order;
- contributor public names;
- corresponding-author designation;
- ORCID values/status as applicable;
- frozen affiliation names;
- frozen ROR IDs where available;
- references and their order;
- final manuscript/file reference;
- publication destination;
- relevant Edition/proceedings context;
- snapshot version;
- finalized by;
- finalized at.

Later User/Profile/Institution edits do not mutate this snapshot.

If a substantive correction is authorized after finalization:
- preserve the prior snapshot;
- create a superseding snapshot/version;
- record reason, authority, and timestamp.

## 14. Publication destination metadata

### Proceedings
Publication record may contain:
- proceedings title;
- publisher;
- publication date;
- ISBN and/or ISSN when applicable;
- volume/series data when applicable;
- DOI;
- page range or article number;
- landing URL;
- final PDF;
- publication date/status.

### Selected Journal
Handoff record may contain:
- target journal;
- OJS/journal endpoint profile;
- external submission/reference ID;
- handoff date;
- known downstream status;
- downstream URL when known.

SELECTED_FOR_JOURNAL is not ACCEPTED_BY_JOURNAL.

## 15. External identifiers

Use a generic identifier concept rather than dedicated OJS/Crossref-specific columns throughout the model.

Conceptual fields:
- entity scope/type;
- entity UUID;
- scheme;
- value;
- URI where applicable;
- source;
- verified/known status;
- assigned/recorded timestamp.

Candidate schemes:
- DOI;
- OJS_SUBMISSION_ID;
- OJS_PUBLICATION_ID;
- ARTICLE_NUMBER;
- URL;
- ORCID;
- ROR.

Edition/proceedings identifiers such as ISBN/ISSN belong to the appropriate Edition/publication context, not copied onto every paper unnecessarily.

## 16. Metadata provenance

Metadata may originate from:
- AUTHOR_INPUT;
- SYSTEM_DERIVED;
- ADMIN_CORRECTION;
- EXTERNAL_ENRICHMENT;
- PUBLICATION_FINALIZATION;
- DOWNSTREAM_REFERENCE.

Consequential corrections remain auditable.

Exact provenance storage strategy is an ERD decision; the contract requires the behavior, not one table design.

## 17. Lifecycle mutability

### Draft
Author may edit submission metadata freely within policy.

### Submitted / under review
Metadata is controlled. Administrative correction/revision creates traceable change history.

### Accepted
LoA uses the accepted scholarly identity. Full Article becomes available.

### Full Article / scheduling
Contributor and presenter changes are controlled because they affect scheduling, certificates, and publication.

### Final ACC
Publication metadata is ready for author/admin final confirmation if configured.

### Finalize for Production
Publication snapshot becomes immutable except via controlled superseding correction.

## 18. Author input vs system-derived data

Author normally inputs:
- scholarly language;
- title/subtitle;
- abstract;
- keywords;
- contributors;
- affiliations;
- ORCID where available;
- references;
- manuscript files.

System/admin derives or assigns:
- internal UUID;
- Paper ID;
- Edition metadata;
- review/scheduling states;
- actual presenter relationship;
- LoA/document numbers;
- presentation facts;
- publication destination;
- DOI/external identifiers when assigned;
- publication snapshot timestamps;
- certificate/award data.

Do not ask authors to repeatedly type conference/edition/publisher metadata already known by the system.

## 19. Public/private boundaries

Normally public after publication:
- paper title;
- abstract/keywords when publication policy permits;
- author public names;
- public affiliations;
- ORCID when legitimately published;
- DOI/publication URL;
- proceedings/journal bibliographic data.

Private by default:
- contributor email;
- phone/account data;
- payment data;
- reviewer identity where anonymous;
- review confidential notes;
- private manuscript working versions;
- internal audit/provenance notes.

## 20. Gate result

META-001 is GREEN when:
- canonical fields are independent of OJS/Crossref schemas;
- contributor/name/affiliation edge cases are defined;
- ORCID/ROR semantics are defined;
- references preserve raw citations;
- file/version behavior is explicit;
- publication snapshot is immutable/versioned;
- adapter boundaries are defined;
- readiness rules exist.

This contract meets those conditions.
