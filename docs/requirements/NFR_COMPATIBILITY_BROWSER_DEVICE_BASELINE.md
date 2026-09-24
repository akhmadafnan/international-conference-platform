# Non-Functional Requirements — Part 11 Compatibility, Browser & Device Support

**ID:** ICP-REQ-NFR-001-P11  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Objective

Define a practical modern-web compatibility baseline so participants and operators can complete critical workflows on realistic devices without forcing the product to carry obsolete-browser complexity.

## Official desktop browser policy

At release time, official desktop support targets:

```text
Chrome  — latest 2 stable major versions
Edge    — latest 2 stable major versions
Firefox — latest 2 stable major versions
Safari  — latest 2 stable major versions
```

A relative version policy prevents the requirement from becoming obsolete every time a browser releases.

## Mobile browser targets

First-class participant mobile targets include:
- Mobile Safari on supported recent iOS/iPadOS;
- Chrome on supported recent Android.

Representative testing should include realistic mid-range mobile hardware rather than only flagship devices.

## Unsupported legacy browsers

Internet Explorer and browsers no longer receiving appropriate security support are not official targets.

Unsupported browsers should receive clear guidance rather than silently presenting a broken critical UI where detection is practical.

Browser detection itself is not a security boundary.

## Standards-first implementation

Prefer modern interoperable web standards over browser-specific behavior/hacks.

Necessary compatibility workarounds should be:
- scoped;
- documented where material;
- regression-tested.

## Progressive enhancement

Where practical, advanced browser features have functional fallbacks.

Examples:
- drag/drop upload → native file picker;
- camera QR scanner → manual code/reference entry or authorized search;
- rich enhancement unavailable → core workflow remains usable.

## Supported device classes

Supported classes include:
- smartphone;
- tablet;
- laptop;
- desktop.

Role emphasis:
- Participant/Author: smartphone-first capable;
- Event Operations: smartphone/tablet operationally usable;
- Finance/Academic/Publication/Admin: optimized for laptop/desktop while responsive.

## Mid-range hardware

Ordinary participant/author workflows must not assume flagship devices, high-memory laptops, or unusually powerful hardware.

Performance goals from NFR Part 4 remain applicable.

## Slow / unstable network

Critical workflows must represent network uncertainty truthfully.

If upload/request completion is not confirmed:
- do not display authoritative success;
- provide clear retry/recovery guidance;
- preserve draft/context where feasible.

V1 is network-aware but does not require a full offline PWA/application.

Conference-day outage continuity is handled by NFR Part 3 continuity/reconciliation controls.

## Input-mode independence

Essential actions must not depend exclusively on:
- hover;
- right-click;
- drag/drop;
- precision mouse movement;
- external/physical keyboard.

Touch, keyboard, and pointer interaction should be supported according to workflow/device context.

## File picker

Native file selection is always available for upload workflows.

Drag/drop may be an enhancement but never the only upload path.

## Camera / QR fallback

Camera scanning may improve:
- gate/check-in;
- certificate/reference lookup;
- event operations.

If camera access is unavailable/denied:
- manual code/reference entry; and/or
- authorized participant/resource search

must provide a proportionate operational fallback.

## QR/barcode trust boundary

```text
SCANNED PAYLOAD
→ LOOKUP / REFERENCE
→ SERVER-SIDE VALIDATION
→ AUTHORIZED ACTION
```

A QR/barcode payload alone does not create PRESENTED, PAID, certificate validity, or other authoritative business state.

## PDF behavior

Browser PDF viewers vary.

Important protected PDF/document workflows must support authorized download regardless of whether embedded view works.

Examples:
- LoA;
- Certificate;
- guidelines;
- manuscript;
- Decision Letter.

## Browser navigation safety

Refresh, back, forward, retry, duplicate click, and reopening a tab must not duplicate authoritative business effects.

Server-side idempotency/state validation from earlier NFRs remains authoritative.

## Multiple / stale tabs

Users may have multiple tabs open.

Perfect live cross-tab synchronization is not required, but stale tabs must not bypass:
- current authorization;
- assignment revocation;
- lifecycle state;
- optimistic/state-conflict protection.

Mutations are revalidated against current server state.

## Session expiration

Session expiration during long-form work should cooperate with:
- draft/autosave/recovery;
- controlled re-authentication;
- return to the still-authorized context where feasible.

Avoid unnecessary loss of substantial user-entered work.

## Browser autofill / native controls

Authentication/profile forms should not intentionally defeat secure browser password managers/autofill.

Use semantic/autocomplete behavior where appropriate.

Native browser date/file controls may differ visually; business validation remains server-side.

## Timezone compatibility

Edition deadlines/schedules retain their intended meaning even when participant/device timezone differs.

Compatibility testing includes scenarios where:

```text
EDITION TIMEZONE ≠ DEVICE TIMEZONE
```

Absolute time/timezone display requirements from NFR Part 7 remain applicable.

## Locale / RTL combinations

Compatibility QA must include representative combinations such as:
- Android + Indonesian;
- iPhone + English;
- desktop modern browser + Arabic RTL;
- tablet + Arabic RTL;
- mixed-direction scholarly identifiers.

## Accessibility combinations

Compatibility testing also intersects with accessibility:
- keyboard + desktop browser;
- zoom/reflow;
- touch + mobile;
- semantic/screen-reader sanity;
- RTL + mobile.

A browser/device pass is not sufficient if accessibility/RTL behavior is broken.

## Embedded in-app browsers

Links may be opened from WhatsApp, Gmail, social apps, or embedded webviews.

These receive best-effort support rather than full official compatibility guarantees.

Where an embedded browser cannot safely support the flow, provide clear guidance to open the same link/context in a supported browser.

## Cookie / storage restrictions

If browser privacy/storage settings prevent required authentication/session state, the product should give usable guidance rather than an opaque 500/error loop.

Security controls are not weakened to accommodate an unsupported storage configuration.

## Device permissions

Ask for device permissions only when needed.

Example:
- camera permission when the user starts scan functionality.

Do not request unnecessary permissions on ordinary page load.

## No mandatory native app

V1 is a web platform.

Participation must not require installation of:
- Android native app;
- iOS native app;
- PWA.

Native/PWA packaging may be considered later only if justified.

## Browser & Device Support Matrix

Before production, maintain a canonical support artifact such as:

```text
BROWSER_DEVICE_SUPPORT_MATRIX.md
```

It should document:
- browser family/support rule;
- desktop/mobile scope;
- device class;
- critical flows verified;
- locale/RTL/accessibility combinations;
- known limitations;
- last verification/release.

## Deferred details

Phase 0 does not lock:
- exact physical device models;
- exact OS minimum versions;
- browser automation service;
- device-lab provider;
- PWA framework;
- QR/camera library;
- exact compatibility-test tool.

## Next

Part 12 defines Maintainability, Testability & Operational Support, followed by the full NFR Parts 1–12 consistency audit.
