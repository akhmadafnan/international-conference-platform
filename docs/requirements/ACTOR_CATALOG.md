# Actor Catalog — Approved Baseline

**Status:** PRODUCT OWNER APPROVED BASELINE — detailed permission matrix still pending

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
8. Domain authorities remain separated:
   - Front Office communicates/escalates;
   - Finance controls finance states;
   - Academic roles control academic decisions;
   - Event roles control event operations;
   - Publication roles control publication processing.
9. Reviewer and Author may coexist, subject to later conflict-of-interest controls.

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
