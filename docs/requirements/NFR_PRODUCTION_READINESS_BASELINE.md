# Non-Functional Requirements — Part 10 Deployment, Configuration, Environment & Production Readiness

**ID:** ICP-REQ-NFR-001-P10  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Objective

Ensure the platform can move from development to staging and production through repeatable, traceable, recoverable processes rather than ad-hoc developer memory or direct production mutation.

## Environment separation

Minimum conceptual environments:

```text
LOCAL / DEVELOPMENT
STAGING
PRODUCTION
```

They are operationally distinct.

Production credentials/data/storage/integration endpoints are not casually reused by non-production environments.

## Non-production data

Local, test, demo, and staging environments use synthetic or redacted data by default.

Production participant/manuscript/Finance/reviewer/audit data may be used outside production only through an explicitly authorized and controlled process.

## Code, configuration, secrets

```text
SOURCE CODE
≠
NON-SECRET CONFIGURATION
≠
SECRETS
```

Environment-specific secrets are not hardcoded into application source.

Repositories may contain safe configuration templates/examples without real credentials.

## Secret isolation

Production secrets are distinct from non-production credentials where material.

Examples:
- application/signing secrets;
- database credentials;
- SMTP credentials;
- object-storage credentials;
- WhatsApp/API credentials;
- OJS/provider credentials;
- webhook secrets;
- monitoring credentials.

Compromise of a developer/local environment must not automatically reveal production credentials.

## Production debug

Production must not expose framework debugging detail to users.

Do not expose:
- stack trace;
- SQL;
- environment variables;
- filesystem paths;
- secrets;
- internal framework detail.

Use safe user-facing error references and internal observability.

## Configuration validation / documentation

Critical configuration should be validated during startup/deployment where practical.

Canonical configuration documentation should describe:
- variable/config item;
- purpose;
- required/optional;
- expected environment(s);
- sensitive/non-sensitive;
- safe example format.

Do not document real secrets.

## Database migration

Schema/data migrations are controlled, versioned deployment steps associated with application release traceability.

Production migration state must be knowable.

## Destructive migration/data operation

Operations such as:
- DROP TABLE/COLUMN;
- destructive rewrite;
- mass data correction;
- irreversible transformation;

require explicit identification, review, verification, and recovery awareness.

Where appropriate, prepare an appropriate recovery point or maintenance plan.

V1 does not require enterprise zero-downtime migration for every change, but destructive operations receive heightened control.

## Repeatable deployment

Deployment should follow a reproducible process such as:

```text
build
→ verify
→ deploy
→ migrate
→ update/restart workers/schedulers if needed
→ health check
→ smoke verification
```

Exact CI/CD technology is deferred.

## Release identity

Production must expose/retain traceability to:
- release/version identity;
- source commit SHA;
- deployment timestamp;
- environment;
- deployment result/status.

## Deployment history

Operational history should record what was deployed, when, where, and the result; actor/automation identity should be retained where supported.

## Quality gate

Production release requires appropriate gates, including as relevant:
- automated tests;
- configuration validation;
- migration sanity;
- security-sensitive regression;
- critical workflow regression;
- build/package success;
- UAT/release approval for changes requiring human acceptance.

Repository branch policy remains:

```text
develop = integration
main = stable/release
```

Feature/develop branches are not direct production release sources.

## Rollback / recovery

Significant deployments require a rollback/recovery plan.

Planning must consider:
- application rollback;
- database compatibility;
- newly-created authoritative data;
- partial migrations;
- queued/background work.

Rollback must not casually delete valid business data created after a release.

## Feature flags

Feature flags may support:
- disabled-first deployment;
- staged activation;
- edition-scoped activation;
- risk containment.

Feature flags are not authorization/security controls.

## Business configuration vs deployment

```text
EDITION / BUSINESS CONFIGURATION
≠
SOFTWARE DEPLOYMENT
```

Examples of business configuration:
- deadlines;
- conference fees;
- refund rules;
- review mode;
- certificate templates/rules;
- publication destination/policy.

These should not require software redeployment merely to change normal edition policy.

Conversely, deploying new software must not silently change business policy without an explicit approved configuration/data migration.

## Seed/demo safety

Development/test seed data is allowed.

Production must not receive unsafe default/demo artifacts such as:
- predictable privileged credentials;
- fake Finance/Reviewer accounts;
- demo participants;
- sample production-like secrets.

Initial privileged access uses secure provisioning.

## Sandbox vs production integrations

Where providers support test/sandbox mode, non-production endpoints/configuration should be separated from production.

Examples:
- future payment sandbox;
- test webhook;
- email/test-domain configuration;
- OJS test/staging target;
- storage endpoint.

Test systems must not silently mutate real downstream production systems.

## Workers / schedulers / jobs

Deployment readiness includes:
- web application;
- background workers;
- schedulers;
- long-running jobs;
- job consumers.

Old/stale worker code must not remain indefinitely incompatible with newly deployed application/schema behavior.

## Scheduled job boundaries

Scheduled tasks have clear environment/ownership boundaries.

Examples:
- deadline processing;
- integration retry;
- notification retry;
- temporary-file cleanup;
- certificate batch;
- backup orchestration.

A non-production scheduler must not process production resources.

## Recovery preparation for high-risk changes

Higher-risk production data/schema changes require stronger recovery preparation.

Not every low-risk visual deployment requires a fresh full backup, but riskier data operations must have a suitable recovery path consistent with the approved RPO/RTO baseline.

## Post-deployment verification

A successful deployment pipeline result does not alone prove production health.

Verify, as applicable:
- application health;
- database connectivity/health;
- storage;
- workers/queues;
- schedulers;
- critical integrations;
- core route/workflow smoke checks.

Smoke tests must not pollute production with fake financial/academic/certificate/publication records.

## Environment parity

Staging and production use different secrets/domains/endpoints as appropriate, while maintaining sufficiently similar architecture/configuration shape to reduce environment-specific surprises.

## Production access

Production operational access follows least privilege.

Not every developer/operator automatically needs:
- database console;
- shell;
- raw participant data;
- raw Finance evidence;
- backup download;
- secret access.

Existing Technical Admin minimum-necessary-data boundaries remain applicable.

## Ad-hoc production mutation

Avoid:
- editing deployed source directly;
- unaudited manual SQL changes;
- undocumented hotfixes.

Emergency action may sometimes be required, but must remain traceable, controlled, and reconciled back to canonical repository/process documentation.

## Operational runbooks

Maintain documentation for at least:
- deployment;
- rollback;
- restore;
- health inspection;
- failed background jobs;
- backup verification;
- secret rotation;
- critical integration recovery.

Operational knowledge must not exist only in one person's memory.

## Secret rotation

The platform/operations design must support credential rotation/revocation without rewriting authoritative business data.

Workers/deployments must eventually use current credentials after rotation.

## Production readiness checklist

First production launch requires a canonical readiness artifact/checklist covering at minimum:
- HTTPS/domain;
- production debug disabled;
- privileged authentication/MFA readiness;
- production secret/config validation;
- database/storage;
- automated backup;
- restore test;
- monitoring/alerts;
- email/active integration configuration;
- workers/schedulers;
- privacy/log redaction;
- file/storage integrity;
- id/en/ar critical translation completeness;
- Arabic RTL sanity;
- accessibility sanity;
- critical regression;
- deployment/rollback documentation;
- secure initial privileged-account provisioning.

A future canonical artifact such as:

```text
PRODUCTION_READINESS_CHECKLIST.md
```

will be created during architecture/release preparation.

Major editions may use a scoped readiness review before critical windows.

## Deferred implementation details

Phase 0 does not lock:
- hosting/cloud vendor;
- container/runtime platform;
- CI/CD provider;
- secret-management product;
- exact migration framework;
- feature-flag provider;
- deployment topology;
- exact rollback automation;
- staging URL/domain;
- exact release numbering scheme.

## Next

Part 11 defines Compatibility, Browser & Device Support.  
Part 12 then defines Maintainability, Testability & Operational Support before final NFR consistency audit.
