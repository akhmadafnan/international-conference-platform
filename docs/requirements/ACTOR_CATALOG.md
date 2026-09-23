# Actor Catalog — Approved Baseline

**Status:** PRODUCT OWNER APPROVED BASELINE — permission model Parts 1–2 approved; detailed actor-by-domain matrix still in progress

## Participant-side actors

- **Visitor** — public user browsing conference information.
- **Prospective Participant** — user preparing to join an edition.
- **Participant** — base membership/status for a person registered in a conference edition.
- **Author / Corresponding Author** — manages a submission and primary correspondence as applicable.
- **Co-author / Contributor** — scholarly contributor who may exist without a login account.
- **Presenter** — person designated to present a submission; not automatically identical to the corresponding author.
- **Non-presenting Participant** — attends without presenting a paper.
- **Invited Speaker / Keynote** — edition-dependent special participant role.

## Internal actors

- **Front Office** — participant-facing communication/support and escalation.
- **Finance Staff / Verifier** — payment/refund/finance operations.
- **Scientific / Academic Committee** — academic governance, screening, reviewer management/monitoring.
- **Reviewer** — assigned academic evaluation.
- **Academic Decision Authority** — final decision authority where the edition policy requires it.
- **Event Operations** — venue/session/presentation/attendance operations.
- **Session Chair** — session-scoped authority.
- **Moderator** — session moderation.
- **Publication Team / Proceeding Editor** — publication gate, final metadata, proceedings/OJS handoff.
- **Technical Administrator** — technical system administration without automatically receiving academic/finance authority.
- **Conference Administrator** — edition-level administrative configuration/operations.
- **Super Administrator** — platform-level global administration.

## Approved role principles

1. One person may hold multiple roles.
2. Most application roles are edition-scoped.
3. Super Administrator is the primary global platform role.
4. Participant is a base edition membership/status concept, not an exclusive single role.
5. Author and Presenter are distinct.
6. Co-authors/contributors need not have login accounts.
7. Scholarly identity is conceptually separate from authentication/account identity.
8. ORCID is optional. A person does not need an ORCID to register, become an author/co-author, present, review, or otherwise participate.
9. If available, ORCID may be attached to scholarly identity and later distinguished as manually supplied vs authenticated/verified.
10. Domain authorities remain separated:
   - Front Office communicates/escalates;
   - Finance controls finance states;
   - Academic roles control academic decisions;
   - Event roles control event operations;
   - Publication roles control publication processing.
11. Reviewer and Author may coexist, subject to later conflict-of-interest controls.

## Conceptual multi-edition example

```text
Person A
├── Edition 2027
│   ├── Participant
│   ├── Author
│   └── Presenter
└── Edition 2029
    ├── Participant
    ├── Reviewer
    └── Academic Committee
```

## Still pending

Phase 0 must still define:
- detailed permission matrix;
- role assignment/revocation authority;
- conflict-of-interest rules;
- edition membership lifecycle;
- invitation/onboarding for internal roles;
- whether special actors such as Sponsor or Steering Committee require system roles or only metadata/content representation.


## Approved authorization foundation

The permission model is not a flat role checkbox list. Authorization evaluates:

```text
ROLE
+ SCOPE
+ RESOURCE RELATIONSHIP
+ DOMAIN AUTHORITY
+ RESOURCE STATE
+ RESTRICTIONS / COI
= EFFECTIVE PERMISSION
```

Core rules:
- default deny;
- least privilege;
- visibility does not imply authority;
- technical authority does not imply business authority;
- Super Administrator does not automatically receive Finance/Academic/Event/Publication decision powers;
- Conference Administrator coordinates an edition but does not automatically own every authoritative state change;
- permissions are scope-aware: global, edition, session, submission/membership, review assignment;
- multi-role is allowed, but explicit restrictions/COI override a normal allow;
- sensitive data follows need-to-know;
- override/exception permissions are distinct and auditable;
- assignment authority and final decision authority are distinct;
- archived/historical corrections require special controlled authority;
- authorization must be enforced server-side, not only through hidden UI controls.


## Administrative role boundaries — approved

- **Super Administrator** = global platform governance and protected access administration; not automatic business-domain decision authority.
- **Technical Administrator** = system operations/diagnostics; confidential business data is minimum-necessary and business decisions remain denied by default.
- **Conference Administrator** = edition operations/configuration and cross-domain status visibility; authoritative Finance/Academic/Event/Publication decisions require additional roles.
- Protected-role self-escalation is denied.
- Global roles cannot be assigned by ordinary edition-level administration.
- Emergency/break-glass access, when implemented, must be temporary/reasoned/audited and does not rewrite the underlying business history.
