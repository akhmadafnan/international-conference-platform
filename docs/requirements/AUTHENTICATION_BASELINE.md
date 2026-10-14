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

## Password usability policy — Product Owner amendment (2026-10-14)

For the current application's password-based paths (initial registration, reset,
change password and trusted local/global superadmin bootstrap), the minimum is
**six characters** with **no required mix of uppercase, lowercase, digits or
symbols**. Password confirmation and current-password validation still apply
where relevant. Existing account passwords are never reset by this policy change.

This deliberately prioritizes low-friction onboarding; **six-character passwords
provide limited brute-force resistance**. The existing authentication controls
(email verification, credential hashing, session protection and login throttling)
must be retained. The approved privileged-production MFA/step-up requirement
**remains mandatory before production release** and is not satisfied by a
password-policy change.

Google sign-in/OIDC is a separately scoped future feature. Provider-verified email
and safe account linkage must be evaluated without silently joining accounts on
unverified email, weakening privilege checks or replacing permission checks.
The project's password-optional onboarding baseline remains a product intent,
not a claim that the current Fortify form already supports passwordless registration.
