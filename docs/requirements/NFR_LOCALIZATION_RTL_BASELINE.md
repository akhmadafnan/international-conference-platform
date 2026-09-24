# Non-Functional Requirements — Part 8 Localization, Internationalization & Arabic RTL Quality

**ID:** ICP-REQ-NFR-001-P8  
**Status:** PRODUCT OWNER APPROVED BASELINE  
**Approved:** 2026-09-24

## Objective

Make Indonesian, English, and Arabic genuine first-class product experiences and ensure Arabic RTL, mixed-script scholarly metadata, generated documents, notifications, and CMS content work without corrupting authoritative data or identity.

## Mandatory V1 locales

```text
id — Indonesian
en — English
ar — Arabic
```

The required locales apply to public and authenticated product surfaces where the workflow is available.

A critical feature must not be complete in one locale and materially incomplete in another without an explicit controlled fallback.

## Localization resources

Translatable application/UI strings come from localization resources/catalogs rather than scattered hardcoded strings.

This supports:
- translation QA;
- completeness checks;
- consistent wording;
- future locale expansion.

## Locale fallback

Deterministic fallback:

```text
REQUESTED LOCALE
→ ENGLISH
→ CONTROLLED MISSING-TRANSLATION HANDLING
```

Users should not see raw translation keys under normal operation.

Fallback does not make missing translations acceptable indefinitely; missing keys must remain detectable.

## Translation completeness gate

Critical UI strings for id/en/ar are a production release/UAT gate.

Critical scope includes:
- authentication/registration;
- submission;
- payment;
- review;
- presentation/event operations;
- publication;
- certificate;
- validation/error/confirmation/status messaging.

## Arabic RTL

Arabic uses true RTL document/layout direction.

RTL review includes, as applicable:
- global shell/sidebar/navigation;
- breadcrumbs;
- forms;
- stepper/wizard;
- cards;
- dialogs/modals;
- tables/list actions;
- pagination;
- upload interfaces;
- mobile navigation;
- status/workflow surfaces.

RTL is not implemented merely by right-aligning text.

## Directional semantics

Direction-aware controls/icons such as Back/Next, chevrons, breadcrumbs, or directional navigation may mirror in RTL.

Non-directional icons such as search, download, calendar, QR, or generic file icons are not blindly mirrored.

## Mixed-direction / BiDi content

Arabic UI must safely render and interact with mixed-direction content such as:
- email addresses;
- URLs;
- DOI;
- ORCID;
- ROR/external IDs;
- submission/reference codes;
- certificate numbers;
- Latin organization names;
- numeric values.

Identifiers preserve their semantic order regardless of surrounding RTL layout.

## UI locale vs scholarly language

```text
UI LOCALE
≠
SCHOLARLY CONTENT LANGUAGE
```

Changing UI language never silently translates or mutates:
- paper title;
- abstract;
- keywords;
- contributor names;
- publication metadata.

Paper/content language is independent metadata.

## Multilingual scholarly metadata

The architecture may support multiple-language title/abstract/keyword metadata where an edition/publisher needs it.

V1 does not globally require every author to provide id/en/ar versions of scholarly metadata.

## CMS / edition content

Public/edition content should be locale-aware/translatable, including:
- About Conference;
- Important Dates;
- Guidelines;
- Payment Instructions;
- Venue;
- FAQ;
- Announcements;
- other CMS-managed public content.

Translation availability must be explicit.

Fallback is controlled; one source-language entry is not silently treated as a completed translation for all locales.

## Dates / time / numbers

Presentation formatting is locale-aware while canonical stored values remain unchanged.

Timezone remains explicit where ambiguity is possible.

```text
LOCALE FORMATTING
≠
AUTHORITATIVE VALUE CHANGE
```

## Currency

```text
UI LOCALE
≠
CURRENCY
```

Currency is determined by edition/business policy.

Selecting Arabic does not imply SAR; selecting English does not imply USD.

## Timezone

```text
UI LOCALE
≠
TIMEZONE
```

Timezone comes from edition/user context.

## Validation / status / errors

Critical validation messages, status labels, confirmation copy, and user-facing errors support the active locale.

A localized form should not fall back unpredictably to untranslated error text.

## Notifications

Email/system notifications should use the recipient/preferred locale where available.

A deterministic fallback is required.

Notification language preference is independent from the authoritative business state.

## Generated-document language

```text
ADMIN UI LANGUAGE
≠
DOCUMENT LANGUAGE
```

Generated documents such as:
- LoA;
- Certificate;
- Decision Letter;
- other configured conference documents;

use an explicit template/document-language context.

An administrator using Indonesian UI may intentionally generate an English certificate/letter.

## Generated-document provenance

Important generated documents preserve language plus template/version/snapshot context in accordance with NFR Part 6.

## Unicode safety

Text storage/search must support full Unicode.

The product must not assume Latin/ASCII-only names, organizations, titles, abstracts, or content.

Exact database collation/index implementation is deferred.

## International personal names

Identity/display-name design must accommodate Indonesian, Arabic, and international naming patterns.

The platform must not require Western-style First/Middle/Last fields as the only valid representation when that would corrupt names.

Feature specifications may store structured name parts where needed, but a truthful display/citation representation must be supported.

## Transliteration

The platform must not silently auto-transliterate personal or scholarly identity.

If alternate-script/transliterated forms are used, they come from:
- the user;
- an authoritative scholarly source;
- an explicitly controlled workflow.

## Search/filter

Search/filter remains usable for:
- Arabic script;
- Latin script;
- DOI;
- email;
- ORCID;
- submission codes;
- certificate/reference identifiers;

including when the surrounding interface is RTL.

## Translation-length resilience

UI layouts/components must tolerate string-length variation across id/en/ar.

Fixed-width design assumptions must not cause translated labels/actions to clip or become unusable.

## Locale switching

Changing locale should preserve the current authorized resource/page context where practical.

Example:

```text
Submission #015 / Review Status / EN
→ switch to AR
→ same Submission #015 / Review Status / AR
```

Locale switching never mutates business data.

## Locale preference

Authenticated users may persist a preferred UI locale.

Public users retain explicit locale-switch control.

Browser locale may be used as a hint, but must not remove user control.

## Missing-translation observability

Missing translation keys/coverage gaps must be detectable through:
- QA;
- test/build checks where practical;
- safe production observability/logging.

Do not depend solely on participant complaints.

## RTL-specific UAT

Arabic receives dedicated UAT for:
- navigation;
- forms;
- tables;
- modals;
- pagination;
- steppers;
- file upload;
- cards/dashboard;
- mixed-direction scholarly metadata;
- mobile layout;
- applicable generated documents.

```text
LTR PASS
≠
RTL PASS
```

## Accessibility + RTL

NFR Part 7 remains fully applicable to Arabic.

RTL UAT includes accessibility concerns such as:
- keyboard navigation;
- visible focus;
- semantic labels/roles;
- field errors;
- screen-reader sanity;
- zoom/reflow;
- mobile usability.

Arabic is not a reduced-quality accessibility experience.

## Deferred implementation details

Phase 0 does not lock:
- i18n framework/package;
- translation management system;
- database collation;
- exact date/number formatting library;
- exact browser-locale detection strategy;
- machine-translation provider;
- transliteration service;
- exact multilingual metadata schema.

## Related Phase 0 closure

This baseline also satisfies the product-level baseline for:
- REQ-L10N-001 Localization Policy;
- REQ-L10N-002 Arabic RTL Policy.

Detailed implementation remains Phase 1+.

## Next

Part 9 defines Integration Resilience, Notifications & External Service Boundaries.
