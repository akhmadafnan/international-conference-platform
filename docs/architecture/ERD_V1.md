# ERD v1 — ICHES Accelerated V1

**Document ID:** ICHES-ARCH-ERD-001
**Status:** FROZEN RELATIONAL CONTRACT FOR V1
**Updated:** 2026-09-30

> **Pre-Phase 02 architecture notice (2026-10-03):** `docs/architecture/PRE_PHASE02_ARCHITECTURE_AMENDMENT.md` supersedes only the conflicting Registration/Payment, bounded CMS, publication-boundary, and production-export portions of this ERD. All non-conflicting relationships and invariants remain frozen.

This document freezes the V1 relational model at architecture level.

It is not a generated migration file. Column lengths, indexes, and framework-specific migration syntax will be implemented from this contract.

## 1. Modeling principles

- MySQL 9.7 relational core.
- UUIDv7 CHAR(36) primary keys for first-class domain entities.
- Human business/document codes remain separate.
- Core relationships use explicit foreign keys.
- JSON is used for snapshots/evidence/configuration, not as a replacement for core relations.
- Historical facts use status/version/supersession, not silent overwrite.
- Multi-edition scope is explicit.
- Contributor scholarly identity is submission-scoped, not forced to be a User.
- Institution/ROR is organization-first; faculty/program is subdivision text only.
- One Submission identity persists through the full academic/publication lifecycle.

## 2. Core relationship overview

~~~mermaid
erDiagram
    USERS ||--o{ EDITION_MEMBERSHIPS : joins
    CONFERENCE_SERIES ||--o{ CONFERENCE_EDITIONS : has
    CONFERENCE_EDITIONS ||--o{ EDITION_MEMBERSHIPS : scopes

    CONFERENCE_EDITIONS ||--o{ PARTICIPATION_PACKAGES : offers
    CONFERENCE_EDITIONS ||--o{ ACTIVITIES : defines
    PARTICIPATION_PACKAGES ||--o{ PACKAGE_ACTIVITY_ENTITLEMENTS : contains
    ACTIVITIES ||--o{ PACKAGE_ACTIVITY_ENTITLEMENTS : included

    EDITION_MEMBERSHIPS ||--o| REGISTRATIONS : participant
    REGISTRATIONS ||--o{ REGISTRATION_ACTIVITIES : entitled
    ACTIVITIES ||--o{ REGISTRATION_ACTIVITIES : activity
    REGISTRATIONS ||--o{ PAYMENTS : pays
    PAYMENTS ||--o{ PAYMENT_PROOFS : evidence
    STORED_FILES ||--o{ PAYMENT_PROOFS : file

    REGISTRATIONS ||--o{ SUBMISSIONS : creates
    CONFERENCE_EDITIONS ||--o{ SUBMISSIONS : contains
    TRACKS ||--o{ SUBMISSIONS : classifies

    SUBMISSIONS ||--o{ SUBMISSION_TRANSLATIONS : texts
    SUBMISSIONS ||--o{ SUBMISSION_KEYWORDS : keywords
    SUBMISSIONS ||--o{ SUBMISSION_CONTRIBUTORS : authors
    SUBMISSION_CONTRIBUTORS ||--o{ CONTRIBUTOR_AFFILIATIONS : affiliations
    INSTITUTIONS ||--o{ CONTRIBUTOR_AFFILIATIONS : matched
    SUBMISSIONS ||--o{ SUBMISSION_REFERENCES : references
    SUBMISSIONS ||--o{ SUBMISSION_FILES : files
    STORED_FILES ||--o{ SUBMISSION_FILES : binary
    SUBMISSIONS ||--o{ SUBMISSION_SNAPSHOTS : evidence

    SUBMISSIONS ||--o{ REVIEW_ASSIGNMENTS : reviewed
    REVIEW_ASSIGNMENTS ||--o| REVIEW_REPORTS : report
    SUBMISSIONS ||--o{ ACADEMIC_DECISIONS : decisions

    CONFERENCE_EDITIONS ||--o{ ROOMS : rooms
    CONFERENCE_EDITIONS ||--o{ SESSIONS : sessions
    ROOMS ||--o{ SESSIONS : hosts
    SESSIONS ||--o{ PRESENTATION_SLOTS : slots
    SUBMISSIONS ||--o| PRESENTATION_SLOTS : scheduled
    SUBMISSION_CONTRIBUTORS ||--o{ PRESENTATION_SLOTS : presenter
    PRESENTATION_SLOTS ||--o{ PRESENTATION_REVIEWER_ASSIGNMENTS : reviewer
    PRESENTATION_REVIEWER_ASSIGNMENTS ||--o| PRESENTATION_ASSESSMENTS : assesses

    REGISTRATION_ACTIVITIES ||--o| ACTIVITY_ATTENDANCES : attendance

    SUBMISSIONS ||--o| PUBLICATION_RECORDS : publication
    PUBLICATION_OUTLETS ||--o{ PUBLICATION_RECORDS : destination
    PUBLICATION_RECORDS ||--o{ PUBLICATION_SNAPSHOTS : snapshots
    PUBLICATION_RECORDS ||--o{ PUBLICATION_HANDOFFS : handoffs

    CONFERENCE_EDITIONS ||--o{ AWARD_PROGRAMS : awards
    AWARD_PROGRAMS ||--o{ AWARD_CANDIDATES : candidates
    AWARD_PROGRAMS ||--o| AWARD_FINALIZATIONS : finalizes
    AWARD_FINALIZATIONS ||--o{ AWARD_RECIPIENTS : recipients

    CONFERENCE_EDITIONS ||--o{ CERTIFICATE_TEMPLATES : templates
    CERTIFICATE_TEMPLATES ||--o{ CERTIFICATES : renders
    STORED_FILES ||--o{ CERTIFICATES : pdf

    CONFERENCE_EDITIONS ||--o{ GENERATED_DOCUMENTS : documents
    STORED_FILES ||--o{ GENERATED_DOCUMENTS : file
~~~

## 3. Identity & access

### users
Authentication identity only.

Core fields:
- id UUID
- email unique
- password
- email_verified_at
- preferred_locale
- two_factor fields per Starter Kit/Fortify
- status
- timestamps

Do not store historical scholarly author metadata as authoritative publication truth here.

### user_profiles
Current mutable person profile.

Fields:
- id UUID
- user_id unique FK
- display_name
- phone nullable
- country_code nullable
- primary_institution_id nullable FK institutions
- subdivision_text nullable
- orcid_uri nullable
- timezone nullable
- timestamps

### edition_memberships
Any User relationship to an Edition.

Fields:
- id UUID
- edition_id FK
- user_id FK
- membership_status
- joined_at
- ended_at nullable
- timestamps

Constraint:
- unique edition_id + user_id.

Participant registration is a specialized optional record linked to a membership.

### authorization package tables
Spatie Permission tables are package-managed.

V1 requirement:
- edition-scoped business-role assignment;
- UUID-compatible morph keys;
- global superadmin authority path with application-wide abilities across editions;
- roles/permissions never replace Policies/Gates.

## 4. Conference model

### conference_series
Fields:
- id UUID
- code unique
- name
- acronym nullable
- about_i18n JSON
- logo_file_id nullable
- status
- timestamps

### conference_editions
Fields:
- id UUID
- series_id FK
- edition_code unique
- edition_number nullable
- year
- theme_i18n JSON
- host_name
- organizer_i18n JSON nullable
- mode: OFFLINE / ONLINE / HYBRID
- timezone IANA
- starts_at
- ends_at
- registration_opens_at nullable
- registration_closes_at nullable
- schedule_published_at nullable
- lifecycle_status
- settings_json
- timestamps

### venues
Fields:
- id UUID
- edition_id FK
- name
- address
- city
- country_code
- latitude/longitude nullable
- map_url nullable
- details_i18n JSON nullable
- timestamps

### activities
Edition-level activities such as Conference, Evening/MoU, Community Service.

Fields:
- id UUID
- edition_id FK
- code
- name_i18n JSON
- description_i18n JSON nullable
- starts_at nullable
- ends_at nullable
- venue_id nullable
- activity_type
- attendance_required boolean
- active boolean
- timestamps

Constraint:
- unique edition_id + code.

### participation_packages
Fields:
- id UUID
- edition_id FK
- code
- name_i18n JSON
- description_i18n JSON nullable
- price DECIMAL
- currency_code
- active
- display_order
- timestamps

### package_activity_entitlements
Fields:
- package_id FK
- activity_id FK
- entitlement_type
- notes nullable

Constraint:
- unique package_id + activity_id.

### important_dates
Fields:
- id UUID
- edition_id FK
- date_key/code
- label_i18n JSON
- starts_at/date value
- ends_at nullable
- public boolean
- display_order
- timestamps

### tracks
Fields:
- id UUID
- edition_id FK
- code
- name_i18n JSON
- description_i18n JSON nullable
- active
- timestamps

### speakers
Fields:
- id UUID
- edition_id FK
- name
- title nullable
- institution_name nullable
- country_code nullable
- speaker_type
- bio_i18n JSON nullable
- topic_i18n JSON nullable
- photo_file_id nullable
- display_order
- published_at nullable
- timestamps

### partners
Fields:
- id UUID
- edition_id FK
- name
- partner_type
- logo_file_id nullable
- url nullable
- display_order
- timestamps

## 5. Registration & finance

### registrations
One participant registration per User per Edition.

Fields:
- id UUID
- membership_id unique FK
- registration_code
- package_id FK
- package_name_snapshot
- participant_category nullable
- fee_amount DECIMAL
- currency_code
- status
- confirmed_at nullable
- cancelled_at nullable
- timestamps

Constraints:
- unique registration_code
- unique membership_id.

### registration_activities
Snapshot of actual activity entitlement for the registration.

Fields:
- id UUID
- registration_id FK
- activity_id FK
- entitlement_source
- status
- assigned_notes nullable
- timestamps

Constraint:
- unique registration_id + activity_id.

### payments
Allows history/retry while keeping one current successful payment fact.

Fields:
- id UUID
- registration_id FK
- expected_amount DECIMAL
- currency_code
- submitted_amount nullable DECIMAL
- sender_name nullable
- transfer_date nullable
- status
- submitted_at nullable
- verified_by_user_id nullable
- verified_at nullable
- correction_reason nullable
- cancelled_at nullable
- timestamps

### payment_proofs
Versioned proof evidence.

Fields:
- id UUID
- payment_id FK
- stored_file_id FK
- version integer
- submitted_by_user_id FK
- submitted_at
- superseded_at nullable

Constraint:
- unique payment_id + version.

### refunds
Exceptional/manual only.

Fields:
- id UUID
- payment_id FK
- reason_code
- reason_text
- amount DECIMAL
- currency_code
- status
- processed_by_user_id nullable
- processed_at nullable
- proof_file_id nullable
- restricted_destination_json nullable
- timestamps

Academic rejection does not automatically create this record.

## 6. Shared file storage

### stored_files
Immutable binary/file metadata.

Fields:
- id UUID
- disk
- path unique
- original_name
- mime_type
- size_bytes
- checksum_sha256
- visibility_class
- uploaded_by_user_id nullable
- created_at

Binary replacement creates a new stored_files record.

No business meaning is encoded only in the path.

## 7. Scholarly submission

### submissions
One persistent Paper identity.

Fields:
- id UUID
- edition_id FK
- registration_id FK
- paper_code
- track_id nullable FK
- primary_locale
- academic_status
- current_abstract_version integer
- actual_presenter_contributor_id nullable
- submitted_at nullable
- accepted_at nullable
- rejected_at nullable
- final_academic_approved_at nullable
- timestamps

Constraints:
- unique edition_id + paper_code.

### submission_translations
Includes primary and optional scholarly translations.

Fields:
- id UUID
- submission_id FK
- locale
- title
- subtitle nullable
- abstract_text
- timestamps

Constraint:
- unique submission_id + locale.

### submission_keywords
Fields:
- id UUID
- submission_id FK
- locale
- value
- sequence
- timestamps

Constraints:
- unique submission_id + locale + sequence.

### submission_contributors
Submission-scoped canonical author metadata.

Fields:
- id UUID
- submission_id FK
- linked_user_id nullable FK
- display_name
- given_name nullable
- family_name nullable
- single_name boolean
- email
- country_code
- orcid_uri nullable
- orcid_verification_state nullable
- is_corresponding boolean
- sequence
- contributor_role default AUTHOR
- timestamps

Constraints:
- unique submission_id + sequence.

Rule:
exactly one corresponding contributor before official submission.

### institutions
Reusable organization identity.

Fields:
- id UUID
- preferred_name
- ror_uri nullable unique
- country_code nullable
- website nullable
- aliases_json nullable
- source
- timestamps

Institution is organization-level.
Faculty/department/study-program is not the primary institution identity.

### contributor_affiliations
Historical affiliation snapshot.

Fields:
- id UUID
- submission_contributor_id FK
- institution_id nullable FK
- institution_name_snapshot
- ror_uri_snapshot nullable
- subdivision_text nullable
- country_code nullable
- city_text nullable
- sequence
- timestamps

Constraint:
- unique submission_contributor_id + sequence.

### submission_references
Fields:
- id UUID
- submission_id FK
- sequence
- raw_citation
- doi nullable
- url nullable
- structured_json nullable
- timestamps

Constraint:
- unique submission_id + sequence.

### submission_files
Business meaning for immutable stored file.

Fields:
- id UUID
- submission_id FK
- stored_file_id FK
- file_role
- manuscript_version nullable integer
- uploaded_by_user_id FK
- submitted_at
- supersedes_submission_file_id nullable
- status
- timestamps

### submission_snapshots
Official abstract/submission evidence.

Fields:
- id UUID
- submission_id FK
- snapshot_type: INITIAL_SUBMISSION / ABSTRACT_REVISION / OTHER_CONTROLLED
- version integer
- metadata_json
- checksum
- created_by_user_id
- created_at

Constraint:
- unique submission_id + snapshot_type + version.

## 8. Administrative screening & abstract review

### administrative_screenings
Fields:
- id UUID
- submission_id FK
- submission_snapshot_id FK
- screener_user_id FK
- outcome: PASS / CORRECTION_REQUIRED / INELIGIBLE
- checklist_json
- notes nullable
- decided_at
- timestamps

History is preserved; do not overwrite prior screening result.

### review_assignments
Fields:
- id UUID
- submission_id FK
- submission_snapshot_id FK
- reviewer_user_id FK
- anonymity_mode default SINGLE_ANONYMOUS
- status
- assigned_at
- due_at nullable
- completed_at nullable
- conflict_status
- conflict_note nullable
- cancelled_at nullable
- timestamps

Self-review is blocked by policy.

### review_reports
One submitted report per assignment.

Fields:
- id UUID
- review_assignment_id unique FK
- recommendation: ACCEPT / REVISION / REJECT
- author_comments nullable
- confidential_comments nullable
- rubric_json nullable
- submitted_at
- reopened_at nullable
- timestamps

### academic_decisions
Immutable decision history.

Fields:
- id UUID
- submission_id FK
- stage: ABSTRACT / FINAL_ARTICLE
- decision
- related_snapshot_id nullable
- decided_by_user_id FK
- rationale nullable
- decided_at
- supersedes_decision_id nullable
- timestamps

Reviewer recommendation does not automatically create this decision.

## 9. Scheduling & event operations

### rooms
Fields:
- id UUID
- edition_id FK
- venue_id nullable FK
- code
- name
- capacity nullable
- online_url nullable
- timestamps

Constraint:
- unique edition_id + code.

### sessions
Fields:
- id UUID
- edition_id FK
- room_id FK
- track_id nullable
- title_i18n JSON
- starts_at
- ends_at
- moderator_user_id nullable
- status
- timestamps

### presentation_slots
One scheduled presentation for a Submission.

Fields:
- id UUID
- session_id FK
- submission_id unique FK
- presenter_contributor_id FK
- starts_at
- ends_at
- sequence
- presentation_status: SCHEDULED / PRESENTED / NO_SHOW
- verified_by_user_id nullable
- verified_at nullable
- exception_json nullable
- timestamps

### presentation_reviewer_assignments
Assignments are slot-specific; session-level bulk assignment expands into slot records.

Fields:
- id UUID
- presentation_slot_id FK
- reviewer_user_id FK
- status
- assigned_at
- completed_at nullable
- timestamps

Constraint:
- unique presentation_slot_id + reviewer_user_id.

### presentation_assessments
Fields:
- id UUID
- presentation_reviewer_assignment_id unique FK
- article_scores_json nullable
- presenter_scores_json nullable
- recommendation
- revision_notes nullable
- internal_notes nullable
- submitted_at
- timestamps

### activity_attendances
Fields:
- id UUID
- registration_activity_id unique FK
- attendance_status
- checked_in_at nullable
- checked_out_at nullable
- verified_by_user_id nullable
- method: QR / MANUAL / IMPORT
- notes nullable
- timestamps

QR lookup alone does not create this record without the authorized check-in action.

## 10. Community Service lightweight model

### community_service_groups
Fields:
- id UUID
- edition_id FK
- activity_id FK
- category
- name
- location_text nullable
- coordinator_user_id nullable
- notes nullable
- timestamps

### community_service_memberships
Fields:
- id UUID
- group_id FK
- registration_id FK
- assigned_by_user_id FK
- assigned_at
- timestamps

Constraint:
- unique group_id + registration_id;
- application prevents one active group assignment per relevant registration unless explicitly changed.

No mini-KKN project management tables are introduced.

## 11. Publication

### publication_outlets
Edition-level destination configuration.

Fields:
- id UUID
- edition_id FK
- outlet_type: PROCEEDINGS / JOURNAL
- name
- publisher nullable
- issn nullable
- isbn nullable
- series_title nullable
- landing_url nullable
- ojs_base_url nullable
- adapter_profile_json nullable
- is_default boolean
- active boolean
- timestamps

Only one default active outlet per Edition is permitted by application invariant.

### publication_records
At most one active publication path per Submission in V1.

Fields:
- id UUID
- submission_id unique FK
- outlet_id FK
- status
- readiness_state
- final_submission_file_id nullable
- selected_at
- selected_by_user_id
- production_started_at nullable
- published_at nullable
- timestamps

### publication_snapshots
Immutable canonical publication snapshot.

Fields:
- id UUID
- publication_record_id FK
- version
- metadata_json
- checksum
- final_submission_file_id FK
- finalized_by_user_id FK
- finalized_at
- supersedes_snapshot_id nullable
- timestamps

Constraint:
- unique publication_record_id + version.

### publication_handoffs
Fields:
- id UUID
- publication_record_id FK
- target_type: OJS / OTHER
- target_name
- status
- payload_file_id nullable
- external_reference nullable
- external_url nullable
- handed_off_by_user_id
- handed_off_at
- response_json nullable
- timestamps

### external_identifiers
Infrastructure-edge generic identifier table.

Fields:
- id UUID
- subject_type
- subject_id UUID
- scheme
- namespace nullable
- value
- uri nullable
- source
- status
- recorded_at
- timestamps

Examples:
- DOI
- OJS_SUBMISSION_ID
- OJS_PUBLICATION_ID
- ARTICLE_NUMBER

Index:
- subject_type + subject_id
- scheme + namespace + value.

ORCID/ROR remain first-class scholarly identity fields in their own canonical records; this generic table is primarily for downstream/publication identifiers.

## 12. Awards

### award_programs
Fields:
- id UUID
- edition_id FK
- code: BEST_ARTICLE / BEST_PRESENTER / custom future
- name_i18n JSON
- scope_type
- status
- timestamps

### award_candidates
Evidence only.

Fields:
- id UUID
- award_program_id FK
- submission_id nullable
- contributor_id nullable
- presentation_slot_id nullable
- evidence_json
- candidate_state
- created_at

A candidate is not a winner.

### award_finalizations
Committee final decision event.

Fields:
- id UUID
- award_program_id unique FK
- finalized_by_user_id FK
- decision_note nullable
- finalized_at
- reopened_at nullable
- reopen_reason nullable
- timestamps

### award_recipients
Final selected winner targets.

Fields:
- id UUID
- award_finalization_id FK
- submission_id nullable
- contributor_id nullable
- user_id nullable
- recipient_name_snapshot
- recipient_context_json nullable
- timestamps

Supports:
- Best Article paper with certificate recipients derived from all final authors;
- Best Presenter actual presenter;
- multiple/no winner when policy later allows it.

## 13. Certificates & generated documents

### certificate_templates
Fields:
- id UUID
- edition_id FK
- certificate_type
- name
- template_html/view_key
- configuration_json
- active
- timestamps

### certificates
One individual credential record.

Fields:
- id UUID
- edition_id FK
- certificate_template_id FK
- certificate_type
- status: ELIGIBLE / GENERATED / ISSUED / REVOKED / SUPERSEDED
- recipient_user_id nullable
- recipient_contributor_id nullable
- recipient_name_snapshot
- recipient_context_json
- certificate_number nullable
- generated_file_id nullable
- generated_at nullable
- issued_at nullable
- revoked_at nullable
- revoke_reason nullable
- supersedes_certificate_id nullable
- created_by_user_id nullable
- timestamps

Rules:
- number/token becomes immutable when issued;
- issued snapshot never follows later profile edits.

### generated_documents
Non-certificate official documents.

Document types include:
- EVENT_PASS
- PRESENTATION_LOA
- PROCEEDINGS_PUBLICATION_ACCEPTANCE
- JOURNAL_SELECTION_HANDOFF_NOTICE

Fields:
- id UUID
- edition_id FK
- document_type
- registration_id nullable
- submission_id nullable
- recipient_user_id nullable
- status
- document_number nullable
- snapshot_json
- stored_file_id nullable
- issued_at nullable
- revoked_at nullable
- supersedes_document_id nullable
- timestamps

### verification_tokens
Central public verification/lookup token.

Fields:
- id UUID
- subject_type
- subject_id UUID
- purpose
- token_hash unique
- public_code nullable unique
- active
- expires_at nullable
- revoked_at nullable
- created_at

QR contains the verification/lookup URL/token, not private subject data.

## 14. Public website / CMS

V1 keeps CMS small.

### news_posts
Fields:
- id UUID
- edition_id nullable
- slug unique
- category
- title_i18n JSON
- excerpt_i18n JSON nullable
- body_i18n JSON
- hero_file_id nullable
- published_at nullable
- status
- timestamps

### faqs
Fields:
- id UUID
- edition_id nullable
- category
- question_i18n JSON
- answer_i18n JSON
- display_order
- published boolean
- timestamps

### edition_documents
Official downloadable templates/guides/schedule exports.

Fields:
- id UUID
- edition_id FK
- document_type
- title_i18n JSON
- locale nullable
- version
- stored_file_id FK
- status: CURRENT / SUPERSEDED
- published_at nullable
- supersedes_document_id nullable
- timestamps

## 15. Notification / infrastructure tables

Use Laravel standard tables where appropriate:
- notifications
- jobs
- failed_jobs
- job_batches if needed
- sessions
- cache / cache_locks

Use Spatie Activitylog package table for activity/audit history.

## 16. Number sequence

### number_sequences
Provides atomic edition-scoped business numbering where needed.

Fields:
- id UUID
- edition_id FK
- sequence_type
- prefix/configuration_json
- last_value
- timestamps

Constraint:
- unique edition_id + sequence_type.

Used for Registration ID, Paper ID, certificate/document number as appropriate.

## 17. Critical deletion rules

RESTRICT deletion or replace with lifecycle action for:
- confirmed registrations
- verified payments
- official submission snapshots
- submitted reviews
- academic decisions
- published schedules/presentation facts
- publication snapshots
- finalized awards
- issued certificates/documents
- audit records

Cascade is acceptable only for dependent draft/configuration records where historical truth is not lost.

## 18. Critical indexes

At minimum index:
- all foreign keys;
- edition_id + status on major operational tables;
- registration_code;
- edition_id + paper_code;
- submission_id + academic_status where relevant;
- payment status / submitted_at;
- review reviewer/status/due_at;
- session/room/start time;
- publication status/readiness;
- certificate status/type;
- generated document type/status;
- public slug;
- verification token hash;
- ROR URI;
- ORCID URI where queried.

## 19. ERD freeze result

This relational contract is **FROZEN for accelerated V1**.

Implementation may adjust:
- exact VARCHAR lengths;
- index names;
- migration ordering;
- framework-generated columns;
- low-level package columns

without reopening ARCH-001, provided the relationships/invariants above do not change.

A structural change that alters lifecycle semantics, cardinality, historical integrity, publication metadata truth, or authority boundaries requires architecture review.
