# Permission Model — Part 2 Administrative Roles

**ID:** ICP-REQ-PERM-001-P2  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-23

## Role separation

```text
SUPER ADMIN
→ Platform Governance

TECHNICAL ADMIN
→ System Operations

CONFERENCE ADMIN
→ Edition Operations

FINANCE
→ Financial Authority

ACADEMIC
→ Academic Authority

EVENT
→ Event Authority

PUBLICATION
→ Publication Authority
```

Administrative capability does not automatically imply business-domain decision authority.

## Super Administrator

Scope: GLOBAL.

### Allowed baseline
- create/manage conference series and editions;
- assign/revoke authorized platform-level administrative access;
- manage platform-level configuration;
- manage account/security administration;
- inspect global security/audit information;
- activate/suspend relevant platform resources;
- perform emergency platform actions through controlled mechanisms.

### Denied by default unless another role applies
- payment verification;
- refund approval/execution;
- abstract/final-paper academic decision;
- reviewer recommendation;
- presentation verification;
- publication approval.

## Technical Administrator

### Allowed baseline
- system/application configuration;
- system health and diagnostics;
- queue/job operations;
- notification/email delivery diagnostics;
- storage/integration diagnostics;
- maintenance operations;
- backup/restore operations when later specified;
- technical logs/audit subset.

### Data-minimization rule
Technical diagnostics expose the minimum business information necessary.

### Denied by default
- business-domain decisions;
- unrestricted reviewer-confidential data;
- unrestricted Finance-sensitive data;
- unrestricted publication/editorial decision data.

## Conference Administrator

Scope: assigned EDITION.

### Allowed baseline
- edition configuration;
- theme/dates/deadlines;
- tracks;
- public/CMS settings;
- fee/refund-policy configuration;
- submission/review-stage configuration;
- event/schedule/presentation configuration;
- publication/certificate configuration;
- operational dashboard/status visibility;
- coordinate edition roles subject to protected-role rules.

### Boundary

```text
CONFIGURE WORKFLOW/POLICY
≠
EXECUTE AUTHORITATIVE DOMAIN DECISION
```

Examples:
- configure fee: ALLOW;
- verify specific payment: DENY unless Finance.
- configure review mode: ALLOW;
- accept/reject submission: DENY unless Academic Decision Authority.
- configure presentation process: ALLOW;
- mark PRESENTED: DENY unless Event authority.
- configure publication workflow: ALLOW;
- approve publication: DENY unless Publication/Academic authority.

## Role assignment governance

### Global protected roles
At minimum:
- Super Administrator;
- Technical Administrator.

Ordinary edition administration cannot assign these roles.

### Sensitive edition roles
May include:
- Finance;
- Academic Decision Authority;
- Publication authority;
- other roles later classified protected.

Exact assignment authority is finalized in Part 9.

## Anti-self-escalation

No administrator may use normal role-management capability to grant themselves a protected role or bypass separation of duties.

## Break-glass access

A controlled emergency-access mechanism may be implemented for serious technical/security incidents.

Minimum required audit:
- actor;
- reason;
- scope/resource;
- access granted;
- start time;
- end/revocation time;
- actions/accesses performed;
- optional incident/reference.

Break-glass access:
- is exceptional;
- is temporary/scoped;
- is auditable;
- does not change the normal business role model;
- does not erase or rewrite prior business-domain history.

## Administrative permission summary

| Capability | Super Admin | Technical Admin | Conference Admin |
|---|---|---|---|
| Global platform config | Allow | Technical subset | Deny |
| Create/manage editions | Allow | Deny by default | Assigned-edition management |
| Technical system operations | Allow/oversight | Allow | Deny |
| Assigned-edition configuration | Allow/oversight | Technical subset | Allow |
| Edition operational status view | Need-based | Minimum necessary | Allow |
| Verify payment | Deny by default | Deny | Deny by default |
| Academic final decision | Deny by default | Deny | Deny by default |
| Verify presentation | Deny by default | Deny | Deny by default |
| Publication approval | Deny by default | Deny | Deny by default |
| Global security audit | Allow | Technical subset | Deny |
| Edition operational history | Need-based | Minimum necessary | Allow |
| Assign global protected roles | Protected global authority | Deny | Deny |
| Self-assign protected role | Deny | Deny | Deny |
| Break-glass | Controlled | Delegated only if policy permits | Deny by default |

## Next

Part 3 defines:
- Participant;
- Author / Corresponding Author;
- Co-author / Contributor;
- Presenter;
- Non-presenting Participant;
- Invited Speaker / Keynote.
