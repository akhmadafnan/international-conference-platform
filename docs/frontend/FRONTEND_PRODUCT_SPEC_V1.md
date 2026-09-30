# ICHES Frontend Product Specification v1

**Document:** Frontend Product & UX Specification  
**Status:** READY FOR PARALLEL FRONTEND PROTOTYPING  
**Audience:** Frontend developer / AI frontend agent  
**Updated:** 2026-09-30  
**Scope:** Public website + authenticated participant/presenter/reviewer-facing experience. Internal admin backoffice is out of scope for this document.

---

## 1. Purpose

Build a polished, multilingual, mobile-first frontend for ICHES that can be developed in parallel without changing core business rules or backend contracts.

The frontend must feel like **one product**, not:
- a marketing website plus a separate admin-looking portal;
- a collection of unrelated forms;
- an OJS clone;
- a generic dashboard template.

Core UX principle:

> The user should always know where they are, what they need to do next, and what documents/information are currently available.

---

## 2. Product Character

Visual direction:
- modern institutional;
- international academic;
- clean and editorial;
- confident but not corporate-heavy;
- strong whitespace;
- clear hierarchy;
- mobile-first;
- accessible;
- Arabic RTL first-class.

Avoid:
- dashboard-template look on the public site;
- excessive gradients;
- overuse of cards;
- decorative animations that slow down content;
- generic blue SaaS aesthetic;
- hardcoded content tied to one edition.

The frontend should support theme tokens so branding can evolve without rewriting layouts.

---

## 3. Frontend Architecture Boundary

The frontend developer may work independently using mock data.

Do not change:
- business state definitions;
- payment rules;
- review policy;
- publication rules;
- certificate eligibility;
- backend authorization assumptions;
- final database schema.

Recommended parallel workflow:

```text
develop
└── feat/frontend-experience-v1
    ├── public website shell
    ├── participant dashboard shell
    ├── reusable components
    ├── responsive layouts
    ├── i18n/RTL shell
    └── mock data adapters
```

Frontend implementation should expose clear data placeholders and component contracts rather than inventing backend behavior.

---

## 4. Global Information Architecture

### Public
- Home
- About
- Call for Papers
- Program
- Speakers
- Activities
- Publication
- Registration & Fees
- Venue
- Downloads
- FAQ
- News
- Contact
- Past Editions
- Verify
- Login
- Register

### Authenticated user workspace
- Overview
- My Activities
- My Submission — only when relevant
- My Reviews — only when relevant
- Payment
- Schedule
- My Documents
- Help
- Profile / Account
- Notifications

The authenticated menu is **state-aware**. Hide irrelevant items.

---

## 5. Global Layout

### Desktop Header

```text
[ICHES Logo]

Home
About
Program
Call for Papers
Speakers
Publication
Information ▼

[Language]
[Login]
[Register]
```

Information dropdown:
- Important Dates
- Registration & Fees
- Venue
- Downloads
- FAQ
- Contact

When authenticated:
- replace Login/Register with Dashboard/Profile access.

### Mobile Header
- Logo
- language selector
- menu button
- sticky/high-visibility primary CTA where useful

### Global Footer
Sections:
- ICHES identity
- quick links
- conference information
- contact
- social links
- legal/privacy links if available
- edition switch / past editions
- copyright

---

# 6. PUBLIC PAGE SPECIFICATIONS

## 6.1 Home

### Goal
Explain the event immediately and drive registration/submission.

### Sections
1. Hero
   - edition label
   - conference title
   - theme
   - date
   - venue
   - primary CTA: Register
   - secondary CTA: Call for Papers / Submit Abstract
   - conference countdown

2. Quick Facts
   - date
   - venue
   - format
   - key deadline

3. About ICHES
   - short narrative
   - link to About

4. Participation Options
   - package cards
   - included activities/facilities
   - fee
   - CTA

5. Important Dates
   - chronological timeline
   - highlight nearest deadline

6. Call for Papers / Tracks
   - theme
   - track cards
   - submission languages

7. Speakers
   - selected speaker cards
   - link to full Speakers page

8. Program Highlights
   - key sessions / event blocks
   - not full paper schedule unless published

9. Publication Opportunities
   - Proceedings
   - Selected Journals
   - honest wording

10. Registration Fees
   - compact matrix
   - link to full Registration & Fees

11. Venue
   - photo
   - venue name
   - address
   - map CTA

12. Partners / Organizers / Host
   - logos with clear hierarchy

13. News / Announcements
   - latest 3–6 items

14. Final CTA
   - Register
   - Call for Papers

### Post-event state
When edition is completed, replace conversion-heavy hero actions with:
- Conference Completed
- View Proceedings
- View Highlights
- View Awardees
- Verify Certificate

---

## 6.2 About

### Goal
Explain conference series and current edition.

### Sections
- About the ICHES series
- Current edition overview
- theme and rationale
- objectives
- organizer / host / partners
- conference history
- link to past editions

Do not hardcode UNISYA as permanent series owner.

---

## 6.3 Call for Papers

### Sections
- page hero
- conference theme
- tracks / sub-themes
- who can submit
- submission languages
- abstract requirements
- important dates
- review model summary
- publication opportunities
- author guidelines
- template downloads
- FAQ subset
- CTA: Submit Abstract

If unauthenticated:
Submit Abstract → Register/Login.

If authenticated but unpaid/unconfirmed:
Submit Abstract → redirect to required registration/payment action.

---

## 6.4 Program

### Before detailed schedule is published
Show:
- program overview
- keynote blocks
- panel sessions
- evening/MoU
- community service
- closing

### After schedule publication
Show:
- date tabs
- session grouping
- room
- time
- moderator
- paper title
- presenter
- optional filters: track / room

Do not expose unpublished draft scheduling.

---

## 6.5 Speakers

### Speaker card
- photo
- name
- title
- institution
- country
- speaker type
- short topic/title

### Speaker detail page/modal
- biography
- session
- topic
- optional external profile links

Speaker type is configurable; do not assume keynote only.

---

## 6.6 Activities

Landing page with activity cards:
- Conference
- Evening / MoU
- International Community Service

Each activity detail page may include:
- purpose
- date/time
- venue
- eligibility/package inclusion
- highlights
- operational notes

Community Service may include:
- category descriptions
- education
- economy/MSME
- social/culture

Do not turn this into a mini-KKN management interface.

---

## 6.7 Publication

### Sections
- publication pathway explanation
- Proceedings
- Selected Journals
- metadata/readiness statement
- publication ethics / requirements
- important publication notes
- partner journal cards if confirmed

Important wording:
- Selected for Journal != Accepted by Journal
- publication acceptance belongs to the journal unless formally authorized otherwise

---

## 6.8 Registration & Fees

### Sections
- who can register
- package cards
- package comparison matrix
- price
- inclusions
- payment method summary
- registration flow
- refund/cancellation policy
- FAQ
- CTA Register

For V1, make package cards the primary UX.

---

## 6.9 Important Dates

Page/section:
- registration open/close
- abstract deadline
- review result
- full article deadline
- slides deadline if used
- conference date
- revision deadline
- publication milestones if public

All dates must come from edition data.

---

## 6.10 Venue

### Sections
- venue hero/photo
- venue name
- full address
- map/embed placeholder
- directions
- facilities
- accessibility information
- optional accommodation notes

Do not build hotel booking.

---

## 6.11 Downloads

Categories:
- Author Guidelines
- Abstract Template
- Full Article Template
- Presentation Slides Template
- Guidebook
- Published Schedule
- Other official documents

Each item:
- title
- language
- version/date
- file type
- current/superseded state

---

## 6.12 FAQ

Search/filter optional.

Categories:
- Registration
- Payment
- Submission
- Review
- Publication
- Venue
- Accommodation
- Certificates

Use accordion pattern.

---

## 6.13 News

### Listing
- featured article
- cards/list
- category
- date
- pagination

### Detail
- title
- date
- hero image optional
- body
- related posts

Use for:
- announcements
- deadline extensions
- speaker announcements
- schedule release
- award announcements
- post-event reports

---

## 6.14 Contact

Sections:
- Front Office
- email
- WhatsApp CTA
- office address
- social media
- contact notes

WhatsApp is convenience only, not source of truth.

---

## 6.15 Past Editions

### Listing
Edition cards:
- year
- theme
- date
- host
- hero image
- links

### Edition detail
May include:
- overview
- program
- speakers
- proceedings
- awards
- news/gallery
- certificates/document verification as appropriate

---

## 6.16 Verify

One public verification shell.

Possible verified document types:
- Certificate
- Presentation LoA
- other generated official document where applicable

### Verification result
Show only minimum necessary data:
- valid / revoked / superseded
- document type
- document number
- recipient name
- edition
- relevant activity/award
- issue date

Never expose:
- email
- phone
- payment data
- internal notes

---

# 7. AUTHENTICATED PARTICIPANT EXPERIENCE

## 7.1 Dashboard / Overview

This is the most important authenticated page.

### Above the fold
- greeting
- edition
- registration summary
- Next Action card
- Event Pass / Show QR quick action
- urgent deadline if applicable

### Contextual blocks
- My Paper
- My Presentation
- My Activities
- Payment
- Documents
- latest notifications

Do not show meaningless analytics.

### Example states

Payment due:
```text
NEXT ACTION
Complete your payment
[View Payment Instructions]
```

Waiting verification:
```text
Payment proof received
No action required
```

Abstract accepted:
```text
Accepted for Presentation
[Download LoA]

NEXT ACTION
Upload Full Article
```

Waiting schedule:
```text
Full Article submitted
Your presentation schedule will appear once published.
```

Revision required:
```text
ACTION REQUIRED
Article Revision Required
[Upload Revised Article]
```

---

## 7.2 My Activities

Activity cards/rows:
- activity name
- registered
- date/time
- location
- attendance status
- assignment details where applicable

Community Service may show:
- preferred category
- assigned group
- coordinator
- location

---

## 7.3 Payment

### Before payment
- amount
- bank/payment instructions
- deadline
- proof upload form

### After proof
- status
- submitted metadata
- current proof
- correction message if required

### Correction
- reason
- replace proof
- old proof history hidden behind secondary detail if needed

Statuses must be translated into human language.

---

## 7.4 Event Pass

Standalone/mobile-friendly pass:
- participant name
- Registration ID
- edition
- package/attendance eligibility summary
- QR
- status

Do not label user Presenter before academic/presentation lifecycle establishes it.

---

## 7.5 My Submission

### Submission list
If V1 allows one or more submissions, list each with:
- Paper ID
- title
- current status
- next action

### Submission detail
Tabs/sections:
- Overview
- Metadata
- Contributors
- References
- Files
- Review / Decision
- Presentation
- Publication

Keep default view simple.

---

## 7.6 Abstract Submission Wizard

### Step 1 — Details
- submission language
- track
- title
- subtitle optional
- abstract
- keywords

### Step 2 — Contributors
Per contributor:
- given name
- family/surname or single name
- email
- country
- affiliation
- department/faculty optional
- ORCID optional
- corresponding author
- order/reorder

### Step 3 — References
- ordered entries
- raw citation
- DOI optional
- add/remove/reorder

### Step 4 — Files
- abstract file if required
- template guidance
- upload state

### Step 5 — Review & Submit
- validation summary
- missing required fields
- declaration/confirmation
- submit

After official submit, editing rules become state-dependent.

---

## 7.7 Submission Status / Review

Possible participant-facing states:
- Draft
- Submitted
- Administrative Check
- Correction Required
- Under Review
- Revision Required
- Revision Submitted
- Accepted for Presentation
- Not Accepted for Presentation

Reviewer identity is never shown to author.

Internal technical enums must not be displayed directly.

---

## 7.8 LoA

Document card:
- document number
- issued date
- paper title
- status
- View/Download
- verification link/QR

LoA page must not show room/session assignment.

---

## 7.9 Full Article

When accepted:
- template download
- deadline
- file upload
- version history
- current manuscript state

After upload:
- status
- readiness for scheduling
- presenter confirmation

---

## 7.10 Presenter Confirmation

Simple selector from contributor list.

Fields:
- actual presenter
- optional contact confirmation if policy requires

Before schedule freeze:
- author may request/change presenter as allowed

After schedule freeze:
- show locked/controlled state and contact admin path

---

## 7.11 Schedule

Participant-facing schedule:
- personal schedule first
- full schedule link
- session
- room
- time
- moderator
- own presentation visually emphasized

On event day:
- timeline mode is preferred on mobile.

---

## 7.12 My Presentation

Show:
- paper title
- presenter
- date/time
- room
- moderator
- presentation status
- slides upload if required
- post-presentation assessment/revision state

Possible states:
- Scheduled
- Presented
- No Show
- Assessment in Progress
- No Revision Required
- Revision Required
- Final Academic Approval

---

## 7.13 Revision

Only visible if required.

Show:
- recommendation
- reviewer notes
- deadline
- current article version
- upload revised article
- version history

No revision button if no revision is required.

---

## 7.14 Publication

Show:
- final approval
- readiness
- destination
- production/handoff status
- publication record when available

Proceedings:
- In Production
- Proof Available if used
- Published
- DOI / URL / pages/article number if available

Selected Journal:
- Selected for Journal
- Handed Off
- external reference/status if known

Do not render “Accepted by Journal” unless that fact is actually known.

---

## 7.15 My Documents

Document hub.

Categories:
- Event Pass
- Presentation LoA
- Templates
- Guidebook
- Schedule
- Participant Certificate
- Presenter Certificate
- Reviewer/Moderator Certificate if applicable
- Award Certificate if applicable
- Publication document if applicable

Each document card:
- title
- status
- date
- current version
- View / Download / Verify

---

## 7.16 Notifications

Simple chronological feed:
- title
- short message
- timestamp
- unread state
- action link

Examples:
- payment verified
- correction requested
- abstract accepted
- LoA available
- schedule published
- revision required
- certificate issued

---

## 7.17 Help

Sections:
- FAQ
- Front Office
- WhatsApp
- email
- Registration ID
- quick-copy support context

No full helpdesk/ticket system in V1.

---

## 7.18 Profile / Account

Personal:
- name
- email
- phone if required
- country

Scholarly:
- affiliation
- department/faculty
- ORCID optional

Account:
- language
- password/security actions

Profile edits must not silently rewrite protected historical publication/certificate snapshots.

---

# 8. REVIEWER-FACING EXPERIENCE

Only appears when user has active reviewer assignments.

## My Reviews

Two sections:
- Abstract Reviews
- Presentation Sessions

### Abstract review card
- Paper ID
- title
- track
- due date
- status
- Review button

### Abstract review detail
- allowed author metadata according to single-anonymous model
- abstract
- submission metadata
- review form
- recommendation
- comments
- Submit Review

### Presentation session
- assigned session
- room
- schedule
- assigned papers
- Full Article access
- assessment action

Reviewer must not see unrelated papers.

---

# 9. SHARED COMPONENT LIBRARY

Frontend AI should create reusable components instead of page-specific duplication.

Core components:
- AppHeader
- PublicHeader
- AuthHeader
- Footer
- LanguageSwitcher
- EditionBadge
- Countdown
- HeroSection
- SectionHeading
- CTAGroup
- StatusBadge
- NextActionCard
- Timeline
- DeadlineList
- PackageCard
- SpeakerCard
- TrackCard
- ActivityCard
- NewsCard
- DocumentCard
- UploadField
- FileVersionList
- PersonCard
- ContributorEditor
- ReferenceEditor
- DataTable
- EmptyState
- Alert / Notice
- VerificationResult
- EventPassCard
- QRDisplay
- ScheduleTimeline
- SessionCard
- ReviewFormShell
- CertificateCard
- NotificationItem

Prefer a small coherent component system over many one-off variants.

---

# 10. STATUS LANGUAGE

Never expose backend enum labels directly.

Examples:

`PROOF_SUBMITTED`
→ “Payment proof received”

`UNDER_REVIEW`
→ “Your abstract is under review”

`READY_FOR_SCHEDULING`
→ “Waiting for presentation schedule”

`REVISION_REQUIRED`
→ “Action required: revise your article”

`PUBLICATION_APPROVED`
→ “Final academic approval complete”

Human wording must be localizable.

---

# 11. MULTILINGUAL & RTL

Mandatory locales:
- Indonesian
- English
- Arabic

Requirements:
- no hardcoded UI strings;
- language switcher persistent;
- Arabic sets document direction to RTL;
- component spacing/icon alignment must work in RTL;
- form labels and validation messages localizable;
- dates/numbers should support locale formatting;
- UI language must not alter scholarly submission language.

Do not duplicate pages per language.

---

# 12. RESPONSIVE PRIORITY

Primary breakpoints:
- mobile
- tablet
- desktop

Mobile priority flows:
- registration
- payment proof
- dashboard
- Event Pass QR
- submission status
- schedule
- document access

On event day, mobile must be excellent.

Avoid horizontal tables on mobile when card/list transformations are more readable.

---

# 13. ACCESSIBILITY

Minimum expectations:
- keyboard navigation;
- visible focus;
- semantic headings;
- labels for form controls;
- error summary and field errors;
- sufficient contrast;
- alt text;
- responsive text;
- no information conveyed by color alone;
- reduced-motion friendly.

---

# 14. FRONTEND DATA CONTRACT PRINCIPLE

Until backend contracts are frozen, use typed mock models.

Example conceptual objects:
- ConferenceSeries
- ConferenceEdition
- ParticipationPackage
- ImportantDate
- Speaker
- Track
- Activity
- UserProfile
- Registration
- Payment
- Submission
- Contributor
- Reference
- SubmissionFile
- ReviewSummary
- LoADocument
- Session
- Room
- PresentationSlot
- Attendance
- PresentationAssessment
- PublicationRecord
- Award
- Certificate
- Notification

Do not infer database tables from these names.

---

# 15. MOCK STATE SCENARIOS REQUIRED

The frontend prototype must support switchable mock scenarios:

1. Visitor
2. Registered but unpaid
3. Payment waiting verification
4. Registration confirmed, no submission
5. Abstract draft
6. Abstract under review
7. Abstract revision required
8. Abstract rejected but still participant
9. Abstract accepted / LoA available
10. Full Article required
11. Waiting for schedule
12. Schedule published
13. Presentation completed / no revision
14. Revision required
15. Ready for Production
16. Proceedings production
17. Selected Journal handoff
18. Certificates issued
19. Reviewer with assignments
20. Multi-role user: Participant + Presenter + Reviewer

This is critical for validating state-aware UX before backend integration.

---

# 16. FRONTEND ROUTE MAP — PROVISIONAL

Public:
```text
/{locale}
/{locale}/about
/{locale}/call-for-papers
/{locale}/program
/{locale}/speakers
/{locale}/activities
/{locale}/activities/{slug}
/{locale}/publication
/{locale}/registration
/{locale}/important-dates
/{locale}/venue
/{locale}/downloads
/{locale}/faq
/{locale}/news
/{locale}/news/{slug}
/{locale}/contact
/{locale}/editions
/{locale}/editions/{edition}
/{locale}/verify/{token?}
/login
/register
```

Authenticated:
```text
/dashboard
/dashboard/activities
/dashboard/payment
/dashboard/event-pass
/dashboard/submissions
/dashboard/submissions/{id}
/dashboard/submissions/{id}/edit
/dashboard/submissions/{id}/loa
/dashboard/submissions/{id}/full-article
/dashboard/submissions/{id}/presentation
/dashboard/submissions/{id}/revision
/dashboard/submissions/{id}/publication
/dashboard/reviews
/dashboard/reviews/{assignment}
/dashboard/schedule
/dashboard/documents
/dashboard/notifications
/dashboard/help
/dashboard/profile
```

Exact Laravel route names are not frozen by this document.

---

# 17. V1 FRONTEND ACCEPTANCE CHECKLIST

The frontend shell is ready for backend integration when:

- public pages are responsive;
- id/en/ar shells work;
- Arabic RTL works;
- homepage has pre-event and post-event states;
- registration/package UX is clear;
- dashboard supports Next Action;
- Event Pass is mobile-friendly;
- abstract wizard exists;
- state-specific submission UI exists;
- LoA page exists;
- Full Article/presenter confirmation exists;
- schedule/presentation UI exists;
- revision UI exists;
- publication state UI exists;
- documents center exists;
- reviewer workspace exists;
- all 20 mock scenarios can be demonstrated;
- no page requires invented backend business rules;
- no internal technical enum is displayed to end users.

---

# 18. Explicit Out-of-Scope for Frontend Prototype

Do not build:
- admin backoffice;
- Finance operational queue;
- Academic admin review-assignment UI;
- schedule builder admin;
- certificate designer admin;
- publication admin queue;
- actual API integrations;
- payment gateway;
- OJS API;
- Crossref API;
- ROR live API dependency;
- WhatsApp automation;
- real QR verification backend.

Use presentation-ready mock states only for these boundaries.

---

# 19. Handoff Expectation

The frontend developer/AI should return:

- route/page inventory;
- component inventory;
- responsive implementation;
- i18n/RTL support;
- mock-data layer;
- screenshots or browser walkthrough;
- known gaps;
- no backend business-rule changes.

Frontend work must remain mergeable into the main product without forcing backend redesign.
