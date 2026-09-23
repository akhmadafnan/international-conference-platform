# Feature Specifications

PRD describes **what/why** at product level.

Feature specs describe detailed behavior needed before implementation.

Flow:

```text
PRD capability
→ feature specification
→ READY ticket
→ implementation plan
→ code/tests
→ regression/UAT
→ closeout
```

A feature specification may include:
- actors;
- trigger/preconditions;
- state machine;
- business rules;
- permissions;
- edge/failure cases;
- notifications;
- audit requirements;
- localization;
- acceptance criteria.

Do not put physical database schema into PRD merely because a feature will need persistence.
