# Implementation Conventions v1

**Status:** FROZEN BASELINE
**Updated:** 2026-10-02

## Backend convention

Controllers stay thin.

Use Form Requests for validation and authorization-entry checks.

Use Actions for consequential behavior:
- each Action has one business intent;
- applies business guards;
- wraps multi-record mutations in a DB transaction;
- emits notification/job/domain event only after durable state is established;
- is directly testable.

Avoid Service classes that merely proxy one Eloquent call.

## Code organization convention

The application remains one Laravel modular monolith with explicit backend and frontend boundaries.

Canonical backend request flow:

`Route → Controller → FormRequest → Policy/Gate → Action → Model → Persistence/Audit`

Rules:
- Controllers stay thin and coordinate HTTP concerns only.
- Form Requests own validation and request-level authorization entry checks.
- Policies/Gates own server-side resource authorization.
- Actions represent one consequential business intent and contain transactional orchestration.
- Models own relationships, casts, query scopes, and limited domain behavior.
- Business history remains in explicit domain records/version tables.
- Shared technical helpers belong in focused Support or Concern classes, not generic catch-all Service classes.
- Do not introduce Repository or Service abstractions that merely proxy Eloquent.

Canonical frontend flow:

`Inertia Page → Feature Component → Composable → Typed Props/Domain Types → UI Primitive`

Rules:
- Vue uses Composition API + TypeScript.
- Server-authoritative business rules stay in Laravel, not Vue.
- Pages are Inertia entry points, not business-logic containers.
- Feature-specific UI belongs with its feature.
- Shared visual primitives live separately from domain components.
- Inertia props and domain-facing frontend contracts are explicitly typed.
- Do not introduce Vue Router or a duplicate client REST architecture for normal first-party flows.

Backend and frontend naming should follow the same domain vocabulary so a feature can be traced vertically through the codebase.

## Identifier convention

First-class application and domain entities use UUIDv7 primary keys stored as standard UUID strings (`CHAR(36)` on MySQL).

Laravel models use the framework-native `HasUuids` concern. In the Laravel 13 baseline this concern generates UUIDv7 identifiers.

`users.id` follows the same UUIDv7 convention as other first-class domain entities.

Foreign keys referencing UUID-backed entities must use UUID-compatible columns. Prefer `foreignUuid()` where an explicit foreign-key constraint is appropriate and `uuid()` where an unconstrained reference is intentional.

Laravel infrastructure tables retain their framework-native identifiers unless compatibility with a UUID-backed entity requires otherwise. `sessions.user_id` is UUID-compatible because it references `users.id`.

Human-facing identifiers such as Registration ID, Paper ID, certificate number, and document number remain separate from technical primary keys.

UUIDs reduce predictable sequential enumeration but are not secrets and do not replace Policies/Gates, authorization checks, signed URLs, random verification tokens, rate limiting, or other security controls.

## Authorization and audit convention

Authorization uses Spatie Laravel Permission with Teams enabled.

The team scope is `conference_edition_id`.

Security identities use UUIDv7:
- users;
- roles;
- permissions;
- model authorization pivots;
- conference edition team keys.

Roles and permissions provide edition-scoped capability vocabulary. They do not replace Laravel Policies/Gates.

Policies and domain guards remain authoritative for:
- resource ownership/relationship;
- edition membership;
- conflict of interest;
- state-transition eligibility;
- other business integrity rules.

Spatie Activitylog records consequential operator/system actions where audit evidence is required.

Activity Log is audit evidence, not the source of truth for business history. Versioned manuscripts, decisions, payments, publication snapshots, certificate lifecycle records, and other consequential facts remain explicit domain records.

`activity_log.id` remains an infrastructure sequence, while subject and causer polymorphic identifiers are UUID-compatible.

## Status convention

Do not create one giant global status enum.

Keep independent state dimensions:
- registration status
- payment status
- submission/academic status
- schedule publication state
- presentation outcome
- publication status
- award state
- certificate state

Human-facing labels are localized separately from stored codes.

## Date/time convention

- store timestamps in UTC;
- store Edition timezone as IANA name;
- convert for display/input;
- store all-day conference dates as DATE where time is irrelevant.

## Money convention

- use fixed-precision DECIMAL, never floating point;
- store ISO currency code;
- payment records snapshot expected amount at transaction time.

## Document numbering

Internal UUID is not public document number.

Number generators are locked once an official document is issued and support edition-configured prefixes/formats.

## Soft-delete convention

Soft delete is allowed for low-risk content/configuration where restoration is meaningful.

Do not delete consequential historical facts as a substitute for:
- cancellation
- revocation
- supersession
- withdrawal
- rejection

## Database constraints

Use DB constraints for invariants where practical, including:
- one participant registration per user per edition;
- unique human Paper ID within edition;
- unique contributor sequence per submission;
- unique registration activity entitlement;
- unique verification token/hash;
- unique issued certificate/document number within its configured numbering scope.

Exactly-one corresponding author is enforced through application transaction guards plus consistency tests if a portable SQL constraint is awkward.

## Files

Never trust original filename for storage path.

Store opaque generated paths/UUIDs.
Preserve original filename only as metadata.

Validate MIME/extension/size and treat uploads as untrusted.

## Localization

UI strings use translation keys.

Scholarly text translations live in scholarly metadata, not UI language files.

CMS translation storage may use localized JSON for V1 where operationally simpler.

## Frontend data

Inertia page props are typed.

Do not create duplicate frontend stores for server-authoritative state without a concrete need.

Use component/composable state for UI-only behavior.

## Queries

Avoid N+1 queries.
Eager-load deliberate relations.
Paginate operational tables.
Index common edition/status/search foreign-key filters.

## Transactions

Consequential actions use DB transactions, especially:
- Finance verification
- academic decision
- schedule publish
- presentation outcome
- publication finalization
- award finalization
- certificate issue/revoke/reissue

## Idempotency

Jobs and repeatable actions that can be retried must be idempotent where feasible, especially:
- queued emails
- bulk PDF generation
- bulk certificate generation
- metadata export generation

## Testing gates

Every bounded implementation batch must pass:
- focused tests
- related regression
- Pint
- static analysis for changed scope where configured
- browser UAT when user-visible
- git diff check
before checkpoint/merge.
