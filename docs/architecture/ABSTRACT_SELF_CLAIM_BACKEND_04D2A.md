# Phase 04D.2-A — Abstract Self-Claim Backend

Product Owner scope: 2026-10-24. Authority: [Operational Rebaseline](../governance/ICHES_2027_OPERATIONAL_REBASELINE_2026_10_23.md), PD-085–PD-096 and [execution roadmap](../project-management/ICHES_2027_EXECUTION_ROADMAP.md). Local backend work only; no Pool page, new routes, Committee release UI, deployment or live-data readiness claim.

## Domain contract

`AbstractPoolEligibility::query(reviewer, edition)` returns only Submission discovery columns. It requires the active Edition context, verified account, a permission-free Edition-scoped `reviewer` designation, active same-Edition Topic eligibility, SUBMITTED state, an unarchived Edition, valid current immutable snapshot identity/version/checksum, and no submitting-owner/contributor/recorded-COI conflict. The latest current-version technical/scope exception blocks discovery until resolved. Screening PASS is never required for the ordinary Pool path. Snapshot metadata and private file references are validated internally and are not returned by Pool discovery.

`ClaimAbstractReviewAction::handle(reviewer, submission, data)` accepts `version`, `snapshot_id` and accepted `no_conflict_declared`. It locks Submission, Edition, User/designation, Track/eligibility, current snapshot and Abstract assignments, then revalidates eligibility. Shared affiliation retains the existing Committee acknowledgement path rather than allowing the reviewer to acknowledge it for themselves.

A successful claim creates an ACCEPTED, SINGLE_ANONYMOUS, ABSTRACT assignment with source SELF_CLAIM and a minimal `abstract_review_claimed` audit event. It changes only Submission academic status to UNDER_REVIEW; it does not create a Screening row or change scholarly metadata/snapshots. The existing reviewer draft/final-review domain works without PASS. Same-reviewer retries return the same current claim without new rows/audits, provided identity, eligibility, snapshot and conflict guards still hold. Cancelled/declined/COI assignments are never silently reactivated. Completed current-version evidence blocks a different claim; earlier-version history remains separate.

The ordinary legacy Committee assignment path retains its historical screening contract in this bounded batch, gains a graceful exclusive-owner check and filters Abstract stage. Abstract review/respond/cancel operations cannot act on a future-stage assignment. Academic decisions, release policy and LoA are outside this unit; the existing decision PASS dependency is not claimed removed.

## Database contract

Migration `2026_10_24_120000_add_exclusive_stage_ownership_to_review_assignments.php` adds:

- `academic_stage`, default ABSTRACT for existing legacy rows;
- `assignment_source`, default MANUAL for existing rows;
- virtual generated `active_owner_slot`: 1 for INVITED/ACCEPTED, NULL otherwise;
- UNIQUE `(submission_id, academic_stage, active_owner_slot)`, covering concurrent writers and all snapshots;
- stage-scoped snapshot/reviewer uniqueness, replacing the older stage-agnostic key.

The replacement snapshot index is installed before removing the old key, preserving MySQL foreign-key index coverage. Terminal rows remain intact and do not occupy an active slot. Stage identity separates Abstract ownership from future conference/Full Paper work; no future-stage workflow is implemented. The preflight refuses conflicting legacy active owners before DDL. Rollback refuses to collapse distinct stage histories into an incompatible legacy key. Deployment must quiesce assignment writers during migration and inspect conflicts; no automatic remediation is authorized.

The migration has NOT run against the existing local MySQL database. MySQL DDL autocommits: verify the migration against a separately provisioned empty schema before scheduling any live application.

## Verification and next gate

SQLite tests use new in-memory connection lifetimes and ordinary forward migrations, with no RefreshDatabase, migrate:fresh, rollback, truncate or database deletion. They cover discovery, snapshot integrity, COI, explicit declaration, stale requests, retry, direct unique-key violations including status activation, legacy conflicts/history, stage separation, private access and existing assigned review.

The opt-in MySQL concurrency test uses two PHP processes released by one start barrier. Phase 04D.2-A.1 hardens both parent and worker with the test-only `Tests\Support\ClaimMysqlConnectionGuard`. It captures the original application identities from configuration, URLs and the original `.env` without opening an application connection, and always forbids the verified working schema `international_conference_platform`.

The harness requires **all** process environment fields: `ICHES_CLAIM_MYSQL_TEST_ENABLED=1`, `ICHES_CLAIM_MYSQL_TEST_DATABASE=iches_claim_test_<unique_suffix>`, and explicit `ICHES_CLAIM_MYSQL_TEST_HOST`, `PORT`, `USERNAME`, `PASSWORD`. No application credential fallback is permitted. The account must differ from captured application accounts and have grants only on the exact temporary schema, without global privileges, roles, mandatory roles or GRANT OPTION. A global USAGE grant is harmless and permitted. When MySQL partial_revokes is off, underscores in the schema grant must be escaped so they cannot act as privilege wildcards; when it is on, the grant uses the literal schema name.

Before access, the guard purges resolved connections, rebuilds connection configuration without inherited URLs, sockets or read/write replicas, explicitly reconnects, and checks `SELECT DATABASE()`, `CURRENT_USER()`, active/mandatory roles and `SHOW GRANTS FOR CURRENT_USER()`. Any mismatch blocks the default connection and raises a sanitized diagnostic with no underlying connector exception. Workers perform the same independent checks before application providers boot, before READY, and immediately before claims. The parent verifies before migration; it rejects a nonempty temporary schema. Successful runs use forward migrations only and retain evidence. Provisioning and cleanup are outside the test and require separate approval. A disposable verification schema is not a second operational development/UAT database.

Phase 04D.2-A.1 is code-only: mocked safety checks and deliberately rejected worker subprocesses do not connect to MySQL. The real two-process test has not run, the working migration remains pending, and MySQL concurrency PASS is not established.

Run the isolated files explicitly:

```powershell
php vendor/bin/pest tests/Isolated/AbstractSelfClaimTest.php tests/Isolated/AbstractClaimMysqlConcurrencyTest.php
```

Phase 04D.2-B may plan against this backend contract after focused checks pass. End-to-end implementation/UAT readiness remains conditional on isolated MySQL migration/concurrency PASS and separately approved application of the new migration. No Pool UI, endpoints or production readiness is delivered by this batch.
