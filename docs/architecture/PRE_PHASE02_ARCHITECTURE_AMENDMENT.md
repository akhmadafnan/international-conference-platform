# Pre-Phase 02 Architecture Amendment

**Document ID:** ICHES-ARCH-PRE02-001  
**Status:** ACCEPTED ARCHITECTURE AMENDMENT  
**Updated:** 2026-10-03  
**Applies to:** ERD v1 and implementation planning before Phase 02.

## 1. Purpose

This amendment translates the accepted Pre-Phase 02 Product Rebaseline into bounded architecture changes.

It supersedes only conflicting portions of ERD_V1. All non-conflicting architecture decisions remain frozen.

## 2. Registration/payment semantics

A Registration may exist before payment as an Edition participation intent/package selection.

Registration confirmation is a later finance-backed fact.

Minimum lifecycle semantics:
- pending/intended registration may exist before academic decision;
- accepted author/presenter path creates payment obligation after ACCEPT;
- participant-only PAID path may create payment obligation without an academic submission;
- FREE package or active fee exemption satisfies the fee requirement without creating a Payment row;
- accepted author/presenter FREE path does not create payment obligation after ACCEPT;
- Finance verification changes a PAID registration to confirmed after successful payment;
- FREE/waived registration may confirm without Finance verification once its non-financial prerequisites are satisfied;
- academic acceptance/LoA never depends on Finance authority.

Exact stored enum labels may be finalized during the bounded Registration/Payment implementation as long as these state boundaries are preserved.

## 3. Payment destinations

Add first-class Edition-scoped payment destination configuration.

### payment_destinations

Fields:
- id UUID
- edition_id FK
- code
- label
- bank_name
- account_number
- account_holder
- instructions_i18n JSON nullable
- is_default boolean
- active boolean
- display_order
- timestamps

Constraints:
- unique edition_id + code;
- application invariant permits at most one active default destination per Edition.

### participation_packages amendment

Add:
- billing_mode: FREE / PAID
- payment_destination_id nullable FK payment_destinations

Rules:
- FREE requires price = 0 and does not require a payment destination;
- PAID requires a positive configured price and must resolve an active payment destination through package override or Edition default;
- billing mode is explicit and must not be inferred only from whether the numeric amount happens to be zero.

Null `payment_destination_id` on a PAID package means use the Edition active default destination.

The existing package price/currency remains the default expected fee source.

## 4. Payment snapshot amendment

Existing payments remain explicit Registration-linked business records.

Add/snapshot at payment-obligation creation:
- payment_destination_id nullable FK;
- package_id_snapshot or equivalent immutable package reference/context;
- package_name_snapshot;
- expected_amount DECIMAL;
- currency_code;
- payment_destination_snapshot_json containing the participant-visible bank/destination facts used for the obligation.

Historical payment display and verification read the snapshot, not mutable current payment-destination configuration.

Payment proof versioning remains unchanged.

No Payment row is created for FREE or actively exempted/complimentary registrations.

### registration_fee_exemptions

Provides an explicit exception for a specific Registration whose selected package is normally PAID.

Fields:
- id UUID
- registration_id FK
- reason_code nullable
- reason_text
- granted_by_user_id FK users
- granted_at
- revoked_by_user_id nullable FK users
- revoked_at nullable
- timestamps

Rules:
- at most one active exemption per Registration;
- active exemption means payment is not required for that Registration;
- granting/revoking exemption is a consequential audited action;
- exemption cannot silently rewrite or erase an already verified Payment;
- if a Payment has already been submitted/verified, correction follows the explicit Finance correction/refund policy instead of converting history into a free registration.

## 5. Operational workflow windows

Add bounded Edition operational configuration separate from public Important Dates.

### edition_workflow_windows

Fields:
- id UUID
- edition_id FK
- window_code
- opens_at nullable
- closes_at nullable
- active boolean
- configuration_json nullable
- timestamps

Constraint:
- unique edition_id + window_code.

Initial bounded codes may include:
- REGISTRATION
- ABSTRACT_SUBMISSION
- ACCEPTED_AUTHOR_PAYMENT
- FULL_ARTICLE
- PRESENTER_CONFIRMATION
- REVISION

Public Important Dates may display these deadlines but are not the enforcement source of truth.

## 6. Review traceability confirmation

No generic review-round entity is added for accelerated V1.

The authoritative relationship remains:
submission snapshot/version
→ review assignment
→ review report
→ academic decision.

Each assignment must identify the exact protected snapshot/version being reviewed.

The normal abstract revision creates a new protected snapshot rather than overwriting the old one.

## 7. Lightweight public CMS amendment

Add bounded CMS flexibility without introducing generic EAV/plugin architecture.

### custom_pages

Fields:
- id UUID
- edition_id nullable FK
- slug
- title_i18n JSON
- body_i18n JSON
- status
- published_at nullable
- display_order nullable
- timestamps

Uniqueness is scoped appropriately so stable public URLs cannot collide.

### navigation_items

Fields:
- id UUID
- edition_id nullable FK
- location
- label_i18n JSON
- target_type
- target_value
- parent_id nullable
- display_order
- active
- timestamps

Supported target types are bounded by application code, for example internal named destination, custom page, or approved external URL.

Existing news_posts may serve public announcements/news; no separate announcement table is required unless implementation proves a distinct operational need.

## 8. Publication boundary amendment

Direct DOI generation, Crossref credentials, and Crossref deposit jobs are removed from accelerated V1 architecture responsibility.

The existing external_identifiers infrastructure remains and may store downstream:
- DOI
- OJS_SUBMISSION_ID
- OJS_PUBLICATION_ID
- ARTICLE_NUMBER
- publication URL identifiers where useful.

Publication adapters consume immutable publication snapshots.

The required V1 adapter/handoff target is OJS-oriented export/handoff. Crossref may be a future adapter only if the publishing responsibility later changes.

## 9. Final approved manuscript

submission_files must support identifying the publication-authoritative file after Final ACC.

Implementation may represent this through the existing file_role/status/supersession fields rather than adding a mutable boolean.

Required invariant:
exactly one publication-authoritative Final Approved Manuscript is referenced by the current finalized publication snapshot.

Earlier files remain immutable history.

## 10. Production export batches

Add auditable bulk handoff/export infrastructure.

### publication_export_batches

Fields:
- id UUID
- edition_id FK
- outlet_id nullable FK publication_outlets
- export_type
- status
- stored_file_id nullable FK stored_files
- manifest_json
- checksum_sha256 nullable
- created_by_user_id FK
- created_at
- completed_at nullable

Initial export type:
- OJS_PRODUCTION_BUNDLE

### publication_export_items

Fields:
- id UUID
- batch_id FK publication_export_batches
- publication_record_id FK
- publication_snapshot_id FK
- final_submission_file_id FK
- bundle_path
- checksum_sha256
- status
- timestamps

Constraints:
- one batch item per publication record per batch.

Eligibility guard before inclusion:
- Final ACC exists;
- finalized publication snapshot exists;
- final approved manuscript exists;
- publication destination exists;
- readiness is not BLOCKED.

The bundle contains publication-facing artifacts only. Reviewer-confidential comments and internal review/audit material are excluded.

## 11. Bundle contents

The generated ZIP may contain one directory per paper plus a batch manifest.

A paper production directory may contain:
- final approved manuscript;
- approved supplementary publication files;
- canonical/OJS-oriented metadata export;
- machine-readable manifest.

The batch manifest must identify at least:
- Paper ID;
- publication snapshot;
- file identity/checksum;
- publication destination;
- readiness state;
- export timestamp.

## 12. Existing architecture retained

Unchanged:
- Laravel 13 modular monolith;
- UUIDv7 first-class identities;
- explicit relational core;
- immutable/versioned historical truth;
- private-by-default files;
- server-side authorization;
- edition-scoped roles/permissions;
- independent academic/finance/publication authorities;
- publication snapshot;
- OJS downstream boundary;
- READY/WARNING/BLOCKED readiness;
- no microservices requirement.

## 13. Gate

This amendment is sufficient to unblock detailed Phase 02 planning only after:
- canonical context references it above conflicting older contracts;
- Decision Register is reconciled;
- Current State records Pre-Phase 02 rebaseline closure;
- repository diff is governance/docs-only and internally consistent.

It does not itself authorize Phase 02 implementation.
