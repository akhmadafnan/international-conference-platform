# Certificate Governance Baseline

**ID:** ICP-REQ-CERT-001  
**Status:** PARTIALLY APPROVED — DETAIL CONTINUES IN STAGE 4F  
**Approved principles:** 2026-09-23

## Core integrity rule

Certificate type/wording must reflect the recipient's documented role, activity, or authorized recognition category.

## Rule-based issuance

Examples:
- Presenter Certificate → verified presentation;
- Participant Certificate → eligibility/attendance rule;
- Reviewer Certificate → completed review rule;
- Committee Certificate → committee membership;
- Session Chair/Moderator Certificate → assigned event role.

Exact eligibility rules remain configurable by edition.

## Manual/ad-hoc issuance

Authorized administrators may generate a certificate manually for exceptional or ad-hoc needs.

Required certificate/issuance data:
- recipient;
- certificate type;
- edition/activity;
- activity/event date used for certificate wording;
- reason/internal note where manual;
- issuing authority;
- rule-based vs manual/exceptional;
- optional evidence/reference;
- immutable internal creation/generation timestamps.

Manual issuance must not fabricate an underlying fact.

## Presenter Certificate

Allowed only when:
- recipient has verified PRESENTED status; or
- an authorized presentation exception/makeup is recorded and edition policy treats it as qualifying.

If a person did not present, use a truthful alternative certificate category such as:
- Participant;
- Committee;
- Guest;
- Supporting Contributor;
- Recognition/Acknowledgement;
- another edition-defined category.

Detailed numbering, QR verification, templates, revocation/reissue, and public verification belong to Stage 4F.


## Manual Certificate Builder

The certificate module must support an authorized manual builder independent from automatic lifecycle issuance.

Modes:
- individual;
- bulk/collective.

Manual recipient may be:
- an existing platform person/user;
- an existing edition participant;
- an external/manual recipient who does not have an account.

Configurable certificate inputs may include:
- recipient full name;
- institution/affiliation;
- certificate type / recognition label;
- edition or activity;
- activity/event date;
- certificate title/wording;
- template;
- signer(s);
- certificate number policy;
- other edition-defined display fields.

Bulk issuance may accept a structured import/paste workflow so each recipient receives an individual certificate record.

Each generated certificate must have its own:
- certificate record;
- certificate number or canonical identifier;
- verification code/token;
- verification link;
- status/history.

## Certificate date semantics

The date shown on a certificate is the **activity/event date** or another explicitly configured certificate display date associated with the event/recognition.

Example:

```text
Activity/event date displayed on certificate: 15 November 2027
Certificate record actually generated later: 20 March 2028
```

The certificate/PDF and normal public verification view do not need to display the later technical generation timestamp.

The platform must separately preserve immutable internal audit timestamps such as:
- record created time;
- generation time;
- reissue/revocation time where applicable.

The displayed activity/event date must not overwrite those technical timestamps.

This allows a certificate for a historical activity to be generated later while keeping the public document focused on the activity date and retaining accurate internal system history.

## Public verification presentation

The public verification page may show only the information necessary to verify the credential, for example:
- validity/status;
- recipient name;
- certificate type/role label;
- event/edition;
- activity/event date;
- certificate number.

Internal generation timestamps, administrative notes, and other audit-only metadata do not need to be public.
