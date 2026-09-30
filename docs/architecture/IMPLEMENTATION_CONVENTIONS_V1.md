# Implementation Conventions v1

**Status:** FROZEN BASELINE
**Updated:** 2026-09-30

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
