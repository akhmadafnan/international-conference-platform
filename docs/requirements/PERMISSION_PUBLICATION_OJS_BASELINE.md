# Permission Model — Part 7 Publication Operations & OJS Handoff

**ID:** ICP-REQ-PERM-001-P7  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Scope

Publication Team / Proceeding Editor is an EDITION-scoped publication-operations authority.

## Authority separation

```text
Academic Decision Authority
→ PUBLICATION_APPROVED

Publication Eligibility Gate
→ PUBLICATION_ELIGIBLE

Publication Team
→ publication operations / OJS handoff / publication-status tracking
```

```text
PUBLICATION_APPROVED
≠
PUBLICATION_ELIGIBLE
≠
PUBLISHED
```

Publication Team does not automatically receive Academic Decision Authority.

## Publication Queue

Publication Team may manage actionable queue items only after required gates are satisfied.

Operational actions may include:
- final metadata validation;
- package readiness;
- READY_FOR_TRANSFER;
- transfer destination;
- manual/assisted OJS/proceedings handoff;
- TRANSFERRED;
- IN_PUBLICATION_PROCESS;
- PUBLISHED;
- controlled correction of publication references/status.

## Publication-required data

Publication Team may access:
- final manuscript/version;
- title;
- abstract;
- keywords;
- language;
- contributor order;
- affiliations;
- optional ORCID;
- publication declarations/consent;
- presentation/eligibility result;
- derived financial requirement satisfied/not satisfied.

Raw payment proof, bank reconciliation, refund bank data, and internal Finance notes are denied by default.

Confidential academic-review detail is limited to what the user's separately assigned academic/editorial role permits; operational publication processing primarily consumes the decision/eligibility result.

## Final publication snapshot

Before/at handoff, preserve a stable snapshot of publication metadata.

Later global-profile edits must not silently rewrite it.

Non-substantive metadata corrections may use controlled publication correction.

Substantive changes, including:
- author addition/removal/reordering;
- major title changes;
- substantive manuscript replacement;
require the appropriate academic/historical correction authority.

## Manual/assisted OJS handoff

V1 does not require OJS API integration.

Authorized Publication Team may:
- prepare/export metadata;
- transfer/upload files manually;
- record publication destination;
- record external OJS/reference ID;
- record transferred_at;
- record transferred_by;
- track downstream status.

## External publication references

Examples:
- OJS reference/submission ID;
- DOI;
- publication URL;
- ISBN/ISSN reference;
- proceedings/volume/issue reference;
- publication date.

These are external references and never internal primary keys.

## Publication status integrity

Status reflects known downstream fact.

```text
READY_FOR_TRANSFER
≠
TRANSFERRED
≠
IN_PUBLICATION_PROCESS
≠
PUBLISHED
```

Queue-clearing convenience is not a valid reason to falsify a downstream publication state.

Status-change audit may capture:
- actor;
- timestamp;
- previous/new status;
- external reference;
- note/evidence where applicable.

## Downstream failure / withdrawal

Publication failure/withdrawal history is preserved.

It does not erase legitimate:
- presentation history;
- participant history;
- presenter certificate history;
- academic history.

## Publication Team + Author

A Publication Team member may perform ordinary downstream processing on an already-eligible own paper where policy permits.

They may not:
- force PUBLICATION_APPROVED;
- bypass PUBLICATION_ELIGIBLE;
- bypass required review/revision;
- rewrite academic decision history through publication operations.

## Record preservation

Publication records are not freely hard-deleted.

Corrections to DOI/URL/external references/status use controlled audited correction.

A historical metadata correction after external publication does not imply the external OJS/proceedings item has automatically changed.

## Future role separation

V1 may combine publication responsibilities.

Architecture must permit later separation such as:
- Publication Metadata Staff;
- OJS Operator;
- Proceeding Editor;
- Publication Manager.

## Summary matrix

| Action | Publication Team | Academic Decision Authority | Finance | Author |
|---|---|---|---|---|
| View publication queue | Allow | Need-based | Deny | Own status only |
| Set PUBLICATION_APPROVED | Deny unless separate academic role | Allow | Deny | Deny |
| Evaluate PUBLICATION_ELIGIBLE | Gate/authorized policy process | Academic input | Derived finance input | Deny |
| View final publication metadata | Allow | Need-based | Deny | Own |
| View raw Finance evidence | Deny default | Deny | Allow | Own proof only |
| Prepare OJS package | Allow | Deny default | Deny | Deny |
| Record transfer | Allow | Deny default | Deny | Deny |
| Record DOI/URL | Allow | Deny default | Deny | View own |
| Mark PUBLISHED | Allow with factual downstream basis | Deny default | Deny | Deny |
| Bypass own-paper gates | Deny | COI rules apply | Deny | Deny |
| Controlled publication correction | Allow by authority | Need-based | Deny | Request only |

## Next

Part 8 defines Certificate & Archive permissions.
