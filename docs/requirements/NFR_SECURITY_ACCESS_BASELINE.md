# Non-Functional Requirements — Part 1 Security & Access Protection

**ID:** ICP-REQ-NFR-001-P1  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Objective

Define product-level security behavior without prematurely locking authentication libraries, MFA providers, session technology, hosting stack, or framework-specific implementation.

## Security posture

The product handles:
- participant identity/contact data;
- unpublished scholarly manuscripts;
- payment/refund evidence;
- reviewer confidentiality;
- certificates;
- privileged administrative actions.

The target is proportionate security for an initial approximately 100-participant conference while remaining reusable for future editions.

## Secure transport

Production traffic must use HTTPS/secure transport.

Sensitive authenticated actions must not be intentionally served through plaintext transport.

## Server-side authorization

All protected operations require server-side authorization.

UI visibility is not a security control.

Authorization follows the canonical permission model:
- default deny;
- least privilege;
- role/capability;
- scope;
- resource relationship;
- resource state;
- COI/restriction.

## MFA baseline

Mandatory MFA in production applies to high-risk internal authorities, including at minimum:
- Super Administrator;
- Technical Administrator;
- Conference Administrator;
- Finance;
- Academic Committee;
- Academic Decision Authority;
- Publication Team / Proceeding Editor;
- privileged certificate authority;
- historical.correct;
- archive/unarchive authority;
- protected-role assignment authority;
- business-override authority;
- break-glass authority.

Participant/Author MFA is not mandatory for V1.

Reviewer requires authenticated account access. Reviewer MFA must be supported and may be required by edition/security policy.

## Sensitive re-authentication

Highly sensitive operations may require recent re-authentication or step-up verification.

Candidate actions include:
- assign protected/global authority;
- primary-email change;
- account recovery;
- break-glass activation;
- edition unarchive;
- historical correction;
- high-risk override/security changes.

Being logged in does not imply unlimited sensitive authority indefinitely.

## Revocation behavior

Revoked authority must stop authorizing the next protected request/action.

An already-open browser/session must not preserve business authority merely because it was authenticated before revocation.

Exact session/token invalidation mechanics are deferred to architecture/security design.

## Abuse protection

Rate limiting/throttling or equivalent abuse controls are required for:
- login/authentication;
- verification-code request;
- magic-link request;
- recovery;
- primary-email change workflow;
- other sensitive endpoints.

Controls should reduce brute force, request flooding, token abuse, and automated misuse.

## Account enumeration

Public authentication/recovery flows should avoid revealing more than necessary about whether a specific email/account exists.

Operational/admin workflows may expose status only to properly authorized users.

## Magic-link / OTP security

Authentication/verification tokens must be:
- cryptographically unpredictable;
- short-lived;
- single-use;
- bound to the intended action/account/context;
- invalid after successful use;
- excluded from plaintext application logs, analytics, and ordinary error output.

## Recovery and email change

Account recovery and primary-email change are sensitive controlled workflows.

Front Office may guide/escalate but cannot:
- impersonate;
- take over the account;
- silently change identity ownership;
- bypass identity verification.

## Upload security baseline

All uploaded files are untrusted input.

A successful upload does not imply that:
- file type/content is safe;
- file may be executed;
- file may be served publicly;
- private download authorization can be bypassed.

Detailed size/type/storage/scanning/integrity requirements belong to later NFR parts/specifications.

## Secret handling

Credentials/secrets must not be committed to source control or exposed in client-side bundles.

Examples:
- application secrets;
- database credentials;
- SMTP credentials;
- API tokens;
- payment/WhatsApp/integration credentials;
- signing secrets.

Sensitive tokens/credentials must not appear in plaintext logs.

## Production error disclosure

Production user-facing errors must not expose:
- stack traces;
- SQL details;
- secrets/tokens;
- internal credentials;
- sensitive filesystem paths;
- unnecessary infrastructure internals.

Diagnostic detail belongs in authorized internal observability channels.

## Security audit events

Security-sensitive events must be auditable, including where applicable:
- abnormal/failed login patterns;
- recovery actions;
- primary-email/security changes;
- MFA changes;
- protected role/capability assignment/revocation;
- break-glass activation/expiry;
- sensitive override execution;
- sensitive account/access changes.

## Impersonation

Silent impersonation/account takeover is prohibited.

V1 does not require an impersonation feature.

If a future support-impersonation feature is approved, it requires its own privileged, visible, reasoned, time-bounded, audited specification.

## Deferred implementation details

Phase 0 does not lock:
- authentication package/framework;
- MFA provider/method;
- OTP transport;
- magic-link implementation;
- session/token storage mechanism;
- rate-limit library;
- WAF/security provider;
- file-scanning product;
- infrastructure secret-management product.

## Acceptance direction

These requirements must become testable security acceptance criteria during architecture/specification and implementation.

## Next

Part 2 defines Privacy, PII & Sensitive Data Protection.
