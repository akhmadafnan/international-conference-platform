# Authentication & Registration Baseline

**ID:** ICP-REQ-AUTH-001  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-23

## Model

Progressive/Hybrid Account Model.

## Participant onboarding

```text
email
→ email verification
→ reusable account/workspace
→ profile
→ edition membership
→ participant/author workflow
```

A password is not mandatory at first registration.

## Participant returning access

Secure email link/code is an accepted product pattern. Password/passkey/MFA may later strengthen the account.

## Cross-edition behavior

One account persists across editions. Edition-specific membership, roles, and historical data remain separate.

## Co-author behavior

Co-author/contributor account is optional.

## Reviewer behavior

Reviewer access requires an authenticated account.

## Privileged internal accounts

Super Admin, Conference Admin, Finance, Academic Decision Authority, Publication administration, and other sensitive roles must support stronger-authentication/MFA policy.

## Recovery

Front Office:
- may explain recovery procedure;
- may create/escalate support cases;
- may not impersonate users;
- may not manually take over accounts;
- may not bypass identity verification.

Account recovery and primary-email change are sensitive controlled flows.

## Identity boundaries

```text
PERSON / SCHOLARLY IDENTITY
├── name
├── affiliations
└── ORCID optional

ACCOUNT / AUTHENTICATION
├── verified email
├── optional password
├── optional/future passkey
└── stronger auth/MFA where required

EDITION MEMBERSHIP
└── roles and conference-specific participation
```

ORCID is never required as the authentication identity.

## Deferred technical decisions

Not locked in Phase 0:
- authentication library/framework;
- magic-link implementation;
- OTP transport/details;
- passkey provider/library;
- MFA method;
- session/token implementation;
- exact recovery mechanics.

These belong to architecture/feature specifications after the product rules are stable.


## NFR Security & Access Protection — approved

The authentication model must satisfy the following non-functional security baseline:

- production traffic uses HTTPS/secure transport;
- protected requests are authorized server-side on every request;
- high-risk internal authorities require MFA in production;
- Participant MFA is not mandatory for V1;
- Reviewer MFA is supported and may be required by edition/security policy;
- sensitive authority/security operations may require re-authentication/step-up verification;
- revocation must prevent the next protected operation even when the previous authenticated session still exists;
- login, verification, magic-link/OTP request, recovery, and sensitive endpoints use abuse/rate limiting controls;
- public auth/recovery responses avoid unnecessary account enumeration;
- magic-link/OTP credentials are unpredictable, time-limited, single-use, action/account-bound, and excluded from plaintext logs/analytics;
- recovery and primary-email change remain controlled sensitive workflows;
- Front Office never gains account-takeover authority;
- silent impersonation remains prohibited and V1 has no impersonation requirement.

Exact MFA technology, authentication framework, token format, session implementation, and re-authentication mechanism remain deferred.
