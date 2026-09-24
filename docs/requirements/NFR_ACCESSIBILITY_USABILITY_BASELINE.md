# Non-Functional Requirements — Part 7 Accessibility, Usability & Responsive Experience

**ID:** ICP-REQ-NFR-001-P7  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Objective

Ensure that the public site and authenticated conference workspaces are usable, understandable, responsive, and accessible for participants, authors, reviewers, event staff, and administrators across the required languages and devices.

## Accessibility target

The V1 target is:

```text
WCAG 2.2 Level AA
```

for public and authenticated product surfaces.

Compliance is demonstrated through implementation and UAT, not merely by declaration.

## Critical flows

Accessibility must be verified particularly for:
- registration;
- email verification;
- joining an edition;
- abstract/submission workflow;
- payment-proof upload;
- revision response;
- Full Paper/final manuscript upload;
- presenter confirmation;
- reviewer form;
- payment verification;
- academic decision;
- check-in/presentation verification;
- certificate verification.

## Device posture

### Participant / Author

```text
MOBILE-FIRST
```

Ordinary participant/author critical workflows must not require desktop.

### Back office

```text
DESKTOP-FIRST
+
RESPONSIVE
```

Dense administrative workflows may optimize for desktop/laptop while remaining functional at narrower viewport sizes.

### Event Operations

Event-day workflows must be usable on tablet/mobile because staff may work while moving between rooms/check-in/session operations.

## Responsive layout

Core content must adapt to narrow screens without unnecessary permanent horizontal scrolling.

Complex tables may use responsive alternatives such as cards/lists or controlled table scrolling when semantically appropriate.

Important content/actions must remain available.

## Keyboard operability

All core interactive workflows must be keyboard-operable.

Examples:
- navigation;
- forms;
- dialogs;
- tabs;
- dropdown/menu controls;
- file-upload controls;
- pagination;
- review forms;
- table/list actions.

No core workflow may depend on mouse-only interaction.

## Focus

Keyboard focus must be clearly visible.

Interactive components must not create keyboard traps.

Focus management for dialogs/dynamic content must remain understandable and recoverable.

## Semantics

Use semantic/accessible component behavior for:
- heading hierarchy;
- landmarks;
- form labels;
- buttons;
- links;
- tables and headers;
- dialogs;
- status regions;
- icon-only controls.

Icon-only actions require accessible names that communicate the action in context.

## Dynamic feedback

Important dynamic status such as:
- upload completed/failed;
- save completed;
- submission completed;
- review submitted;
- validation failed;
- background job finished;

must be exposed through accessible semantic feedback, not only visual animation/color.

## Color / contrast

Color is never the sole carrier of important meaning.

Accepted/rejected/action-required states combine understandable text and/or icons with color.

Text, controls, focus indicators, errors, and meaningful UI states target WCAG 2.2 AA contrast requirements.

## Touch targets

Touch controls meet WCAG 2.2 AA target-size requirements.

Primary mobile actions should use comfortably usable sizing/spacing rather than merely targeting the minimum wherever practical.

## Forms

Forms require:
- persistent labels;
- explicit required/optional state;
- clear instructions where necessary;
- field-associated validation errors;
- error summary/focus strategy where useful;
- retention of already entered data after validation failure whenever technically feasible.

Placeholder text is not a substitute for a persistent label.

## Long-form work protection

Long-form work should not disappear easily.

Applicable workflows use explicit draft save, autosave, recovery, or an appropriate combination.

Examples:
- submission metadata;
- abstract;
- revision response;
- reviewer form;
- publication metadata.

Exact autosave strategy is feature-specific.

## High-impact actions

High-impact actions use proportional confirmation/context.

Examples:
- withdraw submission;
- reject paper;
- execute/verify refund;
- revoke certificate;
- archive/unarchive edition;
- historical correction.

Confirmation should explain the consequence rather than only say “Are you sure?”.

Ordinary low-risk actions should not be burdened by unnecessary confirmation dialogs.

## Workflow language

Internal enums do not need to be shown verbatim.

User-facing UI should clearly answer:

```text
Where am I?
What is my current status?
What should I do next?
```

Statuses and next actions use understandable human language.

## Date, time, timezone

Deadlines and schedules use explicit absolute date/time.

Timezone must be shown wherever ambiguity may occur.

Relative wording such as “tomorrow” may supplement but not replace critical absolute deadline information.

Internal timestamp rules remain governed by NFR Part 5.

## Loading and success states

Slow/state-changing actions display truthful progress/loading state.

Where repeated action could create duplication, controls should prevent accidental duplicate interaction while processing.

Authoritative success is shown only after server-confirmed durable/business completion.

## Zoom / reflow

The application must support browser zoom/text enlargement and responsive reflow without loss of critical content/functionality.

Browser zoom must not be disabled.

## Reduced motion

Animation is optional enhancement, not required to understand or complete workflows.

Where relevant, interfaces respect user reduced-motion preference.

## Language consistency

Accessibility/usability applies equally to:
- Indonesian;
- English;
- Arabic / RTL.

Arabic is not a reduced-quality or desktop-only experience.

Localization/RTL mechanics are defined in NFR Part 8.

## Accessibility UAT

Representative acceptance testing includes:
- mobile phone;
- tablet;
- desktop/laptop;
- keyboard-only navigation;
- visible focus;
- zoom/reflow;
- semantic/screen-reader sanity checks;
- important status/error feedback;
- Arabic RTL.

Exact browser/device matrix is deferred to later compatibility specification.

## Deferred details

Phase 0 does not lock:
- UI component library;
- frontend framework;
- automated accessibility-testing product;
- exact device/browser list;
- exact autosave interval;
- exact design tokens;
- exact responsive breakpoints.

## Next

Part 8 defines Localization, Internationalization & Arabic RTL Quality.
