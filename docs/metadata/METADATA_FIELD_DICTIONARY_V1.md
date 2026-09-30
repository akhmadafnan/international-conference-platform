# Metadata Field Dictionary v1

**Status:** AUTHORITATIVE COMPANION TO ICHES-META-001  
**Updated:** 2026-09-30

This dictionary is conceptual. Exact SQL types/table names are deferred to ERD Freeze.

| Domain | Field / concept | Required | Mutability / note |
|---|---|---:|---|
| Submission | internal UUID | Yes | immutable, system-generated |
| Submission | Paper ID | Yes | immutable display identity after assignment |
| Submission | Edition | Yes | protected relationship |
| Submission | Track | Conditional | required when Edition uses tracks |
| Submission | primary scholarly locale | Yes | id/en/ar initially |
| Scholarly text | title | Yes | primary locale required |
| Scholarly text | subtitle | No | localized |
| Scholarly text | abstract | Yes | primary locale required |
| Keyword | value | Yes | individual structured item |
| Keyword | locale | Yes | tied to scholarly locale |
| Keyword | sequence | Yes | explicit order |
| Contributor | public/full name | Yes | preserved canonical name |
| Contributor | given name | Conditional | may be absent for legitimate mononym |
| Contributor | family/surname | Conditional | may be absent canonically for mononym |
| Contributor | single-name flag | Conditional | supports mononyms |
| Contributor | email | Yes V1 | private by default |
| Contributor | country | Yes V1 | ISO country code preferred |
| Contributor | ORCID | No | valid canonical URI if supplied |
| Contributor | ORCID verification state | Conditional | never infer authenticated |
| Contributor | author order | Yes | unique, explicit |
| Contributor | corresponding author | Yes | exactly one before final submission |
| Contributor | linked User | No | authors need not have accounts |
| Affiliation | institution name snapshot | Yes/conditional | unless explicit independent/no affiliation |
| Affiliation | ROR | No | preferred when confidently matched |
| Affiliation | department/faculty | No | free text, separate from ROR |
| Affiliation | country | Yes/conditional | when institution affiliation exists |
| Affiliation | sequence | Yes | supports multiple affiliations |
| Reference | raw citation | Yes when reference exists | never discard |
| Reference | order | Yes | explicit |
| Reference | DOI | No | validated/normalized when present |
| Reference | structured enrichment | No | non-destructive |
| File | file role | Yes | FULL_ARTICLE, REVISED_ARTICLE, etc. |
| File | version | Conditional | manuscript files |
| File | checksum | Yes | integrity |
| File | MIME/size | Yes | security/integrity |
| File | uploader/time | Yes | provenance |
| File | visibility | Yes | private/public policy |
| Presenter | actual presenter contributor | Conditional | required before final scheduling |
| Publication | destination | Yes before production | proceedings or selected journal |
| Publication | final manuscript | Yes | publication gate |
| Publication snapshot | snapshot version | Yes | immutable version |
| Publication snapshot | final title/abstract/keywords | Yes | frozen |
| Publication snapshot | frozen contributors/order | Yes | frozen |
| Publication snapshot | frozen affiliations/ROR | Yes | frozen |
| Publication snapshot | final references | Policy | frozen list |
| Publication snapshot | finalized by/at | Yes | audit |
| External identifier | scheme/value | Conditional | DOI/OJS/etc. |
| External identifier | source/status | Yes when identifier exists | provenance |
| Proceedings | title | Yes for proceedings publication | Edition/publication config |
| Proceedings | publisher | Yes for proceedings publication | Edition/publication config |
| Proceedings | publication date | Yes for proceedings publication | at least year for Crossref deposit |
| Proceedings | ISBN/ISSN | Conditional | based on publication model |
| Proceedings | DOI resource URL | Crossref-specific | required for Crossref DOI deposit |
