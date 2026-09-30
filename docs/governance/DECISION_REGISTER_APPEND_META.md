# Metadata Decisions — META-001

These decisions supplement the main Product Decision Register and are authoritative for V1.

| ID | Decision | Status |
|---|---|---|
| META-001 | Canonical internal metadata is independent from OJS/Crossref target schemas | ACCEPTED |
| META-002 | One Submission identity persists from abstract through publication | ACCEPTED |
| META-003 | UI locale and scholarly content locale are independent | ACCEPTED |
| META-004 | Primary scholarly locale required; other scholarly translations optional | ACCEPTED |
| META-005 | Keywords are structured individual ordered values | ACCEPTED |
| META-006 | Contributors need not have User accounts | ACCEPTED |
| META-007 | Exactly one corresponding author is required before final submission | ACCEPTED |
| META-008 | Canonical model supports legitimate single-name authors without inventing surname | ACCEPTED |
| META-009 | ORCID is optional; missing ORCID is not a blocker | ACCEPTED |
| META-010 | ORCID verification state distinguishes typed/unverified from authenticated | ACCEPTED |
| META-011 | Contributors may have multiple affiliations | ACCEPTED |
| META-012 | ROR-first with truthful manual fallback | ACCEPTED |
| META-013 | Department/faculty is separate from the ROR organization identity | ACCEPTED |
| META-014 | Raw citation is always preserved; structured reference enrichment is optional/non-destructive | ACCEPTED |
| META-015 | Manuscript files are immutable/versioned; revisions never overwrite prior versions | ACCEPTED |
| META-016 | Finalize for Production creates an immutable publication snapshot | ACCEPTED |
| META-017 | Post-finalization correction creates a superseding snapshot/version with audit | ACCEPTED |
| META-018 | External identifiers use generic scheme/value relationships and never become internal primary keys | ACCEPTED |
| META-019 | OJS adapter is destination-profile/version aware, not one universal XML assumption | ACCEPTED |
| META-020 | Crossref proceedings adapter consumes Edition/proceedings + paper snapshot metadata | ACCEPTED |
| META-021 | General production readiness and Crossref deposit readiness are separate gates | ACCEPTED |
| META-022 | Metadata readiness vocabulary is READY / WARNING / BLOCKED | ACCEPTED |
