# Non-Functional Requirements — Part 2 Privacy, PII & Sensitive Data Protection

**ID:** ICP-REQ-NFR-001-P2  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Objective

Define product-level privacy and sensitive-data behavior consistent with the lifecycle, permission matrix, reviewer anonymity, Finance restrictions, historical integrity, and future integrations.

## Data-treatment classes

The product should treat information according to purpose and sensitivity.

Conceptual classes:

- **PUBLIC** — intentionally public conference content, published schedule/speaker data, approved publication metadata, minimum certificate-verification data.
- **INTERNAL** — operational status, assigned workflow information, support/edition operations.
- **CONFIDENTIAL** — unpublished manuscripts, participant private contact data, author-facing review content, non-public scholarly/operational data.
- **RESTRICTED** — payment/refund evidence, bank/reconciliation data, anonymous reviewer identity, authentication/security data, confidential editor comments, sensitive audit records.

These are product-level treatment categories; Phase 0 does not require one exact database enum.

## Data minimization

Collect only information reasonably required for defined conference lifecycle purposes.

Do not collect personal data merely because it may be useful someday.

Optional scholarly identifiers such as ORCID remain optional unless a later explicit policy changes this.

## Purpose limitation

Data collected for one conference purpose must not silently be repurposed for unrelated use.

New materially different use requires an explicit product/policy decision.

## Current vs historical identity

```text
CURRENT PROFILE
≠
HISTORICAL SNAPSHOT
```

Changing current affiliation/contact/profile data does not silently rewrite:
- submitted-version metadata;
- accepted historical metadata;
- publication snapshot;
- certificate record;
- archived edition record.

## Private by default

The following are private unless an explicit public workflow says otherwise:
- draft submissions;
- unpublished manuscripts;
- payment proof;
- refund-bank data;
- reviewer assignments;
- review reports;
- confidential editor comments;
- support/internal notes;
- audit/security detail.

Private files do not become publicly accessible merely because they have a stored URL.

## Reviewer confidentiality

Reviewer anonymity and confidential-review boundaries apply to:
- UI;
- API;
- downloads/files;
- file names;
- URLs;
- export;
- email;
- notifications;
- document metadata;
- logs/history visible to unauthorized users.

A double-anonymous assignment must not leak identity through technical metadata.

## Finance-restricted data

Payment proof, bank reconciliation, refund-bank data, and internal Finance notes are RESTRICTED.

Unrelated roles consume safe derived statuses such as:
- payment satisfied/not satisfied;
- refund processing/refunded;

without receiving raw evidence.

## Notification privacy

Email/notification content should contain only the minimum sensitive information required.

Where sensitive detail exists, prefer:
```text
notification
→ authenticated workspace
```

rather than duplicating raw confidential/restricted data into email or third-party messaging.

## URL identifiers

Do not use raw email addresses, reviewer names, bank details, or other confidential/PII values as public resource identifiers.

Opaque/internal identifiers or verification tokens are preferred where public links are necessary.

## Logging privacy

Logs must not contain plaintext:
- passwords;
- OTP/magic-link tokens;
- API/application secrets;
- full bank/refund account data;
- raw payment-proof content;
- unnecessary confidential manuscript/review content.

Use resource IDs/references and redacted diagnostics where possible.

## Public certificate verification

Public verification may expose minimum credential information such as:
- status;
- recipient name;
- certificate type/role;
- event/edition;
- activity date;
- certificate number.

Default public verification does not expose:
- email;
- phone;
- account ID;
- internal reason/note;
- issuer audit;
- created_at/generated_at;
- unrelated PII.

## Search-engine boundary

Authenticated/private workspaces, manuscript downloads, payment/review administration, internal certificate management, audit pages, and other non-public resources must not be intentionally exposed for search-engine indexing.

Public conference/publication pages remain separately governed.

## Export and API parity

```text
NOT AUTHORIZED IN UI
→ NOT AUTHORIZED THROUGH EXPORT/API
```

Export/API endpoints must enforce the same or stricter authorization/data-minimization rules.

Sensitive bulk exports may require audit.

## Third-party data minimization

Integrations receive only the minimum data required for their defined purpose.

Examples:
- OJS receives publication handoff metadata/files, not unrelated Finance/support data;
- WhatsApp/Front Office does not receive confidential review data;
- email provider receives only delivery-required content/addressing;
- future ORCID/ROR/Crossref flows use purpose-specific scholarly metadata.

Integration never bypasses the platform permission/privacy model.

## Retention

Retention must be defined by data class and purpose.

Different data classes may require different retention periods.

Exact durations are deferred until organizational/legal policy is available.

Retention decisions must distinguish, for example:
- temporary authentication tokens;
- operational logs;
- support records;
- payment/refund evidence;
- reviewer/audit history;
- manuscripts;
- publication metadata;
- certificate history.

## Account closure vs historical facts

```text
ACCOUNT CLOSURE
≠
ERASE ALL CONFERENCE HISTORY
```

Closing/deleting an account does not automatically erase historical records that must remain for scholarly, financial, certificate, publication, audit, or organizational integrity.

Personal data no longer required may be deleted/anonymized according to retention/privacy policy.

## Non-production environments

Production PII/confidential data must not be copied casually into:
- local development;
- demo;
- tests;
- staging;
- screenshots;
- fixtures;
- AI prompts.

Use synthetic/redacted data by default.

Any justified production-data use outside production requires a controlled, authorized process.

## Backup privacy

Backups inherit the sensitivity of the data they contain.

A restricted production database remains restricted when backed up.

Backup files must not become an informal/public route to access protected data.

Backup frequency/recovery objectives are defined in NFR Part 3.

## Deferred details

Phase 0 does not yet lock:
- exact retention periods;
- privacy-policy/legal wording;
- anonymization implementation;
- backup encryption provider;
- storage vendor;
- DLP tooling;
- analytics vendor;
- exact consent UI.

## Next

Part 3 defines Reliability, Availability, Backup & Disaster Recovery.
