# V1 Authority Baseline

**ID:** ICHES-V1-AUTHZ-001  
**Status:** AUTHORITATIVE V1 BASELINE  
**Updated:** 2026-09-30

## Principle

Committee position and application authority are related but not identical.

Use default deny, least privilege, edition scope, resource relationship, COI restrictions, server-side authorization, and auditability.

## Minimum V1 authority domains

### Conference Admin
Coordinates edition configuration and operational setup without automatically receiving every business-domain authority.

### Finance Authority
May inspect relevant payment proof, verify/correct payment, and process authorized exceptional refund records. Does not receive Academic authority.

### Academic Authority
May manage academic process, record final abstract decisions, record final post-presentation academic approval, and control academic exceptions. Cannot decide own conflicted paper.

### Reviewer
May access only assigned papers/versions and submit assigned review/assessment.

Phase 04D.1-B.1 (Product Owner corrective, 2026-10-24): account selection for Topic management uses one Edition-scoped Spatie `reviewer` designation with no permissions. A demo workspace persona alone is not this designation. The picker lists only verified designated accounts for the selected Edition, excluding reviewers already present in its Topic eligibility history. Save uses the immutable User ID and revalidates verification/designation; Edit retains that identity. Legacy Topic grants also require the designation. No per-Topic roles are introduced. Designation grants no manuscript access or broad Academic authority; Topic eligibility and stage-specific paper assignments remain separate facts.

`LocalConferenceUatReviewerSeeder` is a manual, local/testing, demo-opt-in bootstrap for the existing verified `reviewer@iches.test` demo identity in the existing marked `ICHES-UAT-2027` Edition. It adds only the permission-free designation, preserves users/passwords, unrelated roles and academic records, and refuses an existing Reviewer role with permissions. Execution against local MySQL requires separate Product Owner approval.

### Event / Program Authority
May manage sessions/rooms/slots, publish schedule, and record operational attendance/presentation facts within scope. Does not receive Finance or Academic authority automatically.

### Moderator / Session Role
May receive session-scoped operational actions, including PRESENTED/NO_SHOW when configured.

### Award / Committee Authority
May finalize Best Article / Best Presenter using evidence as advisory input. System ranking does not constrain final Committee decision.

### Publication Authority
May assign/override publication destination, finalize handoff/production operational status, and record external references. Cannot fabricate journal acceptance or bypass missing Final ACC.

### Certificate Authority
May generate, preview, issue, revoke, reissue, and manage authorized external/manual recipients. Manual certificate action does not rewrite attendance/presentation facts.

### Technical Admin
Technical/system authority is limited to system operations and does not automatically become business-domain authority.

### Global Super Admin
Super Admin is an explicit global application authority.

Super Admin may:
- access every Conference Edition;
- access every application module;
- execute every application ability;
- bypass ordinary role/permission checks;
- bypass ordinary Policy/Gate authorization denials whose purpose is only to restrict actor authority.

This authorization bypass does **not** bypass application integrity.

Super Admin remains subject to:
- validation;
- business/domain invariants;
- mandatory workflow and state-transition rules;
- database constraints;
- transaction safety;
- immutable/versioned history;
- mandatory audit/activity logging.

Super Admin is not implemented by assigning every edition-scoped business role.

## Multi-role

One user may hold multiple scoped authorities.

Restrictions/COI override normal allow rules.

All consequential authority changes/actions are auditable.
